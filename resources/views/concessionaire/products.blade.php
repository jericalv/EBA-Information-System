@extends('concessionaire.layout')

@section('title', 'Products')

@section('extra-css')
<style>
    .products-page {
        width: 100%;
        display: flex;
        flex-direction: column;
        gap: 16px;
    }
    .products-action-row {
        display: flex;
        justify-content: flex-end;
        align-items: center;
    }
    .products-toolbar {
        background: var(--card);
        border: 1px solid var(--line);
        border-radius: 12px;
        padding: 14px;
        box-shadow: var(--shadow-card);
    }
    .products-instant-search {
        position: relative;
        flex: 1;
        min-width: 0;
    }
    .products-instant-search input {
        width: 100%;
        padding-left: 38px;
    }
    .products-instant-search svg {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        width: 16px;
        height: 16px;
        color: var(--faint);
        pointer-events: none;
    }
    .error-box {
        border: 1px solid #F2D8D8;
        background: #FDF3F3;
        color: var(--danger);
        border-radius: 8px;
        padding: 12px 16px;
        font-size: 13.5px;
    }
    html[data-theme="dark"] .error-box {
        border-color: rgba(227, 106, 106, 0.35);
        background: rgba(227, 106, 106, 0.12);
        color: #F0A0A0;
    }
    .filters-bar {
        display: flex;
        gap: 12px;
        align-items: center;
    }
    .filter-select {
        min-width: 170px;
        width: auto;
        flex-shrink: 0;
    }
    .products-results {
        background: var(--card);
        border: 1px solid var(--line);
        border-radius: 12px;
        padding: 18px;
        box-shadow: var(--shadow-card);
    }
    .products-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 18px;
    }
    .product-card {
        border: 1px solid var(--line);
        border-radius: 10px;
        background: var(--card);
        overflow: hidden;
        display: flex;
        flex-direction: column;
        min-height: 100%;
        transition: border-color 0.15s ease;
    }
    .product-card:hover {
        border-color: var(--line-strong);
    }
    .product-media {
        position: relative;
        width: 100%;
        padding-top: 78%;
        background: var(--paper);
        overflow: hidden;
    }
    .product-media img {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .product-fallback {
        position: absolute;
        inset: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #94a3b8;
    }
    .product-fallback svg {
        width: 56px;
        height: 56px;
    }
    .product-body {
        padding: 14px 14px 12px;
        display: flex;
        flex-direction: column;
        flex: 1;
        gap: 10px;
    }
    .product-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
    }
    .product-name {
        margin: 0;
        font-size: 15px;
        line-height: 1.35;
        font-weight: 700;
        color: var(--ink);
        letter-spacing: -0.01em;
        word-break: break-word;
    }
    .product-desc {
        margin: 0;
        color: var(--muted);
        font-size: 12.5px;
        line-height: 1.45;
        display: -webkit-box;
        line-clamp: 2;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        min-height: 34px;
    }
    .product-chip-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
    }
    .product-chip {
        display: inline-flex;
        align-items: center;
        padding: 3px 9px;
        border-radius: 5px;
        font-family: var(--font-mono);
        font-size: 10px;
        font-weight: 500;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        border: 1px solid var(--line);
        color: var(--muted);
        background: var(--paper);
    }
    .product-bottom {
        margin-top: auto;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding-top: 2px;
    }
    .product-price {
        margin: 0;
        font-family: var(--font-mono);
        font-size: 14px;
        line-height: 1.2;
        font-weight: 600;
        color: var(--ink);
        font-variant-numeric: tabular-nums;
    }
    .toggle-btn {
        border: 0;
        padding: 5px 10px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 600;
        font-family: inherit;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: background-color 0.15s ease, color 0.15s ease;
    }
    .toggle-btn::before {
        content: '';
        width: 6px;
        height: 6px;
        border-radius: 999px;
        background: currentColor;
    }
    .toggle-btn.available {
        color: #14532D;
        background: #EAF3ED;
    }
    html[data-theme="dark"] .toggle-btn.available {
        color: var(--green-dark);
        background: var(--pine-soft);
    }
    .toggle-btn.unavailable {
        color: var(--muted);
        background: var(--paper);
    }
    .product-links {
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    .product-links form {
        margin: 0;
    }
    .edit-current-image {
        margin-top: 4px;
        border: 1px solid var(--line-strong);
        border-radius: 8px;
        background: var(--paper);
        overflow: hidden;
        width: 100%;
        max-width: 240px;
        aspect-ratio: 16 / 10;
    }
    .edit-current-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .edit-image-note {
        margin: 8px 2px 0;
        font-size: 12px;
        color: var(--muted);
    }
    .is-hidden {
        display: none !important;
    }
    .pagination-wrap {
        margin-top: 22px;
        display: flex;
        justify-content: center;
    }
    .pagination-wrap nav {
        display: flex;
        flex-wrap: wrap;
        gap: 4px;
        align-items: center;
    }
    .pagination-wrap a,
    .pagination-wrap span {
        padding: 8px 13px;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 500;
        text-decoration: none;
        color: var(--muted);
        background: var(--card);
        border: 1px solid var(--line);
        transition: border-color 0.15s, color 0.15s;
        display: inline-block;
    }
    .pagination-wrap a:hover {
        border-color: var(--pine);
        color: var(--pine);
    }
    .pagination-wrap span[aria-current="page"] {
        background: var(--pine);
        border-color: var(--pine);
        color: #fff;
    }
    html[data-theme="dark"] .pagination-wrap span[aria-current="page"] { color: #0C130F; }
    .pagination-wrap span.cursor-default {
        color: var(--line-strong);
        background: var(--paper);
    }
    .empty-state {
        margin-top: 0;
        border: 1px dashed var(--line-strong);
        border-radius: 12px;
        background: var(--card);
        text-align: center;
        padding: 56px 20px;
    }
    .empty-state svg {
        width: 56px;
        height: 56px;
        color: var(--line-strong);
        margin-bottom: 12px;
    }
    .empty-state h3 {
        margin: 0;
        color: var(--ink);
        font-size: 20px;
        font-weight: 700;
        letter-spacing: -0.01em;
        line-height: 1.2;
    }
    .empty-state p {
        margin: 8px 0 18px;
        color: var(--muted);
        font-size: 13.5px;
    }
    .modal-wrap {
        position: fixed;
        inset: 0;
        z-index: 3000;
        background: rgba(23, 37, 28, 0.5);
        display: none;
        align-items: center;
        justify-content: center;
        padding: 16px;
    }
    .modal-wrap.show {
        display: flex;
    }
    .modal-card {
        width: 100%;
        max-width: 720px;
        background: var(--card);
        border-radius: 12px;
        border: 1px solid var(--line);
        padding: 22px;
        box-shadow: var(--shadow-pop);
        max-height: calc(100vh - 32px);
        overflow-y: auto;
    }
    .modal-card-sm {
        max-width: 440px;
    }
    .modal-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
    }
    .modal-head h3 {
        margin: 0;
        color: var(--ink);
        font-size: 18px;
        font-weight: 700;
        letter-spacing: -0.01em;
    }
    .modal-close {
        width: 32px;
        height: 32px;
        border: 0;
        border-radius: 6px;
        background: var(--paper);
        color: var(--muted);
        font-size: 18px;
        line-height: 1;
        cursor: pointer;
        transition: background-color 0.15s ease, color 0.15s ease;
    }
    .modal-close:hover {
        background: var(--pine-soft);
        color: var(--ink);
    }
    .modal-sub {
        margin: 6px 0 0;
        color: var(--muted);
        font-size: 13px;
    }
    .form-grid {
        margin-top: 16px;
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 14px;
    }
    .field {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }
    .field.span-2 {
        grid-column: span 2;
    }
    .field label {
        font-family: var(--font-mono);
        font-size: 11px;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: var(--muted);
    }
    .field input,
    .field textarea,
    .field select {
        border: 1px solid var(--line-strong);
        border-radius: 6px;
        padding: 9px 12px;
        font-family: var(--font-ui);
        font-size: 13.5px;
        color: var(--ink);
        background: var(--field);
        transition: border-color 0.15s ease, box-shadow 0.15s ease;
    }
    .field input:focus,
    .field textarea:focus,
    .field select:focus {
        outline: none;
        border-color: var(--pine);
        box-shadow: 0 0 0 3px rgba(10, 92, 47, 0.12);
    }
    html[data-theme="dark"] .field input:focus,
    html[data-theme="dark"] .field textarea:focus,
    html[data-theme="dark"] .field select:focus {
        box-shadow: 0 0 0 3px rgba(123, 211, 160, 0.18);
    }
    .modal-card .field input::placeholder,
    .modal-card .field textarea::placeholder {
        color: var(--faint);
    }
    .field textarea {
        min-height: 98px;
        resize: vertical;
    }
    .upload-zone {
        position: relative;
        border: 1px dashed var(--line-strong);
        border-radius: 8px;
        background: var(--paper);
        min-height: 122px;
        padding: 18px 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        cursor: pointer;
        transition: border-color 0.15s ease, background-color 0.15s ease;
    }
    .upload-zone:hover {
        background: var(--pine-soft);
        border-color: var(--pine);
    }
    .upload-zone svg {
        width: 22px;
        height: 22px;
        color: var(--muted);
        margin: 0 auto 10px;
        display: block;
    }
    .upload-zone-title {
        display: block;
        font-family: var(--font-ui);
        font-size: 13.5px;
        font-weight: 600;
        text-transform: none;
        letter-spacing: 0;
        color: var(--ink);
    }
    .upload-zone-subtext {
        display: block;
        margin-top: 4px;
        font-family: var(--font-ui);
        font-size: 12px;
        font-weight: 500;
        text-transform: none;
        letter-spacing: 0;
        color: var(--muted);
    }
    .upload-input {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        opacity: 0;
        cursor: pointer;
    }
    .upload-file-name {
        margin: 8px 2px 0;
        font-size: 12px;
        color: var(--muted);
    }
    .checkbox-line {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: var(--ink);
        font-family: var(--font-ui);
        font-size: 13.5px;
        font-weight: 500;
        text-transform: none;
        letter-spacing: 0;
        cursor: pointer;
    }
    .checkbox-line input[type="checkbox"] {
        accent-color: var(--pine);
        width: 15px;
        height: 15px;
    }
    .modal-actions {
        margin-top: 18px;
        display: flex;
        justify-content: flex-end;
        gap: 10px;
    }
    .delete-product-name {
        margin: 12px 0 0;
        color: var(--ink);
        font-size: 13.5px;
        line-height: 1.5;
        font-weight: 600;
    }
    @media (max-width: 1024px) {
        .products-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }
    @media (max-width: 768px) {
        .products-action-row {
            justify-content: stretch;
        }
        .products-action-row #openAddProductModal {
            width: 100%;
            justify-content: center;
        }
        .filters-bar {
            flex-direction: column;
            align-items: stretch;
        }
        .products-instant-search {
            width: 100%;
        }
        .filter-select {
            width: 100%;
            min-width: 0;
        }
    }
    @media (max-width: 680px) {
        .products-toolbar,
        .products-results {
            padding: 12px;
        }
        .products-grid {
            grid-template-columns: 1fr;
            gap: 16px;
        }
        .form-grid {
            grid-template-columns: 1fr;
        }
        .field.span-2 {
            grid-column: span 1;
        }
    }
