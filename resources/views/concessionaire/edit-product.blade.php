@extends('concessionaire.layout')

@section('title', 'Edit Product')

@section('extra-css')
<style>
    .edit-product-wrap {
        max-width: 820px;
        margin: 32px auto;
        padding: 0 24px;
    }
    .card {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 8px 26px rgba(0,0,0,0.08);
        padding: 24px;
    }
    .card h2 {
        color: var(--green);
        margin-bottom: 16px;
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
        font-size: 13px;
        font-weight: 700;
        color: #334155;
    }
    .field input,
    .field textarea,
    .field select {
        border: 1px solid #d1d5db;
        border-radius: 10px;
        padding: 10px 12px;
        font: inherit;
    }
    .field textarea {
        min-height: 110px;
        resize: vertical;
    }
    .preview {
        width: 220px;
        height: 160px;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        overflow: hidden;
        background: #f8fafc;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .preview img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .actions {
        margin-top: 18px;
        display: flex;
        justify-content: flex-end;
        gap: 10px;
    }
    .btn {
        padding: 10px 18px;
        border: none;
        border-radius: 10px;
        text-decoration: none;
        cursor: pointer;
        font-weight: 700;
        font-family: inherit;
        background: var(--green);
        color: #fff;
    }
    .btn:hover {
        background: var(--green-light);
    }
    .btn-outline {
        background: #fff;
        color: var(--green);
        border: 1px solid #cbd5e1;
    }
    .error-list {
        background: #fee2e2;
        border: 1px solid #fca5a5;
        color: #991b1b;
        border-radius: 10px;
        padding: 12px;
        margin-bottom: 14px;
        font-size: 14px;
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
        <div class="card">
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
                        <input id="name" type="text" name="name" value="{{ old('name', $product->name) }}" required maxlength="255">
                    </div>

                    <div class="field full">
                        <label for="description">Description</label>
                        <textarea id="description" name="description">{{ old('description', $product->description) }}</textarea>
                    </div>

                    <div class="field">
                        <label for="price">Price</label>
                        <input id="price" type="number" name="price" value="{{ old('price', $product->price) }}" min="0" step="0.01" required>
                    </div>

                    <div class="field">
                        <label for="category">Category</label>
                        <select id="category" name="category" required>
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
                        <input id="image" type="file" name="image" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">
                    </div>

                    <div class="field full">
                        <input type="hidden" name="is_available" value="0">
                        <label style="display: flex; align-items: center; gap: 8px; font-weight: 600;">
                            <input type="checkbox" name="is_available" value="1" {{ old('is_available', $product->is_available) ? 'checked' : '' }}>
                            Available
                        </label>
                    </div>
                </div>

                <div class="actions">
                    <a href="{{ route('concessionaire.products') }}" class="btn btn-outline">Cancel</a>
                    <button type="submit" class="btn">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
@endsection
