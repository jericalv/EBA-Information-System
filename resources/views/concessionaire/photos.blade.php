@extends('concessionaire.layout')

@section('title', 'Photos')

@section('extra-css')
<style>
    .photos-container {
        max-width: 1000px;
        margin: 0 auto;
    }
    .section-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 20px;
        flex-wrap: wrap;
        gap: 16px;
    }
    .section-header h2 {
        font-size: 20px;
        font-weight: 700;
        letter-spacing: -0.01em;
        color: var(--ink);
    }
    .photos-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 16px;
    }
    .photo-card {
        aspect-ratio: 1;
        background: var(--card);
        border: 1px solid var(--line);
        border-radius: 10px;
        overflow: hidden;
        cursor: pointer;
        transition: border-color 0.15s ease;
    }
    .photo-card:hover {
        border-color: var(--line-strong);
    }
    .photo-card img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .photo-placeholder {
        width: 100%;
        height: 100%;
        background: var(--paper);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--faint);
    }
    .empty-state {
        text-align: center;
        padding: 56px 20px;
        background: var(--card);
        border: 1px dashed var(--line-strong);
        border-radius: 12px;
    }
    .empty-state svg {
        width: 56px;
        height: 56px;
        color: var(--line-strong);
        margin: 0 auto 14px;
    }
    .empty-state h3 {
        font-size: 18px;
        font-weight: 700;
        letter-spacing: -0.01em;
        color: var(--ink);
        margin-bottom: 6px;
    }
    .empty-state p {
        color: var(--muted);
        font-size: 13.5px;
        margin-bottom: 18px;
    }
</style>
@endsection

@section('content')
    <div class="photos-container">
        <div class="section-header">
            <h2>Photo Gallery</h2>
            <button class="btn btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
                </svg>
                Upload Photos
            </button>
        </div>

        <!-- Empty State -->
        <div class="empty-state">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
            </svg>
            <h3>No Photos Yet</h3>
            <p>Upload photos to showcase your stall, products, and services.</p>
            <button class="btn btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
                </svg>
                Upload Your First Photo
            </button>
        </div>

        <!-- Photos grid (shown when photos exist)
        <div class="photos-grid">
            <div class="photo-card">
                <div class="photo-placeholder">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="48" height="48">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                    </svg>
                </div>
            </div>
        </div>
        -->
    </div>
@endsection
