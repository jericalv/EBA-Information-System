@extends('concessionaire.layout')

@section('title', 'Media')

@section('extra-css')
<style>
    .media-container {
        max-width: 800px;
        margin: 32px auto;
        padding: 0 24px;
        display: flex;
        flex-direction: column;
        gap: 24px;
    }
    .media-header h2 {
        font-size: 24px;
        font-weight: 800;
        color: #0A5C2F;
        margin: 0;
    }
    .media-header p {
        margin: 4px 0 0;
        font-size: 14px;
        color: #64748b;
    }
    .alert-success {
        padding: 12px 16px;
        border-radius: 12px;
        background: rgba(10, 92, 47, 0.08);
        border: 1px solid rgba(10, 92, 47, 0.2);
        color: #0A5C2F;
        font-size: 14px;
        font-weight: 600;
    }
    .section-card {
        background: linear-gradient(to bottom, #ffffff 0%, #fafffe 100%);
        border: 1px solid #d4e8dc;
        border-radius: 20px;
        padding: 24px;
        box-shadow: 0 4px 12px rgba(6, 68, 32, 0.06), 0 12px 32px rgba(6, 68, 32, 0.08);
        transition: all 0.3s ease;
    }
    .section-card:hover {
        box-shadow: 0 8px 20px rgba(6, 68, 32, 0.08), 0 16px 40px rgba(6, 68, 32, 0.1);
        border-color: #c0dcc8;
    }
    .section-card-title {
        font-size: 18px;
        font-weight: 800;
        color: #0A5C2F;
        margin-bottom: 6px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .section-card-title::before {
        content: '';
        width: 4px;
        height: 20px;
        background: linear-gradient(to bottom, #0A5C2F, #1a8f50);
        border-radius: 4px;
    }
    .section-card-subtitle {
        font-size: 13px;
        color: #64748b;
        margin-bottom: 18px;
        font-weight: 500;
    }
    .about-store-form {
        display: flex;
        flex-direction: column;
        gap: 10px;
        margin-top: 8px;
    }
    .about-store-textarea {
        width: 100%;
        min-height: 110px;
        border: 1px solid #cfe1d6;
        border-radius: 12px;
        padding: 12px 14px;
        font-size: 14px;
        line-height: 1.5;
        color: #0f172a;
        background: #f9fdfb;
        resize: vertical;
        transition: border-color 0.2s ease, box-shadow 0.2s ease, background-color 0.2s ease;
    }
    .about-store-textarea:focus {
        outline: none;
        border-color: #0A5C2F;
        background: #ffffff;
        box-shadow: 0 0 0 3px rgba(10, 92, 47, 0.12);
    }
    .about-store-textarea.is-invalid {
        border-color: #dc2626;
        box-shadow: 0 0 0 2px rgba(220, 38, 38, 0.12);
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
        color: #64748b;
    }
    .about-store-save {
        border: 0;
        border-radius: 10px;
        padding: 9px 16px;
        font-size: 13px;
        font-weight: 700;
        color: #ffffff;
        background: linear-gradient(90deg, #0A5C2F 0%, #0e7b40 100%);
        cursor: pointer;
        box-shadow: 0 6px 14px rgba(10, 92, 47, 0.2);
        transition: transform 0.15s ease, box-shadow 0.15s ease;
    }
    .about-store-save:hover {
        transform: translateY(-1px);
        box-shadow: 0 8px 18px rgba(10, 92, 47, 0.28);
    }
    .about-store-save:active {
        transform: translateY(0);
    }
    .about-store-error {
        margin: 0;
        color: #b91c1c;
        font-size: 13px;
        font-weight: 600;
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
            <div class="alert-success">{{ session('success') }}</div>
        @endif

        <div class="section-card" id="carousel-image-section">
            <div class="section-card-title">Carousel Banner Image</div>
            <div class="section-card-subtitle">This image appears on the public concessionaires page carousel. Use a wide landscape photo (recommended: 1200×500 px).</div>

            <div style="display:flex;flex-direction:column;gap:20px;margin-top:16px;">
                {{-- Current Image Preview --}}
                <div id="carousel-preview-wrap" style="position:relative;border-radius:14px;overflow:hidden;background:#f1f5f9;border:2px dashed #c5dace;min-height:160px;display:flex;align-items:center;justify-content:center;">
                    @if ($user->carousel_image)
                        <img id="carousel-preview-img"
                             src="{{ asset('storage/' . $user->carousel_image) }}"
                             alt="Carousel banner"
                             style="width:100%;height:200px;object-fit:cover;display:block;border-radius:12px;">
                        <div style="position:absolute;top:10px;right:10px;">
                            <form method="POST" action="{{ route('concessionaire.carousel-image.delete') }}" onsubmit="return confirm('Remove carousel image?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" style="background:rgba(220,38,38,0.9);color:#fff;border:none;border-radius:8px;padding:6px 14px;font-size:13px;font-weight:700;cursor:pointer;backdrop-filter:blur(4px);">Remove</button>
                            </form>
                        </div>
                    @else
                        <div id="carousel-drop-zone" style="display:flex;flex-direction:column;align-items:center;gap:8px;padding:40px 24px;cursor:pointer;width:100%;" onclick="document.getElementById('carousel_image_input').click();">
                            <svg width="48" height="48" fill="none" stroke="#94a3b8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            <span style="font-size:14px;font-weight:600;color:#64748b;">Click to upload a carousel image</span>
                            <span style="font-size:12px;color:#94a3b8;">JPG, PNG, WebP — max 4 MB</span>
                        </div>
                    @endif
                </div>

                {{-- Upload Form --}}
                <form method="POST" action="{{ route('concessionaire.carousel-image.update') }}" enctype="multipart/form-data" id="carousel-upload-form">
                    @csrf
                    <div style="display:flex;flex-direction:column;gap:10px;">
                        <label for="carousel_image_input" style="display:block;cursor:pointer;">
                            <div style="display:flex;align-items:center;gap:10px;padding:12px 16px;border:2px solid #e2e8f0;border-radius:10px;background:#f8fafc;transition:all 0.2s;" onmouseover="this.style.borderColor='var(--green)'" onmouseout="this.style.borderColor='#e2e8f0'">
                                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color:#64748b;flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                                <span id="carousel-file-label" style="font-size:14px;color:#64748b;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $user->carousel_image ? 'Click to replace image...' : 'Click to choose an image...' }}</span>
                            </div>
                            <input type="file" id="carousel_image_input" name="carousel_image" accept="image/jpeg,image/png,image/webp" style="display:none;" onchange="previewCarouselImage(this)">
                        </label>
                        <button type="submit" id="carousel-upload-btn"
                                style="width:100%;padding:13px;background:#0A5C2F;color:#fff;border:none;border-radius:10px;font-size:15px;font-weight:700;font-family:inherit;cursor:pointer;transition:background 0.2s;"
                                onmouseover="this.style.background='#0D7A3E'" onmouseout="this.style.background='#0A5C2F'">
                            Upload Image
                        </button>
                    </div>
                    @error('carousel_image')
                        <p style="color:#dc2626;font-size:13px;margin-top:6px;">{{ $message }}</p>
                    @enderror
                </form>
            </div>
        </div>

        <div class="section-card">
            <div class="section-card-title">About Your Store</div>
            <div class="section-card-subtitle">This description appears across your concessionaire profile.</div>
            <form method="POST" action="{{ route('concessionaire.update') }}" class="about-store-form">
                @csrf
                @method('PATCH')

                <textarea
                    id="description"
                    name="description"
                    maxlength="1000"
                    class="about-store-textarea @error('description') is-invalid @enderror"
                    placeholder="Tell students what makes your store special..."
                >{{ old('description', $user->description) }}</textarea>

                @error('description')
                    <p class="about-store-error">{{ $message }}</p>
                @enderror

                <div class="about-store-actions">
                    <p class="about-store-hint">You can leave this blank if you prefer not to show a description.</p>
                    <button type="submit" class="about-store-save">Save Description</button>
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
            wrap.innerHTML = '<img src="' + e.target.result + '" style="width:100%;height:200px;object-fit:cover;display:block;border-radius:12px;">';
        };
        reader.readAsDataURL(file);
    }
</script>
@endsection
