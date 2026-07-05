@extends('concessionaire.layout')

@section('title', 'Products')

@section('extra-css')
<style>
    @font-face {
        font-family: 'Inter';
        src: url('/fonts/web/Inter-Regular.woff2') format('woff2');
        font-weight: 400;
        font-style: normal;
        font-display: swap;
    }
    @font-face {
        font-family: 'Inter';
        src: url('/fonts/web/Inter-Medium.woff2') format('woff2');
        font-weight: 500;
        font-style: normal;
        font-display: swap;
    }
    @font-face {
        font-family: 'Inter';
        src: url('/fonts/web/Inter-SemiBold.woff2') format('woff2');
        font-weight: 600;
        font-style: normal;
        font-display: swap;
    }
    .products-page-wrapper,
    .products-page-wrapper input,
    .products-page-wrapper select,
    .products-page-wrapper button,
    .products-page-wrapper textarea {
        font-family: 'Inter', sans-serif;
    }
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
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 16px;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
    }
    .products-instant-search {
        position: relative;
        flex: 1;
        min-width: 0;
    }
    .products-instant-search input {
        width: 100%;
        padding: 12px 16px 12px 44px;
        border: 2px solid #e2e8f0;
        border-radius: 10px;
        font-size: 14px;
        font-family: inherit;
        transition: border-color 0.2s;
        background: #fff;
    }
    .products-instant-search input:focus {
        outline: none;
        border-color: var(--green);
    }
    .products-instant-search svg {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        width: 18px;
        height: 18px;
        color: #94a3b8;
    }
    #openAddProductModal {
        padding: 10px 24px;
        font-weight: 600;
        border-radius: 6px;
        background: #0a5c2f;
        color: #ffffff;
        border: none;
        cursor: pointer;
        font-size: 14px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    #openAddProductModal:hover {
        background: #084a25;
    }
    #openAddProductModal svg {
        width: 16px;
        height: 16px;
    }
    .error-box {
        margin-top: 16px;
        border: 1px solid #fecaca;
        background: #fef2f2;
        color: #991b1b;
        border-radius: 10px;
        padding: 12px 14px;
        font-size: 14px;
    }
    .filters-bar {
        background: transparent;
        border-radius: 0;
        padding: 0;
        margin-bottom: 0;
        box-shadow: none;
        border-top: 0;
        display: flex;
        gap: 14px;
        align-items: center;
    }
    .search-box {
        min-width: 0;
        position: relative;
    }
    .search-box input {
        width: 100%;
        padding: 12px 16px 12px 44px;
        border: 2px solid #e2e8f0;
        border-radius: 10px;
        font-size: 14px;
        font-family: inherit;
        transition: border-color 0.2s;
    }
    .search-box input:focus {
        outline: none;
        border-color: var(--green);
    }
    .search-box svg {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        width: 18px;
        height: 18px;
        color: #94a3b8;
    }
    .filter-select {
        padding: 12px 16px;
        border: 2px solid #e2e8f0;
        border-radius: 10px;
        font-size: 14px;
        font-family: inherit;
        background: #fff;
        cursor: pointer;
        min-width: 170px;
        width: auto;
        flex-shrink: 0;
    }
    .filter-select:focus {
        outline: none;
        border-color: var(--green);
    }
    .products-results {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 18px;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
    }
    .products-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 20px;
    }
    .product-card {
        border: 1px solid #e7edf5;
        border-radius: 16px;
        background: #fff;
        overflow: hidden;
        box-shadow: 0 8px 22px rgba(15, 23, 42, 0.06);
        display: flex;
        flex-direction: column;
        min-height: 100%;
        transition: transform 0.22s ease, box-shadow 0.22s ease;
    }
    .product-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 14px 28px rgba(15, 23, 42, 0.12);
    }
    .product-media {
        position: relative;
        width: 100%;
        padding-top: 78%;
        background: #f1f5f9;
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
        font-size: 17px;
        line-height: 1.35;
        font-weight: 600;
        color: #0f172a;
        word-break: break-word;
    }
    .product-desc {
        margin: 0;
        color: #64748b;
        font-size: 12px;
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
        padding: 4px 10px;
        border-radius: 999px;
        font-size: 10px;
        font-weight: 600;
        letter-spacing: 0.04em;
        text-transform: uppercase;
        border: 1px solid #dbe4ef;
        color: #475569;
        background: #f8fafc;
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
        font-size: 15px;
        line-height: 1.2;
        font-weight: 700;
        color: #0f172a;
    }
    .product-actions {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 10px;
    }
    .product-actions form {
        margin: 0;
    }
    .toggle-btn {
        border: 0;
        padding: 5px 10px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 600;
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
        color: #166534;
        background: #ecfdf3;
    }
    .toggle-btn.unavailable {
        color: #64748b;
        background: #f1f5f9;
    }
    .product-links {
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    .open-edit-btn {
        padding: 8px 20px;
        font-weight: 600;
        border-radius: 6px;
        background: #f3f4f6;
        color: #1f2937;
        border: none;
        cursor: pointer;
        font-size: 13px;
    }
    .open-edit-btn:hover {
        background: #e5e7eb;
    }
    .product-links form button[type="submit"] {
        padding: 8px 20px;
        font-weight: 600;
        border-radius: 6px;
        background: #fef2f2;
        color: #dc2626;
        border: none;
        cursor: pointer;
        font-size: 13px;
    }
    .product-links form button[type="submit"]:hover {
        background: #fee2e2;
    }
    .edit-current-image {
        margin-top: 4px;
        border: 1px solid #cbd5e1;
        border-radius: 10px;
        background: #f8fafc;
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
        color: #6b7280;
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
        padding: 8px 14px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 500;
        text-decoration: none;
        color: #475569;
        background: #fff;
        border: 1px solid #e2e8f0;
        transition: all 0.15s;
        display: inline-block;
    }
    .pagination-wrap a:hover {
        border-color: #0a5c2f;
        color: #0a5c2f;
    }
    .pagination-wrap span[aria-current="page"] {
        background: #0a5c2f;
        border-color: #0a5c2f;
        color: #fff;
    }
    .pagination-wrap span.cursor-default {
        color: #cbd5e1;
        background: #f8fafc;
    }
    .empty-state {
        margin-top: 0;
        border: 1px dashed #cbd5e1;
        border-radius: 16px;
        background: #fff;
        text-align: center;
        padding: 56px 20px;
    }
    .empty-state svg {
        width: 72px;
        height: 72px;
        color: #cbd5e1;
        margin-bottom: 10px;
    }
    .empty-state h3 {
        margin: 0;
        color: #0f172a;
        font-size: 30px;
        line-height: 1.1;
    }
    .empty-state p {
        margin: 10px 0 18px;
        color: #64748b;
        font-size: 15px;
    }
    .modal-wrap {
        position: fixed;
        inset: 0;
        z-index: 3000;
        background: rgba(15, 23, 42, 0.55);
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
        max-width: 760px;
        background: #fff;
        border-radius: 14px;
        padding: 20px;
        box-shadow: 0 22px 54px rgba(0,0,0,0.28);
        font-family: 'Century Gothic', sans-serif;
    }
    .modal-card-sm {
        max-width: 460px;
    }
    .modal-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
    }
    .modal-head h3 {
        margin: 0;
        color: #0f172a;
        font-size: 24px;
        font-weight: 800;
    }
    .modal-close {
        width: 34px;
        height: 34px;
        border: 0;
        border-radius: 8px;
        background: #f1f5f9;
        color: #475569;
        font-size: 20px;
        line-height: 1;
        cursor: pointer;
    }
    .modal-sub {
        margin: 8px 0 0;
        color: #64748b;
        font-size: 14px;
    }
    .form-grid {
        margin-top: 14px;
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
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
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: #111827;
    }
    .field input,
    .field textarea,
    .field select {
        border: 1px solid #cbd5e1;
        border-radius: 10px;
        padding: 9px 12px;
        font: inherit;
        color: #111827;
        background: #f9fafb;
    }
    .modal-card .field input::placeholder,
    .modal-card .field textarea::placeholder {
        color: #6b7280 !important;
    }
    .modal-card .field input::-webkit-input-placeholder,
    .modal-card .field textarea::-webkit-input-placeholder {
        color: #6b7280 !important;
    }
    .field textarea {
        min-height: 98px;
        resize: vertical;
    }
    .upload-zone {
        position: relative;
        border: 1px dashed #94a3b8;
        border-radius: 10px;
        background: #f8fafc;
        min-height: 132px;
        padding: 18px 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        cursor: pointer;
    }
    .upload-zone:hover {
        background: #f1f5f9;
        border-color: #64748b;
    }
    .upload-zone svg {
        width: 24px;
        height: 24px;
        color: #475569;
        margin: 0 auto 10px;
        display: block;
    }
    .upload-zone-title {
        display: block;
        font-size: 14px;
        font-weight: 600;
        color: #111827;
    }
    .upload-zone-subtext {
        display: block;
        margin-top: 4px;
        font-size: 12px;
        color: #6b7280;
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
        color: #6b7280;
    }
    .checkbox-line {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: #111827;
        font-size: 14px;
    }
    .modal-actions {
        margin-top: 16px;
        display: flex;
        justify-content: flex-end;
        gap: 10px;
    }
    .btn-cancel,
    .btn-save {
        border-radius: 10px;
        font-size: 14px;
        font-weight: 700;
        padding: 10px 14px;
        cursor: pointer;
    }
    .btn-cancel {
        border: 1px solid #cbd5e1;
        background: #fff;
        color: #475569;
    }
    .btn-save {
        border: 0;
        background: #0f6a35;
        color: #fff;
    }
    .btn-save:hover {
        background: #0b5a2d;
    }
    .btn-danger {
        border: 0;
        border-radius: 10px;
        background: #dc2626;
        color: #fff;
        font-size: 14px;
        font-weight: 700;
        padding: 10px 14px;
        cursor: pointer;
    }
    .btn-danger:hover {
        background: #b91c1c;
    }
    .delete-product-name {
        margin: 10px 0 0;
        color: #1e293b;
        font-size: 14px;
        line-height: 1.4;
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
        .products-action-row .add-product-btn {
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
        .product-name {
            font-size: 18px;
        }
        .product-price {
            font-size: 19px;
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
                <button class="px-8 py-3 font-semibold rounded bg-green-800 text-white hover:bg-green-900 text-sm" type="button" id="openAddProductModal">
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
                        <input id="productsInstantSearch" type="search" placeholder="Search products..." autocomplete="off">
                    </div>

                    <select id="categoryFilter" class="filter-select">
                        <option value="">All Categories</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat }}">{{ ucfirst($cat) }}</option>
                        @endforeach
                    </select>
                    <select id="sortFilter" class="filter-select">
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
                                                class="open-edit-btn px-8 py-3 font-semibold rounded bg-gray-100 text-gray-800 hover:bg-gray-200 text-sm"
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
                                                <button type="submit" class="px-8 py-3 font-semibold rounded bg-red-50 text-red-600 hover:bg-red-100 text-sm">Delete</button>
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
                    <button class="btn-cancel" type="button" id="cancelAddProduct">Cancel</button>
                    <button class="btn-save" type="submit">Save Product</button>
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
                    <button class="btn-cancel" type="button" id="cancelEditProduct">Cancel</button>
                    <button class="btn-save" type="submit">Update Product</button>
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
                <button class="btn-cancel" type="button" id="cancelDeleteProduct">Cancel</button>
                <button class="btn-danger" type="button" id="confirmDeleteProduct">Delete</button>
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
                    <button class="btn-save" type="button" id="ackProductFeedbackModal">Okay</button>
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
            }
        })();
    </script>
@endsection
