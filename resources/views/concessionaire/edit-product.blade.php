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
    .preview {
        width: 220px;
        height: 160px;
        border-radius: 8px;
        border: 1px solid var(--line-strong);
        overflow: hidden;
        background: var(--paper);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--muted);
        font-size: 13px;
    }
    .preview img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .actions {
        margin-top: 20px;
        display: flex;
        justify-content: flex-end;
        gap: 10px;
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
                        <label>Current Image</label>
                        <div class="preview">
                            @if ($product->image)
                                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}">
                            @else
                                <span>No image uploaded</span>
                            @endif
                        </div>
                    </div>

                    <div class="field full">
                        <label for="image">New Image (optional)</label>
                        <input id="image" class="control" type="file" name="image" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">
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
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
@endsection