</style>
@endsection

@section('content')
    @php
        $productActionSuccessMessages = [
            'Product added successfully.' => 'Product added',
            'Product updated successfully.' => 'Product updated',
            'Product deleted successfully.' => 'Product deleted',
        ];
        $currentSuccessMessage = session('success');
        $showProductActionSuccessModal = is_string($currentSuccessMessage) && array_key_exists($currentSuccessMessage, $productActionSuccessMessages);
    @endphp

    <div class="products-page-wrapper">
        @if (session('success') && ! $showProductActionSuccessModal)
            <div class="alert alert-success" style="margin-bottom:16px;">{{ session('success') }}</div>
        @endif

        <div
            class="products-page"
            data-open-add-product-modal="{{ $errors->any() ? '1' : '0' }}"
            data-open-product-feedback-modal="{{ $showProductActionSuccessModal ? '1' : '0' }}"
        >
            <div class="products-action-row">
                <button class="btn btn-primary" type="button" id="openAddProductModal">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                    </svg>
                    Add Product
                </button>
            </div>

            <div class="products-toolbar">
                <div class="filters-bar">
                    <div class="products-instant-search">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                            <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
                        </svg>
                        <input id="productsInstantSearch" class="control" type="search" placeholder="Search products..." autocomplete="off">
                    </div>

                    <select id="categoryFilter" class="control filter-select">
                        <option value="">All Categories</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat }}">{{ ucfirst($cat) }}</option>
                        @endforeach
                    </select>
                    <select id="sortFilter" class="control filter-select">
                        <option value="default">Default Order</option>
                        <option value="name-asc">Name A-Z</option>
                        <option value="name-desc">Name Z-A</option>
                        <option value="price-asc">Price Low to High</option>
                        <option value="price-desc">Price High to Low</option>
                    </select>
                </div>
            </div>

            @if ($errors->any())
                <div class="error-box">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="products-results">
                @if ($products->count() > 0)
                    <div class="products-grid">
                        @foreach ($products as $product)
                            <article class="product-card" data-product-card="1" data-product-name="{{ e(strtolower((string) $product->name)) }}" data-product-category="{{ e(strtolower((string) $product->category)) }}">
                                <div class="product-media">
                                    @if ($product->image)
                                        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}">
                                    @else
                                        <div class="product-fallback">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                                            </svg>
                                        </div>
                                    @endif
                                </div>

                                <div class="product-body">
                                    <div class="product-head">
                                        <h3 class="product-name" data-product-price="{{ (float) $product->price }}">{{ $product->name }}</h3>
                                        <form method="POST" action="{{ route('concessionaire.products.toggle', $product) }}">
                                            @csrf
                                            <button type="submit" class="toggle-btn {{ $product->is_available ? 'available' : 'unavailable' }}">
                                                {{ $product->is_available ? 'Available' : 'Unavailable' }}
                                            </button>
                                        </form>
                                    </div>

                                    <p class="product-desc">{{ \Illuminate\Support\Str::limit($product->description ?: 'No description provided yet.', 90) }}</p>

                                    <div class="product-chip-row">
                                        <span class="product-chip">{{ ucfirst($product->category) }}</span>
                                    </div>

                                    <div class="product-bottom">
                                        <p class="product-price">PHP {{ number_format($product->price, 2) }}</p>

                                        <div class="product-links">
                                            <button
                                                type="button"
                                                class="open-edit-btn btn btn-secondary btn-sm"
                                                data-id="{{ $product->id }}"
                                                data-name="{{ e($product->name) }}"
                                                data-description="{{ e($product->description ?? '') }}"
                                                data-price="{{ (float) $product->price }}"
                                                data-category="{{ e($product->category) }}"
                                                data-image-path="{{ $product->image ? e(asset('storage/' . $product->image)) : '' }}"
                                                data-is-available="{{ $product->is_available ? '1' : '0' }}"
                                            >
                                                Edit
                                            </button>

                                            <form method="POST" action="{{ route('concessionaire.products.destroy', $product) }}" class="delete-product-form" data-product-name="{{ e($product->name) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger-soft btn-sm">Delete</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>

                    @if ($products->hasPages())
                        <div class="pagination-wrap">
                            {{ $products->links() }}
                        </div>
                    @endif
                @else
                    <div class="empty-state">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                        </svg>
                        <h3>Start Your Catalog</h3>
                        <p>Add your first product so students can discover what your stall offers.</p>
                    </div>
                @endif
            </div>
        </div>

    </div>

    <div class="modal-wrap" id="addProductModal">
        <div class="modal-card">
            <div class="modal-head">
                <h3>Add Product</h3>
                <button type="button" class="modal-close" id="closeAddProductModal">&times;</button>
            </div>
            <p class="modal-sub">Create a new product listing to keep your storefront fresh and complete.</p>

            <form method="POST" action="{{ route('concessionaire.products.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="form-grid">
                    <div class="field span-2">
                        <label for="name">Name</label>
                        <input id="name" type="text" name="name" value="{{ old('name') }}" required maxlength="255">
                    </div>

                    <div class="field span-2">
                        <label for="description">Description</label>
                        <textarea id="description" name="description">{{ old('description') }}</textarea>
                    </div>

                    <div class="field">
                        <label for="price">Price</label>
                        <input id="price" type="number" name="price" value="{{ old('price') }}" min="0" step="0.01" required>
                    </div>

                    <div class="field">
                        <label for="category">Category</label>
                        <input id="category" type="text" name="category" value="{{ old('category') }}" placeholder="e.g. food, beverage, snack" required maxlength="100">
                    </div>

                    <div class="field span-2">
                        <label for="image">Image (jpg, jpeg, png, webp, max 2MB)</label>
                        <label for="image" class="upload-zone">
                            <span>
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 16V8m0 0-3 3m3-3 3 3" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 16.5a4.5 4.5 0 0 1-4.5 4.5h-9A4.5 4.5 0 0 1 3 16.5a4.5 4.5 0 0 1 4.5-4.5h.179A5.25 5.25 0 0 1 18 10.5h.75A2.25 2.25 0 0 1 21 12.75v3.75Z" />
                                </svg>
                                <span class="upload-zone-title">Click to upload image</span>
                                <span class="upload-zone-subtext">JPG, JPEG, PNG, WEBP - max 2MB</span>
                            </span>
                            <input id="image" class="upload-input" type="file" name="image" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">
                        </label>
                        <p class="upload-file-name" id="imageFileName">No file selected</p>
                    </div>

                    <div class="field span-2">
                        <input type="hidden" name="is_available" value="0">
                        <label class="checkbox-line">
                            <input type="checkbox" name="is_available" value="1" {{ old('is_available', '1') ? 'checked' : '' }}>
                            Available
                        </label>
                    </div>
                </div>

                <div class="modal-actions">
                    <button class="btn btn-secondary" type="button" id="cancelAddProduct">Cancel</button>
                    <button class="btn btn-primary" type="submit">Save Product</button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal-wrap" id="editProductModal">
        <div class="modal-card">
            <div class="modal-head">
                <h3>Edit Product</h3>
                <button type="button" class="modal-close" id="closeEditProductModal">&times;</button>
            </div>
            <p class="modal-sub">Update your product details without leaving this page.</p>

            <form
                id="editProductForm"
                method="POST"
                action="{{ route('concessionaire.products.update', ['product' => 0]) }}"
                data-action-template="{{ route('concessionaire.products.update', ['product' => '__PRODUCT__']) }}"
                enctype="multipart/form-data"
            >
                @csrf
                @method('PUT')

                <div class="form-grid">
                    <div class="field span-2">
                        <label for="edit_name">Name</label>
                        <input id="edit_name" type="text" name="name" required maxlength="255">
                    </div>

                    <div class="field span-2">
                        <label for="edit_description">Description</label>
                        <textarea id="edit_description" name="description"></textarea>
                    </div>

                    <div class="field">
                        <label for="edit_price">Price</label>
                        <input id="edit_price" type="number" name="price" min="0" step="0.01" required>
                    </div>

                    <div class="field">
                        <label for="edit_category">Category</label>
                        <input id="edit_category" type="text" name="category" placeholder="e.g. food, beverage, snack" required maxlength="100">
                    </div>

                    <div class="field span-2">
                        <label>Current image preview</label>
                        <div class="edit-current-image is-hidden" id="editImagePreviewWrap">
                            <img id="editImagePreview" src="" alt="Current product image preview">
                        </div>
                        <p class="edit-image-note" id="editImagePreviewEmpty">No image uploaded for this product.</p>
                    </div>

                    <div class="field span-2">
                        <label for="edit_image">Upload New Image (jpg, jpeg, png, webp, max 2MB)</label>
                        <label for="edit_image" class="upload-zone">
                            <span>
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 16V8m0 0-3 3m3-3 3 3" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 16.5a4.5 4.5 0 0 1-4.5 4.5h-9A4.5 4.5 0 0 1 3 16.5a4.5 4.5 0 0 1 4.5-4.5h.179A5.25 5.25 0 0 1 18 10.5h.75A2.25 2.25 0 0 1 21 12.75v3.75Z" />
                                </svg>
                                <span class="upload-zone-title">Click to upload image</span>
                                <span class="upload-zone-subtext">JPG, JPEG, PNG, WEBP - max 2MB</span>
                            </span>
                            <input id="edit_image" class="upload-input" type="file" name="image" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">
                        </label>
                        <p class="upload-file-name" id="editImageFileName">No file selected</p>
                    </div>

                    <div class="field span-2">
                        <input type="hidden" name="is_available" value="0">
                        <label class="checkbox-line">
                            <input id="edit_is_available" type="checkbox" name="is_available" value="1">
                            Available
                        </label>
                    </div>
                </div>

                <div class="modal-actions">
                    <button class="btn btn-secondary" type="button" id="cancelEditProduct">Cancel</button>
                    <button class="btn btn-primary" type="submit">Update Product</button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal-wrap" id="deleteProductModal">
        <div class="modal-card modal-card-sm">
            <div class="modal-head">
                <h3>Delete Product</h3>
                <button type="button" class="modal-close" id="closeDeleteProductModal">&times;</button>
            </div>
            <p class="modal-sub">This action cannot be undone.</p>
            <p class="delete-product-name" id="deleteProductName">You are about to permanently delete this product.</p>

            <div class="modal-actions">
                <button class="btn btn-secondary" type="button" id="cancelDeleteProduct">Cancel</button>
                <button class="btn btn-danger" type="button" id="confirmDeleteProduct">Delete</button>
            </div>
        </div>
    </div>

    @if ($showProductActionSuccessModal)
        <div class="modal-wrap" id="productFeedbackModal">
            <div class="modal-card modal-card-sm">
                <div class="modal-head">
                    <h3>{{ $productActionSuccessMessages[$currentSuccessMessage] }}</h3>
                    <button type="button" class="modal-close" id="closeProductFeedbackModal">&times;</button>
                </div>
                <p class="modal-sub">{{ $currentSuccessMessage }}</p>

                <div class="modal-actions">
                    <button class="btn btn-primary" type="button" id="ackProductFeedbackModal">Okay</button>
                </div>
            </div>
        </div>
    @endif

    <script>
        (() => {
            const addProductModal = document.getElementById('addProductModal');
            const editProductModal = document.getElementById('editProductModal');
            const deleteProductModal = document.getElementById('deleteProductModal');
            const productFeedbackModal = document.getElementById('productFeedbackModal');

            const openButtons = [
                document.getElementById('openAddProductModal')
            ].filter(Boolean);

            const closeButtons = [
                document.getElementById('closeAddProductModal'),
                document.getElementById('cancelAddProduct')
            ].filter(Boolean);

            const closeEditButtons = [
                document.getElementById('closeEditProductModal'),
                document.getElementById('cancelEditProduct')
            ].filter(Boolean);

            const imageInput = document.getElementById('image');
            const imageFileName = document.getElementById('imageFileName');
            const editImageInput = document.getElementById('edit_image');
            const editImageFileName = document.getElementById('editImageFileName');

            const editProductForm = document.getElementById('editProductForm');
            const editActionTemplate = editProductForm ? editProductForm.dataset.actionTemplate : '';
            const editOpenButtons = document.querySelectorAll('.open-edit-btn');
            const editNameInput = document.getElementById('edit_name');
            const editDescriptionInput = document.getElementById('edit_description');
            const editPriceInput = document.getElementById('edit_price');
            const editCategoryInput = document.getElementById('edit_category');
            const editAvailableInput = document.getElementById('edit_is_available');
            const editImagePreviewWrap = document.getElementById('editImagePreviewWrap');
            const editImagePreview = document.getElementById('editImagePreview');
            const editImagePreviewEmpty = document.getElementById('editImagePreviewEmpty');
            const deleteProductForms = Array.from(document.querySelectorAll('.delete-product-form'));
            const deleteProductName = document.getElementById('deleteProductName');
            const closeDeleteProductModal = document.getElementById('closeDeleteProductModal');
            const cancelDeleteProduct = document.getElementById('cancelDeleteProduct');
            const confirmDeleteProduct = document.getElementById('confirmDeleteProduct');
            const closeProductFeedbackModal = document.getElementById('closeProductFeedbackModal');
            const ackProductFeedbackModal = document.getElementById('ackProductFeedbackModal');
            const instantSearchInput = document.getElementById('productsInstantSearch');
            const productsGrid = document.querySelector('.products-grid');
            const productCards = Array.from(document.querySelectorAll('.product-card[data-product-card="1"]'));
            let noMatchState = null;
            let pendingDeleteForm = null;
            let skipDeleteConfirmationOnce = false;

            if (addProductModal) {
                openButtons.forEach((btn) => {
                    btn.addEventListener('click', () => addProductModal.classList.add('show'));
                });

                closeButtons.forEach((btn) => {
                    btn.addEventListener('click', () => addProductModal.classList.remove('show'));
                });

                addProductModal.addEventListener('click', (event) => {
                    if (event.target === addProductModal) {
                        addProductModal.classList.remove('show');
                    }
                });
            }

            if (imageInput && imageFileName) {
                imageInput.addEventListener('change', () => {
                    imageFileName.textContent = imageInput.files && imageInput.files.length
                        ? imageInput.files[0].name
                        : 'No file selected';
                });
            }

            if (editImageInput && editImageFileName) {
                editImageInput.addEventListener('change', () => {
                    editImageFileName.textContent = editImageInput.files && editImageInput.files.length
                        ? editImageInput.files[0].name
                        : 'No file selected';
                });
            }

            closeEditButtons.forEach((btn) => {
                btn.addEventListener('click', () => {
                    if (editProductModal) {
                        editProductModal.classList.remove('show');
                    }
                });
            });

            if (editProductModal) {
                editProductModal.addEventListener('click', (event) => {
                    if (event.target === editProductModal) {
                        editProductModal.classList.remove('show');
                    }
                });
            }

            const hideDeleteModal = () => {
                if (deleteProductModal) {
                    deleteProductModal.classList.remove('show');
                }
                pendingDeleteForm = null;
            };

            deleteProductForms.forEach((form) => {
                form.addEventListener('submit', (event) => {
                    if (skipDeleteConfirmationOnce) {
                        skipDeleteConfirmationOnce = false;
                        return;
                    }

                    event.preventDefault();
                    pendingDeleteForm = form;

                    if (deleteProductName) {
                        const productName = form.dataset.productName || 'this product';
                        deleteProductName.textContent = `You are about to permanently delete "${productName}".`;
                    }

                    if (deleteProductModal) {
                        deleteProductModal.classList.add('show');
                    }
                });
            });

            [closeDeleteProductModal, cancelDeleteProduct].filter(Boolean).forEach((btn) => {
                btn.addEventListener('click', hideDeleteModal);
            });

            if (deleteProductModal) {
                deleteProductModal.addEventListener('click', (event) => {
                    if (event.target === deleteProductModal) {
                        hideDeleteModal();
                    }
                });
            }

            if (confirmDeleteProduct) {
                confirmDeleteProduct.addEventListener('click', () => {
                    if (!pendingDeleteForm) {
                        return;
                    }

                    const formToSubmit = pendingDeleteForm;
                    pendingDeleteForm = null;

                    if (deleteProductModal) {
                        deleteProductModal.classList.remove('show');
                    }

                    skipDeleteConfirmationOnce = true;
                    if (typeof formToSubmit.requestSubmit === 'function') {
                        formToSubmit.requestSubmit();
                    } else {
                        formToSubmit.submit();
                    }
                });
            }

            [closeProductFeedbackModal, ackProductFeedbackModal].filter(Boolean).forEach((btn) => {
                btn.addEventListener('click', () => {
                    if (productFeedbackModal) {
                        productFeedbackModal.classList.remove('show');
                    }
                });
            });

            if (productFeedbackModal) {
                productFeedbackModal.addEventListener('click', (event) => {
                    if (event.target === productFeedbackModal) {
                        productFeedbackModal.classList.remove('show');
                    }
                });
            }

            window.openEditModal = (id, name, description, price, category, imagePath, isAvailable) => {
                if (!editProductModal || !editProductForm || !editActionTemplate) {
                    return;
                }

                editProductForm.action = editActionTemplate.replace('__PRODUCT__', String(id));

                if (editNameInput) {
                    editNameInput.value = name || '';
                }
                if (editDescriptionInput) {
                    editDescriptionInput.value = description || '';
                }
                if (editPriceInput) {
                    editPriceInput.value = price ?? '';
                }
                if (editCategoryInput) {
                    editCategoryInput.value = category || '';
                }
                if (editAvailableInput) {
                    editAvailableInput.checked = Boolean(isAvailable);
                }
                if (editImageInput) {
                    editImageInput.value = '';
                }
                if (editImageFileName) {
                    editImageFileName.textContent = 'No file selected';
                }

                if (editImagePreviewWrap && editImagePreview && editImagePreviewEmpty) {
                    if (imagePath) {
                        editImagePreview.src = imagePath;
                        editImagePreviewWrap.classList.remove('is-hidden');
                        editImagePreviewEmpty.classList.add('is-hidden');
                    } else {
                        editImagePreview.src = '';
                        editImagePreviewWrap.classList.add('is-hidden');
                        editImagePreviewEmpty.classList.remove('is-hidden');
                    }
                }

                editProductModal.classList.add('show');
            };

            editOpenButtons.forEach((btn) => {
                btn.addEventListener('click', () => {
                    const id = Number(btn.dataset.id || 0);
                    const name = btn.dataset.name || '';
                    const description = btn.dataset.description || '';
                    const price = btn.dataset.price || '';
                    const category = btn.dataset.category || '';
                    const imagePath = btn.dataset.imagePath || '';
                    const isAvailable = btn.dataset.isAvailable === '1';

                    window.openEditModal(id, name, description, price, category, imagePath, isAvailable);
                });
            });

            if (addProductModal && document.querySelector('[data-open-add-product-modal="1"]')) {
                addProductModal.classList.add('show');
            }

            if (productFeedbackModal && document.querySelector('[data-open-product-feedback-modal="1"]')) {
                productFeedbackModal.classList.add('show');
            }

            const categoryFilter = document.getElementById('categoryFilter');
            const sortFilter = document.getElementById('sortFilter');

            if (instantSearchInput && productsGrid && productCards.length) {
                noMatchState = document.createElement('div');
                noMatchState.className = 'empty-state is-hidden';
                noMatchState.innerHTML = '<h3>No Matching Products</h3><p>Try a different keyword for name or category.</p>';
                productsGrid.parentElement.appendChild(noMatchState);

                const applyFiltersAndSort = () => {
                    const searchQuery = (instantSearchInput.value || '').trim().toLowerCase();
                    const categoryValue = categoryFilter ? categoryFilter.value.toLowerCase() : '';
                    const sortValue = sortFilter ? sortFilter.value : 'default';

                    let visibleCards = [];

                    productCards.forEach((card) => {
                        const productName = card.dataset.productName || '';
                        const productCategory = card.dataset.productCategory || '';
                        
                        const searchMatch = searchQuery === '' || productName.includes(searchQuery) || productCategory.includes(searchQuery);
                        const categoryMatch = categoryValue === '' || productCategory === categoryValue;
                        const matches = searchMatch && categoryMatch;

                        if (matches) {
                            visibleCards.push(card);
                        }
                        card.style.display = matches ? '' : 'none';
                    });

                    if (sortValue !== 'default' && visibleCards.length > 0) {
                        visibleCards.sort((a, b) => {
                            const aName = a.dataset.productName || '';
                            const bName = b.dataset.productName || '';
                            const aPrice = parseFloat(a.querySelector('[data-product-price]')?.dataset.productPrice || 0);
                            const bPrice = parseFloat(b.querySelector('[data-product-price]')?.dataset.productPrice || 0);

                            if (sortValue === 'name-asc') return aName.localeCompare(bName);
                            if (sortValue === 'name-desc') return bName.localeCompare(aName);
                            if (sortValue === 'price-asc') return aPrice - bPrice;
                            if (sortValue === 'price-desc') return bPrice - aPrice;
                            return 0;
                        });

                        visibleCards.forEach((card) => {
                            productsGrid.appendChild(card);
                        });
                    }

                    noMatchState.classList.toggle('is-hidden', visibleCards.length > 0);
                };

                instantSearchInput.addEventListener('input', applyFiltersAndSort);
                instantSearchInput.addEventListener('keydown', (event) => {
                    if (event.key === 'Enter') {
                        event.preventDefault();
                    }
                });

                if (categoryFilter) {
                    categoryFilter.addEventListener('change', applyFiltersAndSort);
                }

                if (sortFilter) {
                    sortFilter.addEventListener('change', applyFiltersAndSort);
                }

                // Prefill from the navbar search handoff (?q=...)
                const initialQuery = (new URLSearchParams(window.location.search).get('q') || '').trim();
                if (initialQuery) {
                    instantSearchInput.value = initialQuery;
                    applyFiltersAndSort();
                }
            }
        })();
    </script>
@endsection
