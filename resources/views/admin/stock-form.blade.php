@extends('admin.layout')

@php
    $isEdit = $stock !== null;
    $sizeKeys = \App\Models\UniformStock::SIZE_KEYS;
    $hasOld = old('item_type') !== null;

    $existingSizes = $isEdit && is_array($stock->sizes) ? $stock->sizes : [];
    $existingPrices = $isEdit && is_array($stock->prices) ? $stock->prices : [];
    $carried = $isEdit ? array_keys($stock->carriedSizes()) : [];

    if ($hasOld) {
        $initialSizes = (array) old('sizes_available', []);
    } elseif ($isEdit && ($stock->item_type === 'uniforms')) {
        $initialSizes = $carried;
    } else {
        $initialSizes = ['S', 'M', 'L', 'XL'];
    }

    $pricesState = [];
    $quantitiesState = [];
    foreach ($sizeKeys as $sizeKey) {
        $inputKey = strtolower($sizeKey);
        $pricesState[$sizeKey] = (float) old('price_' . $inputKey, $existingPrices[$sizeKey] ?? 0);
        $quantitiesState[$sizeKey] = (int) old('qty_' . $inputKey, $existingSizes[$sizeKey] ?? 0);
    }

    // Prefer "same price for all sizes" when every carried size shares one price.
    $carriedPrices = array_values(array_unique(array_map(
        fn ($sizeKey) => (float) ($existingPrices[$sizeKey] ?? 0),
        $carried
    )));
    $derivedMode = (! $isEdit || count($carriedPrices) <= 1) ? 'single' : 'per_size';
    $derivedSingle = count($carriedPrices) === 1 ? $carriedPrices[0] : 0;

    $formState = [
        'isEdit' => $isEdit,
        'type' => (string) old('item_type', $stock->item_type ?? ''),
        'sizesAvailable' => array_values($initialSizes),
        'priceMode' => (string) old('price_mode', $derivedMode),
        'uniformPrice' => (float) old('uniform_price', $derivedSingle),
        'prices' => $pricesState,
        'quantities' => $quantitiesState,
        'bookPrice' => (float) old('book_price', $stock->unit_price ?? 0),
        'bookQty' => (int) old('quantity', $stock->quantity ?? 0),
        'currentImages' => $isEdit
            ? $stock->images->map(fn ($img) => ['id' => $img->id, 'url' => asset('storage/' . $img->path)])->values()->all()
            : [],
        'sizeKeys' => $sizeKeys,
    ];

    $isVisibleChecked = $hasOld ? (bool) old('is_visible') : ($isEdit ? (bool) $stock->is_visible : true);
@endphp

@section('title', $isEdit ? 'Edit Stock Item' : 'Add Stock Item')

