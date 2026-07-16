@extends('concessionaire.layout')

@section('title', 'Edit Product')

@section('extra-css')
<style>
    .edit-product-wrap {
        max-width: 780px;
        margin: 0 auto;
    }
    .edit-product-card {
        padding: 24px 26px;
    }
    .edit-product-card h2 {
        font-size: 20px;
        font-weight: 700;
        letter-spacing: -0.01em;
        color: var(--ink);
        margin-bottom: 18px;
    }
    .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 14px;
    }
    .field {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }
    .field.full {
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
    .field textarea {
        min-height: 110px;
        resize: vertical;
        line-height: 1.5;
    }
    .checkbox-line {
        display: flex;
        align-items: center;
        gap: 8px;
        font-family: var(--font-ui);
        font-size: 13.5px;
        font-weight: 500;
        text-transform: none;
        letter-spacing: 0;
        color: var(--ink);
        cursor: pointer;
    }
    .checkbox-line input[type="checkbox"] {
        accent-color: var(--pine);
        width: 15px;
        height: 15px;
    }
    .photo-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(130px, 1fr));
        gap: 10px;
    }
    .photo-tile {
        position: relative;
        border: 1px solid var(--line-strong);
        border-radius: 8px;
        overflow: hidden;
        background: var(--paper);
        aspect-ratio: 1;
    }
    .photo-tile img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
        transition: opacity .2s ease;
    }
    .photo-tile.marked img {
        opacity: 0.35;
    }
    .photo-tile.marked {
        border-color: var(--danger);
    }
    .photo-tile .cover-tag {
        position: absolute;
        top: 6px;
        left: 6px;
        font-family: var(--font-mono);
        font-size: 9px;
        font-weight: 600;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        background: var(--pine);
        color: var(--pine-ink, #0B1F16);
        border-radius: 4px;
        padding: 3px 6px;
        pointer-events: none;
    }
    .photo-remove {
        position: absolute;
        top: 6px;
        right: 6px;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        font-family: var(--font-mono);
        font-size: 10px;
        font-weight: 600;
        letter-spacing: 0.05em;
        text-transform: uppercase;
        background: rgba(255, 255, 255, 0.92);
        border: 1px solid var(--line-strong);
        border-radius: 5px;
        padding: 4px 7px;
        color: var(--danger);
        cursor: pointer;
        user-select: none;
    }
    html[data-theme="dark"] .photo-remove {
        background: rgba(20, 30, 25, 0.9);
    }
    .photo-remove input {
        accent-color: var(--danger);
        width: 12px;
        height: 12px;
        margin: 0;
    }
    .photo-note {
        font-size: 12.5px;
        color: var(--muted);
        margin-top: 2px;
    }
    .photo-note strong {
        color: var(--ink);
    }
    .no-photos {
        border: 1px dashed var(--line-strong);
        border-radius: 8px;
        padding: 18px;
        text-align: center;
        color: var(--muted);
        font-size: 13px;
    }
    .error-list {
        background: #FDF3F3;
        border: 1px solid #F2D8D8;
        color: var(--danger);
        border-radius: 8px;
        padding: 12px 16px;
        margin-bottom: 16px;
        font-size: 13.5px;
    }
    html[data-theme="dark"] .error-list {
        background: rgba(227, 106, 106, 0.12);
        border-color: rgba(227, 106, 106, 0.35);
        color: #F0A0A0;
    }
    .actions {
        margin-top: 20px;
        display: flex;
        justify-content: flex-end;
        gap: 10px;
    }
    @media (max-width: 700px) {
        .form-grid {
            grid-template-columns: 1fr;
        }
        .field.full {
            grid-column: span 1;
        }
    }
</style>
@endsection

@section('content')
    <div class="edit-product-wrap">
        <div class="panel edit-product-card">
            <h2>Edit Product</h2>

            @if ($errors->any())
                <div class="error-list">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('concessionaire.products.update', $product) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="form-grid">
                    <div class="field full">
                        <label for="name">Name</label>
                        <input id="name" class="control" type="text" name="name" value="{{ old('name', $product->name) }}" required maxlength="255">
                    </div>

                    <div class="field full">
                        <label for="description">Description</label>
                        <textarea id="description" class="control" name="description">{{ old('description', $product->description) }}</textarea>
                    </div>

                    <div class="field">
                        <label for="price">Price</label>
                        <input id="price" class="control" type="number" name="price" value="{{ old('price', $product->price) }}" min="0" step="0.01" required>
                    </div>

                    <div class="field">
                        <label for="category">Category</label>
                        <select id="category" class="control" name="category" required>
                            <option value="food" {{ old('category', $product->category) === 'food' ? 'selected' : '' }}>Food</option>
                            <option value="beverage" {{ old('category', $product->category) === 'beverage' ? 'selected' : '' }}>Beverage</option>
                            <option value="snack" {{ old('category', $product->category) === 'snack' ? 'selected' : '' }}>Snack</option>
                        </select>
                    </div>

                    <div class="field full">
                        <label>Current Photos ({{ $product->images->count() }} of 5)</label>
                        @if ($product->images->count() > 0)
                            <div class="photo-grid" id="photoGrid">
                                @foreach ($product->images as $image)
                                    <div class="photo-tile" data-photo-tile>
                                        <img src="{{ asset('storage/' . $image->path) }}" alt="Product photo {{ $loop->iteration }}">
                                        @if ($loop->first)
                                            <span class="cover-tag">Cover</span>
                                        @endif
                                        <label class="photo-remove">
                                            <input type="checkbox" name="remove_images[]" value="{{ $image->id }}" data-remove-photo>
                                            Remove
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="no-photos">No photos uploaded for this product yet.</div>
                        @endif
                        <p class="photo-note">Tick <strong>Remove</strong> on a photo to delete it when you save. The first photo is the cover shown on listings.</p>
                    </div>

                    <div class="field full">
                        <label for="images">Add Photos (jpg, jpeg, png, webp, max 2MB each)</label>
                        <input id="images" class="control" type="file" name="images[]" multiple accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">
                        <p class="photo-note" id="photoCountNote" data-existing="{{ $product->images->count() }}">A product can have at most <strong>5 photos</strong>.</p>
                    </div>

                    <div class="field full">
                        <input type="hidden" name="is_available" value="0">
                        <label class="checkbox-line">
                            <input type="checkbox" name="is_available" value="1" {{ old('is_available', $product->is_available) ? 'checked' : '' }}>
                            Available
                        </label>
                    </div>
                </div>

                <div class="actions">
                    <a href="{{ route('concessionaire.products') }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary" id="saveProductBtn">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        (() => {
            const imagesInput = document.getElementById('images');
            const note = document.getElementById('photoCountNote');
            const saveBtn = document.getElementById('saveProductBtn');
            const removeBoxes = Array.from(document.querySelectorAll('[data-remove-photo]'));
            const existing = Number(note ? note.dataset.existing : 0);

            const refresh = () => {
                removeBoxes.forEach((box) => {
                    box.closest('[data-photo-tile]').classList.toggle('marked', box.checked);
                });

                const removed = removeBoxes.filter((box) => box.checked).length;
                const added = imagesInput && imagesInput.files ? imagesInput.files.length : 0;
                const total = existing - removed + added;
                const over = total > 5;

                if (note) {
                    note.innerHTML = over
                        ? '<strong>Too many photos:</strong> a product can have at most 5. Remove ' + (total - 5) + ' more or pick fewer files.'
                        : 'A product can have at most <strong>5 photos</strong>. After saving you will have <strong>' + Math.max(total, 0) + '</strong>.';
                    note.style.color = over ? 'var(--danger)' : '';
                }
                if (saveBtn) {
                    saveBtn.disabled = over;
                    saveBtn.style.opacity = over ? '0.55' : '';
                    saveBtn.style.cursor = over ? 'not-allowed' : '';
                }
            };

            removeBoxes.forEach((box) => box.addEventListener('change', refresh));
            if (imagesInput) {
                imagesInput.addEventListener('change', refresh);
            }
            refresh();
        })();
    </script>
@endsection
