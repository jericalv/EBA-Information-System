@extends('concessionaire.layout')

@section('title', 'Media')

@section('extra-css')
<style>
    .media-container {
        max-width: 820px;
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
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 16px;
    }
    .media-count-pill {
        flex-shrink: 0;
        font-family: var(--font-mono);
        font-size: 11px;
        font-weight: 600;
        letter-spacing: 0.04em;
        color: var(--muted);
        background: var(--pine-soft);
        border: 1px solid var(--line);
        border-radius: 999px;
        padding: 4px 11px;
        white-space: nowrap;
    }

    /* Gallery grid */
    .banner-gallery {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
        gap: 12px;
    }
    .banner-tile {
        position: relative;
        border-radius: 9px;
        overflow: hidden;
        border: 1px solid var(--line);
        background: var(--paper);
        aspect-ratio: 16 / 10;
        box-shadow: var(--shadow-card);
    }
    .banner-tile img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }
    .banner-tile-index {
        position: absolute;
        top: 8px;
        left: 8px;
        font-family: var(--font-mono);
        font-size: 10.5px;
        font-weight: 600;
        color: #fff;
        background: rgba(16, 26, 20, 0.72);
        border-radius: 5px;
        padding: 3px 7px;
        letter-spacing: 0.04em;
    }
    .banner-tile-remove {
        position: absolute;
        top: 8px;
        right: 8px;
    }
    .banner-tile-remove button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 28px;
        height: 28px;
        padding: 0;
        border: none;
        border-radius: 6px;
        background: rgba(16, 26, 20, 0.72);
        color: #fff;
        cursor: pointer;
        transition: background-color 0.15s ease;
    }
    .banner-tile-remove button:hover { background: var(--danger); }
    .banner-tile-remove svg { width: 15px; height: 15px; }

    /* Drop / upload zone */
    .banner-uploader {
        margin-top: 16px;
    }
    .banner-drop {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 8px;
        padding: 30px 24px;
        border: 1.5px dashed var(--line-strong);
        border-radius: 9px;
        background: var(--paper);
        cursor: pointer;
        text-align: center;
        transition: border-color 0.15s ease, background-color 0.15s ease;
    }
    .banner-drop:hover, .banner-drop.dragover {
        border-color: var(--pine);
        background: var(--pine-soft);
    }
    .banner-drop svg { width: 34px; height: 34px; color: var(--faint); }
    .banner-drop-title { font-size: 13.5px; font-weight: 600; color: var(--muted); }
    .banner-drop-sub { font-size: 12px; color: var(--faint); }
    .banner-drop.is-hidden { display: none; }

    .banner-selected {
        display: none;
        flex-direction: column;
        gap: 12px;
        margin-top: 4px;
    }
    .banner-selected.is-visible { display: flex; }
    .banner-preview-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
        gap: 10px;
    }
    .banner-preview-grid .banner-tile { aspect-ratio: 16 / 10; }
    .banner-actions {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }
    .banner-actions .btn { flex: 0 0 auto; }
    .banner-hint {
        margin: 0;
        font-size: 12px;
        color: var(--muted);
    }

    .banner-empty {
        padding: 34px 24px;
        text-align: center;
        border: 1px dashed var(--line-strong);
        border-radius: 9px;
        color: var(--muted);
        font-size: 13px;
        background: var(--paper);
    }

    .field-error {
        color: var(--danger);
        font-size: 12.5px;
        margin: 8px 0 0;
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
            <p>Manage how your store appears to students — your banner carousel and store description.</p>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-error">{{ session('error') }}</div>
        @endif

        <div class="panel media-panel" id="carousel-image-section">
            <div class="media-panel-head">
                <div>
                    <h3 class="panel-title">Banner Carousel</h3>
                    <p class="panel-sub">These images rotate as the banner on your public page. Use wide landscape photos (recommended 1600&times;600 px). Up to 8 images.</p>
                </div>
                <span class="media-count-pill">{{ $user->carouselMedia->count() }} / 8</span>
            </div>

            @if ($user->carouselMedia->count() > 0)
                <div class="banner-gallery">
                    @foreach ($user->carouselMedia as $index => $media)
                        <div class="banner-tile">
                            <span class="banner-tile-index">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span>
                            <img src="{{ asset('storage/' . $media->path) }}" alt="Banner image {{ $index + 1 }}">
                            <div class="banner-tile-remove">
                                <form method="POST" action="{{ route('concessionaire.carousel-image.delete') }}" onsubmit="return confirm('Remove this banner image?');">
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" name="media_id" value="{{ $media->id }}">
                                    <button type="submit" title="Remove image" aria-label="Remove image">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="banner-empty">No banner images yet. Add a few photos below — they’ll appear as a carousel on your public page.</div>
            @endif

            {{-- Upload Form --}}
            @if ($user->carouselMedia->count() < 8)
                <form method="POST" action="{{ route('concessionaire.carousel-image.update') }}" enctype="multipart/form-data" id="carousel-upload-form" class="banner-uploader">
                    @csrf
                    <div id="banner-drop" class="banner-drop">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <span class="banner-drop-title">Click to upload or drag &amp; drop images</span>
                        <span class="banner-drop-sub">JPG, PNG, WebP — max 4 MB each. Select multiple.</span>
                    </div>

                    <input type="file" id="carousel_images_input" name="carousel_images[]" accept="image/jpeg,image/png,image/webp" multiple style="display:none;">

                    <div id="banner-selected" class="banner-selected">
                        <div id="banner-preview-grid" class="banner-preview-grid"></div>
                        <div class="banner-actions">
                            <button type="submit" id="carousel-upload-btn" class="btn btn-primary">Upload <span id="upload-count"></span></button>
                            <button type="button" id="banner-clear" class="btn btn-secondary">Clear</button>
                            <p class="banner-hint" id="banner-hint"></p>
                        </div>
                    </div>

                    @error('carousel_images')
                        <p class="field-error">{{ $message }}</p>
                    @enderror
                    @error('carousel_images.*')
                        <p class="field-error">{{ $message }}</p>
                    @enderror
                </form>
            @endif
        </div>

        <div class="panel media-panel">
            <div class="media-panel-head">
                <div>
                    <h3 class="panel-title">About Your Store</h3>
                    <p class="panel-sub">This description appears across your concessionaire profile.</p>
                </div>
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
    (function () {
        const input = document.getElementById('carousel_images_input');
        if (!input) return;

        const drop = document.getElementById('banner-drop');
        const selected = document.getElementById('banner-selected');
        const grid = document.getElementById('banner-preview-grid');
        const clearBtn = document.getElementById('banner-clear');
        const uploadCount = document.getElementById('upload-count');
        const hint = document.getElementById('banner-hint');
        const remaining = {{ 8 - $user->carouselMedia->count() }};

        function renderPreviews() {
            const files = Array.from(input.files || []);
            grid.innerHTML = '';

            if (files.length === 0) {
                selected.classList.remove('is-visible');
                drop.classList.remove('is-hidden');
                return;
            }

            drop.classList.add('is-hidden');
            selected.classList.add('is-visible');
            uploadCount.textContent = files.length + (files.length === 1 ? ' image' : ' images');
            hint.textContent = files.length > remaining
                ? ('Only ' + remaining + ' will be saved — banner limit is 8.')
                : '';

            files.forEach((file, i) => {
                const tile = document.createElement('div');
                tile.className = 'banner-tile';
                const idx = document.createElement('span');
                idx.className = 'banner-tile-index';
                idx.textContent = String(i + 1).padStart(2, '0');
                const img = document.createElement('img');
                img.alt = file.name;
                const reader = new FileReader();
                reader.onload = (e) => { img.src = e.target.result; };
                reader.readAsDataURL(file);
                tile.appendChild(idx);
                tile.appendChild(img);
                grid.appendChild(tile);
            });
        }

        drop.addEventListener('click', () => input.click());
        input.addEventListener('change', renderPreviews);
        clearBtn.addEventListener('click', () => { input.value = ''; renderPreviews(); });

        ['dragenter', 'dragover'].forEach(evt =>
            drop.addEventListener(evt, (e) => { e.preventDefault(); drop.classList.add('dragover'); })
        );
        ['dragleave', 'drop'].forEach(evt =>
            drop.addEventListener(evt, (e) => { e.preventDefault(); drop.classList.remove('dragover'); })
        );
        drop.addEventListener('drop', (e) => {
            if (e.dataTransfer && e.dataTransfer.files.length) {
                input.files = e.dataTransfer.files;
                renderPreviews();
            }
        });
    })();
</script>
@endsection