@section('extra-css')
<style>
    [x-cloak] { display: none !important; }

    .sform-back {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 13px;
        font-weight: 700;
        color: var(--muted);
        text-decoration: none;
        margin-bottom: 14px;
        transition: color 0.12s ease;
    }
    .sform-back:hover { color: var(--pine); }
    .sform-back svg { width: 14px; height: 14px; }

    .sform-grid {
        display: grid;
        grid-template-columns: minmax(0, 5fr) minmax(0, 7fr);
        gap: 20px;
        align-items: start;
    }
    @media (max-width: 1100px) {
        .sform-grid { grid-template-columns: 1fr; }
    }

    .sform-card-body { padding: 20px 22px; }

    .sform-field { margin-bottom: 18px; }
    .sform-field:last-child { margin-bottom: 0; }
    .sform-field > label {
        display: block;
        font-size: 13px;
        font-weight: 700;
        color: var(--ink);
        margin-bottom: 7px;
    }
    .sform-field input[type="text"],
    .sform-field input[type="number"],
    .sform-field select {
        width: 100%;
        border: 1px solid var(--line-strong);
        border-radius: 6px;
        font-family: inherit;
        font-size: 14px;
        color: var(--ink);
        background: var(--field, var(--card));
        height: 42px;
        padding: 0 14px;
        box-sizing: border-box;
    }
    .sform-field select {
        cursor: pointer;
        appearance: none;
        -webkit-appearance: none;
        background-image: url("data:image/svg+xml;charset=utf-8,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%23687180' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='m6 9 6 6 6-6'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 12px center;
        padding-right: 34px;
    }
    .sform-field input:focus,
    .sform-field select:focus {
        outline: none;
        border-color: var(--pine);
        box-shadow: 0 0 0 3px rgba(10, 92, 47, 0.10);
    }
    .sform-help {
        margin-top: 6px;
        font-size: 12px;
        color: var(--muted);
        line-height: 1.45;
    }
    .sform-error {
        margin-top: 6px;
        font-size: 12.5px;
        font-weight: 600;
        color: var(--danger, #b91c1c);
    }

    /* ---------- Size chips ---------- */
    .size-chip-row { display: flex; flex-wrap: wrap; gap: 8px; }
    .size-chip {
        position: relative;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 46px;
        padding: 7px 12px;
        border: 1px solid var(--line-strong);
        border-radius: 8px;
        background: var(--field, var(--card));
        font-family: var(--font-mono);
        font-size: 12.5px;
        font-weight: 700;
        color: var(--muted);
        cursor: pointer;
        user-select: none;
        transition: background-color 0.12s ease, color 0.12s ease, border-color 0.12s ease;
    }
    .size-chip:hover { border-color: var(--pine); color: var(--ink); }
    .size-chip.is-on {
        background: var(--pine-soft);
        border-color: var(--pine);
        color: var(--pine);
    }
    .size-chip input { position: absolute; opacity: 0; pointer-events: none; }

    /* ---------- Price mode toggle ---------- */
    .price-mode-toggle {
        display: inline-flex;
        border: 1px solid var(--line-strong);
        border-radius: 8px;
        overflow: hidden;
        background: var(--field, var(--card));
    }
    .price-mode-toggle label {
        padding: 8px 14px;
        font-size: 12.5px;
        font-weight: 700;
        color: var(--muted);
        cursor: pointer;
        transition: background-color 0.12s ease, color 0.12s ease;
    }
    .price-mode-toggle label.is-on {
        background: var(--pine);
        color: #fff;
    }
    .price-mode-toggle input { position: absolute; opacity: 0; pointer-events: none; }

    /* ---------- Per-size rows ---------- */
    .size-rows-head,
    .size-row {
        display: grid;
        grid-template-columns: 64px minmax(0, 1fr) minmax(0, 1fr);
        gap: 10px;
        align-items: center;
    }
    .size-rows-head.no-price,
    .size-row.no-price {
        grid-template-columns: 64px minmax(0, 1fr);
    }
    .size-rows-head {
        margin-bottom: 8px;
        font-size: 11px;
        font-family: var(--font-mono);
        font-weight: 600;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: var(--muted);
    }
    .size-row { margin-bottom: 8px; }
    .size-row .size-row-label {
        font-family: var(--font-mono);
        font-size: 12.5px;
        font-weight: 700;
        color: var(--ink);
    }
    .size-row input {
        width: 100%;
        border: 1px solid var(--line-strong);
        border-radius: 6px;
        font-family: inherit;
        font-size: 14px;
        color: var(--ink);
        background: var(--field, var(--card));
        height: 38px;
        padding: 0 12px;
        box-sizing: border-box;
    }
    .size-row input:focus {
        outline: none;
        border-color: var(--pine);
        box-shadow: 0 0 0 3px rgba(10, 92, 47, 0.10);
    }

    .size-rows-empty {
        padding: 18px;
        border: 1px dashed var(--line-strong);
        border-radius: 8px;
        text-align: center;
        font-size: 13px;
        color: var(--muted);
    }

    /* ---------- Photo manager ---------- */
    .photo-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(96px, 1fr));
        gap: 10px;
    }
    .photo-tile {
        position: relative;
        border: 1px solid var(--line-strong);
        border-radius: 10px;
        overflow: hidden;
        background: var(--hover);
        aspect-ratio: 1;
    }
    .photo-tile img { width: 100%; height: 100%; object-fit: cover; display: block; transition: opacity 0.15s ease; }
    .photo-tile.marked { border-color: #dc2626; }
    .photo-tile.marked img { opacity: 0.35; }
    .photo-tile-btn {
        position: absolute;
        left: 50%;
        bottom: 6px;
        transform: translateX(-50%);
        border: 1px solid var(--line-strong);
        border-radius: 5px;
        background: rgba(255, 255, 255, 0.92);
        color: #dc2626;
        font-size: 10.5px;
        font-weight: 700;
        padding: 3px 8px;
        cursor: pointer;
        white-space: nowrap;
    }
    html[data-theme="dark"] .photo-tile-btn { background: rgba(20, 25, 32, 0.9); color: #F0A0A0; }
    .photo-add {
        border: 2px dashed var(--line-strong);
        border-radius: 10px;
        background: var(--hover);
        aspect-ratio: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        font-size: 12.5px;
        font-weight: 700;
        color: var(--pine);
        cursor: pointer;
        transition: border-color 0.15s ease;
    }
    .photo-add:hover { border-color: var(--pine); }
    .photo-counter { font-weight: 700; color: var(--faint); margin-left: 6px; }
    .photo-counter.over { color: #dc2626; }

    .sform-visible-row {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        color: var(--ink);
        font-weight: 500;
        cursor: pointer;
    }
    .sform-visible-row input[type="checkbox"] { width: 16px; height: 16px; cursor: pointer; }

    /* ---------- Sticky action bar ---------- */
    .sform-actions {
        position: sticky;
        bottom: 14px;
        z-index: 30;
        margin-top: 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        flex-wrap: wrap;
        padding: 14px 20px;
        border-radius: 12px;
        background: var(--card);
        border: 1px solid var(--line);
        box-shadow: var(--shadow-pop, 0 8px 24px rgba(0,0,0,0.12));
    }
    .sform-actions-total {
        font-size: 13px;
        color: var(--muted);
    }
    .sform-actions-total strong {
        color: var(--ink);
        font-variant-numeric: tabular-nums;
    }
    .sform-actions-buttons { display: flex; gap: 10px; }

    /* ---------- Movement history ---------- */
    .mv-list { display: flex; flex-direction: column; }
    .mv-row {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 11px 0;
        border-bottom: 1px solid var(--line);
        font-size: 13px;
    }
    .mv-row:last-child { border-bottom: none; }
    .mv-badge {
        flex-shrink: 0;
        display: inline-flex;
        align-items: center;
        padding: 2px 9px;
        border-radius: 999px;
        font-family: var(--font-mono);
        font-size: 10.5px;
        font-weight: 700;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        border: 1px solid transparent;
    }
    .mv-badge.type-initial { background: var(--pine-soft); color: var(--pine); }
    .mv-badge.type-restock { background: var(--pine-soft); color: var(--pine); }
    .mv-badge.type-sale { background: var(--hover-2); color: var(--muted); }
    .mv-badge.type-edit { background: var(--hover-2); color: var(--muted); }
    .mv-badge.type-correction { background: #FEF3E2; color: #92400E; }
    html[data-theme="dark"] .mv-badge.type-correction { background: rgba(227, 164, 72, 0.14); color: #E9C288; }
    .mv-main { flex: 1; min-width: 0; color: var(--ink); }
    .mv-meta { font-size: 12px; color: var(--muted); white-space: nowrap; }
    .mv-delta { font-family: var(--font-mono); font-weight: 700; font-variant-numeric: tabular-nums; }
    .mv-delta.is-plus { color: var(--pine); }
    .mv-delta.is-minus { color: var(--danger, #b91c1c); }
</style>
@endsection

@section('content')
<a href="{{ route('admin.stocks') }}" class="sform-back">
    <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5"/>
    </svg>
    Back to Stocks
</a>

<form
    id="stockForm"
    method="POST"
    action="{{ $isEdit ? route('admin.stocks.update', $stock) : route('admin.stocks.store') }}"
    enctype="multipart/form-data"
    x-data="stockForm(@js($formState))"
>
    @csrf
    @if ($isEdit)
        @method('PATCH')
    @endif

    <div class="sform-grid">
        {{-- ---------- Left: item details ---------- --}}
        <section class="card">
            <div class="card-header">
                <strong style="font-size:15px;color: var(--ink);">Item Details</strong>
            </div>
            <div class="sform-card-body">
                <div class="sform-field">
                    <label for="item_type">Item Type</label>
                    <select id="item_type" name="item_type" :value="type" @change="onTypeChange($event)" required>
                        <option value="" disabled {{ old('item_type', $stock->item_type ?? '') === '' ? 'selected' : '' }}>-- Select Type --</option>
                        <option value="uniforms" {{ old('item_type', $stock->item_type ?? '') === 'uniforms' ? 'selected' : '' }}>Uniforms</option>
                        <option value="books" {{ old('item_type', $stock->item_type ?? '') === 'books' ? 'selected' : '' }}>Books</option>
                    </select>
                    @error('item_type')<div class="sform-error">{{ $message }}</div>@enderror
                </div>

                <div class="sform-field">
                    <label for="item_name">Item Name</label>
                    <input type="text" id="item_name" name="item_name" maxlength="100"
                           value="{{ old('item_name', $stock->item_name ?? '') }}" required>
                    @error('item_name')<div class="sform-error">{{ $message }}</div>@enderror
                </div>

                <div class="sform-field">
                    <label for="low_stock_threshold">Low Stock Alert Threshold</label>
                    <input type="number" id="low_stock_threshold" name="low_stock_threshold" min="0" max="10000"
                           value="{{ old('low_stock_threshold', $stock->low_stock_threshold ?? 10) }}" required>
                    <p class="sform-help">Alerts trigger when a size (or the total, for books) falls to this count or below.</p>
                    @error('low_stock_threshold')<div class="sform-error">{{ $message }}</div>@enderror
                </div>

                <div class="sform-field">
                    <label>Item Photos <span class="photo-counter" :class="{ over: photoOver }" x-text="photoTotal + ' / 5'"></span></label>
                    <div class="photo-grid">
                        <template x-for="img in currentImages" :key="'c' + img.id">
                            <div class="photo-tile" :class="{ marked: removeIds.includes(img.id) }">
                                <img :src="img.url" alt="Item photo">
                                <button type="button" class="photo-tile-btn" @click="toggleRemove(img.id)" x-text="removeIds.includes(img.id) ? 'Undo' : 'Remove'"></button>
                            </div>
                        </template>
                        <template x-for="(src, i) in newPreviews" :key="'n' + i">
                            <div class="photo-tile">
                                <img :src="src" alt="New photo preview">
                                <button type="button" class="photo-tile-btn" @click="removeNew(i)">Remove</button>
                            </div>
                        </template>
                        <button type="button" class="photo-add" x-show="photoTotal < 5" @click="$refs.imageInput.click()">+ Add photo</button>
                    </div>
                    <input type="file" x-ref="imageInput" name="images[]" multiple accept=".jpg,.jpeg,.png,.webp" style="display:none;" @change="addImages($refs.imageInput)">
                    <template x-for="id in removeIds" :key="'r' + id">
                        <input type="hidden" name="remove_images[]" :value="id">
                    </template>
                    <p class="sform-help">Optional, up to 5 photos (JPG, PNG, or WebP — max 2 MB each). The first photo is the cover shown on listings.</p>
                    @error('images')<div class="sform-error">{{ $message }}</div>@enderror
                    @error('images.*')<div class="sform-error">{{ $message }}</div>@enderror
                </div>

                <div class="sform-field">
                    <label class="sform-visible-row">
                        <input type="checkbox" name="is_visible" value="1" {{ $isVisibleChecked ? 'checked' : '' }}>
                        Show on public products page
                    </label>
                </div>
            </div>
        </section>

        {{-- ---------- Right: pricing & stock ---------- --}}
        <section class="card">
            <div class="card-header">
                <strong style="font-size:15px;color: var(--ink);">Pricing &amp; Stock</strong>
            </div>
            <div class="sform-card-body">
                <div x-show="type === ''" x-cloak class="size-rows-empty">
                    Select an item type first to set prices and stock.
                </div>

                {{-- Uniforms --}}
                <div x-show="type === 'uniforms'" x-cloak>
                    <div class="sform-field">
                        <label>Sizes This Item Comes In</label>
                        <div class="size-chip-row">
                            @foreach ($sizeKeys as $sizeKey)
                                <label class="size-chip" :class="{ 'is-on': sizesAvailable.includes('{{ $sizeKey }}') }">
                                    <input type="checkbox" name="sizes_available[]" value="{{ $sizeKey }}"
                                           x-model="sizesAvailable" :disabled="type !== 'uniforms'">
                                    {{ $sizeKey }}
                                </label>
                            @endforeach
                        </div>
                        @error('sizes_available')<div class="sform-error">{{ $message }}</div>@enderror
                    </div>

                    <div class="sform-field">
                        <label>Pricing</label>
                        <div class="price-mode-toggle" role="radiogroup">
                            <label :class="{ 'is-on': priceMode === 'single' }">
                                <input type="radio" name="price_mode" value="single" x-model="priceMode" :disabled="type !== 'uniforms'">
                                Same price for all sizes
                            </label>
                            <label :class="{ 'is-on': priceMode === 'per_size' }">
                                <input type="radio" name="price_mode" value="per_size" x-model="priceMode" :disabled="type !== 'uniforms'">
                                Price per size
                            </label>
                        </div>
                    </div>

                    <div class="sform-field" x-show="priceMode === 'single'" x-cloak>
                        <label for="uniform_price">Price (₱)</label>
                        <input type="number" id="uniform_price" name="uniform_price" min="0" step="0.01"
                               x-model.number="uniformPrice" :disabled="type !== 'uniforms' || priceMode !== 'single'">
                        @error('uniform_price')<div class="sform-error">{{ $message }}</div>@enderror
                    </div>

                    <div class="sform-field">
                        <label>Stock per Size</label>

                        <div x-show="sizesAvailable.length === 0" x-cloak class="size-rows-empty">
                            Pick at least one size above to enter stock.
                        </div>

                        <div x-show="sizesAvailable.length > 0" x-cloak>
                            <div class="size-rows-head" :class="{ 'no-price': priceMode === 'single' }">
                                <span>Size</span>
                                <span x-show="priceMode === 'per_size'">Price (₱)</span>
                                <span>Stock</span>
                            </div>
                            @foreach ($sizeKeys as $sizeKey)
                                <div class="size-row" :class="{ 'no-price': priceMode === 'single' }"
                                     x-show="sizesAvailable.includes('{{ $sizeKey }}')" x-cloak>
                                    <span class="size-row-label">{{ $sizeKey }}</span>
                                    <input type="number" name="price_{{ strtolower($sizeKey) }}" min="0" step="0.01"
                                           x-show="priceMode === 'per_size'"
                                           x-model.number="prices['{{ $sizeKey }}']"
                                           :disabled="type !== 'uniforms' || priceMode !== 'per_size' || !sizesAvailable.includes('{{ $sizeKey }}')">
                                    <input type="number" name="qty_{{ strtolower($sizeKey) }}" min="0"
                                           x-model.number="quantities['{{ $sizeKey }}']"
                                           :disabled="type !== 'uniforms' || !sizesAvailable.includes('{{ $sizeKey }}')">
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Books --}}
                <div x-show="type === 'books'" x-cloak>
                    <div class="sform-field">
                        <label for="book_price">Price (₱)</label>
                        <input type="number" id="book_price" name="book_price" min="0" step="0.01"
                               x-model.number="bookPrice" :disabled="type !== 'books'">
                        <p class="sform-help">Selling price per book. Auto-fills at checkout and can be adjusted per sale.</p>
                        @error('book_price')<div class="sform-error">{{ $message }}</div>@enderror
                    </div>

                    <div class="sform-field">
                        <label for="quantity">Quantity</label>
                        <input type="number" id="quantity" name="quantity" min="0" max="100000"
                               x-model.number="bookQty" :disabled="type !== 'books'">
                        @error('quantity')<div class="sform-error">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>
        </section>
    </div>

    <div class="sform-actions">
        <div class="sform-actions-total">
            <template x-if="type === 'uniforms'">
                <span>Total stock: <strong x-text="totalQuantity.toLocaleString()"></strong>
                    across <strong x-text="sizesAvailable.length"></strong> <span x-text="sizesAvailable.length === 1 ? 'size' : 'sizes'"></span></span>
            </template>
            <template x-if="type === 'books'">
                <span>Total stock: <strong x-text="(Number(bookQty) || 0).toLocaleString()"></strong> copies</span>
            </template>
            <template x-if="type === ''">
                <span>Choose an item type to begin.</span>
            </template>
        </div>
        <div class="sform-actions-buttons">
            <a href="{{ route('admin.stocks') }}" class="btn btn-outline btn-sm">Cancel</a>
            <button type="submit" class="btn btn-green btn-sm">{{ $isEdit ? 'Save Changes' : 'Add Item' }}</button>
        </div>
    </div>
</form>

@if ($isEdit && $movements->isNotEmpty())
    <section class="card" style="margin-top:20px;">
        <div class="card-header">
            <strong style="font-size:15px;color: var(--ink);">Recent Stock Movements</strong>
            <div style="font-size:12.5px;color: var(--muted);margin-top:3px;">Last {{ $movements->count() }} changes to this item</div>
        </div>
        <div class="sform-card-body">
            <div class="mv-list">
                @foreach ($movements as $movement)
                    <div class="mv-row">
                        <span class="mv-badge type-{{ $movement->type }}">{{ $movement->type }}</span>
                        <span class="mv-main">
                            <span class="mv-delta {{ $movement->quantity_change >= 0 ? 'is-plus' : 'is-minus' }}">
                                {{ $movement->quantity_change >= 0 ? '+' : '' }}{{ number_format($movement->quantity_change) }}
                            </span>
                            @if ($movement->size) <span style="font-family: var(--font-mono);font-size:12px;">{{ $movement->size }}</span> @endif
                            → {{ number_format($movement->quantity_after) }} left
                            @if ($movement->note) <span style="color: var(--muted);">· {{ $movement->note }}</span> @endif
                        </span>
                        <span class="mv-meta">
                            {{ $movement->user?->name ?? 'System' }} · {{ $movement->created_at->format('M j, g:i A') }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endif
@endsection

@section('scripts')
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<script>
    function stockForm(state) {
        return {
            type: state.type,
            sizesAvailable: state.sizesAvailable,
            priceMode: state.priceMode,
            uniformPrice: state.uniformPrice,
            prices: state.prices,
            quantities: state.quantities,
            bookPrice: state.bookPrice,
            bookQty: state.bookQty,
            currentImages: state.currentImages,
            removeIds: [],
            newPreviews: [],
            dt: new DataTransfer(),

            get totalQuantity() {
                return this.sizesAvailable.reduce((sum, size) => sum + (Number(this.quantities[size]) || 0), 0);
            },
            get keptCount() {
                return this.currentImages.length - this.removeIds.length;
            },
            get photoTotal() {
                return this.keptCount + this.newPreviews.length;
            },
            get photoOver() {
                return this.photoTotal > 5;
            },

            hasPricingData() {
                if (this.type === 'uniforms') {
                    return this.totalQuantity > 0
                        || (Number(this.uniformPrice) || 0) > 0
                        || Object.values(this.prices).some((p) => (Number(p) || 0) > 0);
                }
                if (this.type === 'books') {
                    return (Number(this.bookPrice) || 0) > 0 || (Number(this.bookQty) || 0) > 0;
                }
                return false;
            },
            onTypeChange(event) {
                const next = event.target.value;
                if (this.type && next !== this.type && this.hasPricingData()) {
                    if (!confirm('Switching the item type clears the pricing and stock entries. Continue?')) {
                        event.target.value = this.type;
                        return;
                    }
                    this.resetPricing();
                }
                this.type = next;
            },
            resetPricing() {
                this.uniformPrice = 0;
                this.bookPrice = 0;
                this.bookQty = 0;
                state.sizeKeys.forEach((size) => {
                    this.prices[size] = 0;
                    this.quantities[size] = 0;
                });
            },

            toggleRemove(id) {
                const idx = this.removeIds.indexOf(id);
                if (idx === -1) {
                    this.removeIds.push(id);
                } else {
                    this.removeIds.splice(idx, 1);
                }
            },
            addImages(input) {
                for (const file of Array.from(input.files || [])) {
                    if (this.keptCount + this.dt.items.length >= 5) { break; }
                    this.dt.items.add(file);
                    const slot = this.newPreviews.length;
                    this.newPreviews.push('');
                    const reader = new FileReader();
                    reader.onload = (e) => { this.newPreviews[slot] = e.target.result; };
                    reader.readAsDataURL(file);
                }
                input.files = this.dt.files;
            },
            removeNew(index) {
                this.dt.items.remove(index);
                this.newPreviews.splice(index, 1);
                this.$refs.imageInput.files = this.dt.files;
            },
        };
    }

    // Warn before leaving with unsaved edits (a misclick should never lose work).
    (function () {
        const form = document.getElementById('stockForm');
        if (!form) return;

        let dirty = false;
        let submitting = false;

        form.addEventListener('input', () => { dirty = true; });
        form.addEventListener('change', () => { dirty = true; });
        form.addEventListener('submit', () => { submitting = true; });

        window.addEventListener('beforeunload', (event) => {
            if (dirty && !submitting) {
                event.preventDefault();
                event.returnValue = '';
            }
        });
    })();
</script>
@endsection
