@extends('concessionaire.layout')

@section('title', 'Media')

@section('extra-css')
<style>
    .media-container {
        max-width: 760px;
        margin: 0 auto;
        display: flex;
        flex-direction: column;
        gap: 20px;
    }
    .media-header h2 {
        font-size: 20px;
        font-weight: 700;
        letter-spacing: -0.01em;
        color: var(--ink);
        margin: 0;
    }
    .media-header p {
        margin: 4px 0 0;
        font-size: 13.5px;
        color: var(--muted);
    }
    .media-panel {
        padding: 20px 22px;
    }
    .media-panel-head {
        margin-bottom: 16px;
    }
    .carousel-preview {
        position: relative;
        border-radius: 8px;
        overflow: hidden;
        background: var(--paper);
        border: 1px dashed var(--line-strong);
        min-height: 160px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .carousel-preview img {
        width: 100%;
        height: 200px;
        object-fit: cover;
        display: block;
    }
    .carousel-remove {
        position: absolute;
        top: 10px;
        right: 10px;
    }
    .carousel-drop-zone {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 8px;
        padding: 40px 24px;
        cursor: pointer;
        width: 100%;
        text-align: center;
    }
    .carousel-drop-zone svg {
        width: 40px;
        height: 40px;
        color: var(--faint);
    }
    .carousel-drop-title {
        font-size: 13.5px;
        font-weight: 600;
        color: var(--muted);
    }
    .carousel-drop-sub {
        font-size: 12px;
        color: var(--faint);
    }
    .carousel-form {
        display: flex;
        flex-direction: column;
        gap: 10px;
        margin-top: 16px;
    }
    .carousel-file-picker {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 11px 14px;
        border: 1px dashed var(--line-strong);
        border-radius: 6px;
        background: var(--paper);
        cursor: pointer;
        transition: border-color 0.15s ease, background-color 0.15s ease;
    }
    .carousel-file-picker:hover {
        border-color: var(--pine);
        background: var(--pine-soft);
    }
    .carousel-file-picker svg {
        width: 16px;
        height: 16px;
        color: var(--muted);
        flex-shrink: 0;
    }
    .carousel-file-picker span {
        font-size: 13.5px;
        color: var(--muted);
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .field-error {
        color: var(--danger);
        font-size: 12.5px;
        margin: 4px 0 0;
    }
    .about-store-form {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }
    .about-store-textarea {
        width: 100%;
        min-height: 110px;
        resize: vertical;
        line-height: 1.5;
    }
    .about-store-textarea.is-invalid {
        border-color: var(--danger);
        box-shadow: 0 0 0 3px rgba(185, 28, 28, 0.1);
    }
    .about-store-actions {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        flex-wrap: wrap;
    }
    .about-store-hint {
        margin: 0;
        font-size: 12px;
        color: var(--muted);
    }
</style>
@endsection

@section('content')
    <div class="media-container">
        <div class="media-header">
            <h2>Media</h2>
            <p>Manage how your store appears to students — your carousel banner and store description.</p>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="panel media-panel" id="carousel-image-section">
            <div class="media-panel-head">
                <h3 class="panel-title">Carousel Banner Image</h3>
                <p class="panel-sub">This image appears on the public concessionaires page carousel. Use a wide landscape photo (recommended: 1200&times;500 px).</p>
            </div>

            {{-- Current Image Preview --}}
            <div id="carousel-preview-wrap" class="carousel-preview">
                @if ($user->carousel_image)
                    <img id="carousel-preview-img"
                         src="{{ asset('storage/' . $user->carousel_image) }}"
                         alt="Carousel banner">
                    <div class="carousel-remove">
                        <form method="POST" action="{{ route('concessionaire.carousel-image.delete') }}" onsubmit="return confirm('Remove carousel image?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">Remove</button>
                        </form>
                    </div>
                @else
                    <div id="carousel-drop-zone" class="carousel-drop-zone" onclick="document.getElementById('carousel_image_input').click();">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <span class="carousel-drop-title">Click to upload a carousel image</span>
                        <span class="carousel-drop-sub">JPG, PNG, WebP — max 4 MB</span>
                    </div>
                @endif
            </div>

            {{-- Upload Form --}}
            <form method="POST" action="{{ route('concessionaire.carousel-image.update') }}" enctype="multipart/form-data" id="carousel-upload-form" class="carousel-form">
                @csrf
                <label for="carousel_image_input" class="carousel-file-picker">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                    <span id="carousel-file-label">{{ $user->carousel_image ? 'Click to replace image...' : 'Click to choose an image...' }}</span>
                    <input type="file" id="carousel_image_input" name="carousel_image" accept="image/jpeg,image/png,image/webp" style="display:none;" onchange="previewCarouselImage(this)">
                </label>
                <button type="submit" id="carousel-upload-btn" class="btn btn-primary" style="width:100%;">
                    Upload Image
                </button>
                @error('carousel_image')
                    <p class="field-error">{{ $message }}</p>
                @enderror
            </form>
        </div>

        <div class="panel media-panel">
            <div class="media-panel-head">
                <h3 class="panel-title">About Your Store</h3>
                <p class="panel-sub">This description appears across your concessionaire profile.</p>
            </div>
            <form method="POST" action="{{ route('concessionaire.update') }}" class="about-store-form">
                @csrf
                @method('PATCH')

                <textarea
                    id="description"
                    name="description"
                    maxlength="1000"
                    class="control about-store-textarea @error('description') is-invalid @enderror"
                    placeholder="Tell students what makes your store special..."
                >{{ old('description', $user->description) }}</textarea>

                @error('description')
                    <p class="field-error">{{ $message }}</p>
                @enderror

                <div class="about-store-actions">
                    <p class="about-store-hint">You can leave this blank if you prefer not to show a description.</p>
                    <button type="submit" class="btn btn-primary">Save Description</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    function previewCarouselImage(input) {
        if (!input.files || !input.files[0]) return;
        const file = input.files[0];
        const label = document.getElementById('carousel-file-label');
        if (label) label.textContent = file.name;
        const reader = new FileReader();
        reader.onload = function (e) {
            const wrap = document.getElementById('carousel-preview-wrap');
            if (!wrap) return;
            wrap.innerHTML = '<img src="' + e.target.result + '" alt="Carousel banner preview">';
        };
        reader.readAsDataURL(file);
    }
</script>
@endsection
