@extends('admin.layout')

@section('title', 'Site Settings')

@section('extra-css')
<style>
    /* ===== SITE SETTINGS CMS ===== */
    .ss-head {
        display:flex; align-items:flex-start; justify-content:space-between;
        gap:16px; flex-wrap:wrap; margin-bottom:20px;
    }
    .ss-head h2 { font-size:22px; font-weight:800; color:#0f172a; }
    .ss-head p  { font-size:14px; color:#64748b; margin-top:4px; max-width:620px; line-height:1.55; }

    /* Tabbed layout — section nav + single visible panel */
    .ss-layout {
        display:grid; grid-template-columns:248px minmax(0,1fr); gap:24px; align-items:start;
        padding-bottom:84px; /* room for sticky save bar */
    }
    .ss-tabs {
        position:sticky; top:84px;
        background:#fff; border:1px solid #e2e8f0; border-radius:14px;
        padding:10px; display:flex; flex-direction:column; gap:3px;
        box-shadow:0 1px 3px rgba(0,0,0,.04);
    }
    .ss-tab {
        display:flex; align-items:center; gap:11px; width:100%;
        padding:11px 13px; border:none; background:none; cursor:pointer;
        border-radius:10px; font-family:inherit; font-size:14px; font-weight:600;
        color:#475569; text-align:left; transition:background .15s, color .15s;
    }
    .ss-tab svg { width:18px; height:18px; flex-shrink:0; }
    .ss-tab:hover { background:#f1f5f9; color:#0f172a; }
    .ss-tab.active { background:rgba(10,92,47,.1); color:#0A5C2F; }
    .ss-tab-num {
        margin-left:auto; font-size:11px; font-weight:700; color:#94a3b8;
        background:#f1f5f9; border-radius:999px; padding:1px 8px; min-width:22px; text-align:center;
    }
    .ss-tab.active .ss-tab-num { background:#0A5C2F; color:#fff; }

    .ss-panel { display:none; }
    .ss-panel.active { display:block; animation:ssFade .18s ease; }
    @keyframes ssFade { from { opacity:0; transform:translateY(4px); } to { opacity:1; transform:none; } }

    .ss-panel-head { margin-bottom:14px; }
    .ss-panel-head h3 { font-size:18px; font-weight:800; color:#0f172a; }
    .ss-panel-head p  { font-size:13px; color:#64748b; margin-top:3px; line-height:1.5; }

    .ss-card {
        background:#fff; border:1px solid #e2e8f0; border-radius:14px;
        padding:22px; box-shadow:0 1px 3px rgba(0,0,0,.04);
    }

    .ss-grid { display:grid; gap:18px; }
    .ss-grid-2 { grid-template-columns:repeat(2, minmax(0,1fr)); }
    .ss-grid-3 { grid-template-columns:repeat(3, minmax(0,1fr)); }
    .ss-span-2 { grid-column:1 / -1; }
    @media (max-width: 900px) {
        .ss-grid-2, .ss-grid-3 { grid-template-columns:1fr; }
    }

    .ss-field label {
        display:block; font-size:13px; font-weight:600; color:#374151; margin-bottom:6px;
    }
    .ss-input, .ss-textarea {
        width:100%; padding:10px 12px; border:1px solid #e2e8f0; border-radius:9px;
        font-size:14px; font-family:inherit; color:#1e293b; background:#fff; outline:none;
        transition:border-color .15s, box-shadow .15s;
    }
    .ss-input:focus, .ss-textarea:focus {
        border-color:#0A5C2F; box-shadow:0 0 0 3px rgba(10,92,47,.1);
    }
    .ss-textarea { resize:vertical; min-height:84px; }

    .ss-sub {
        background:#f8fafc; border:1px solid #e2e8f0; border-radius:12px; padding:16px;
        display:flex; flex-direction:column; gap:12px;
    }
    .ss-sub-label {
        font-size:11px; font-weight:700; text-transform:uppercase;
        letter-spacing:.5px; color:#94a3b8;
    }

    .ss-img-card {
        background:#f8fafc; border:1px solid #e2e8f0; border-radius:12px;
        padding:14px; display:flex; flex-direction:column; gap:10px;
    }
    .ss-img-preview {
        width:100%; aspect-ratio:16/10; border-radius:10px; overflow:hidden;
        background:#e2e8f0 repeating-linear-gradient(45deg,#eef2f7 0 10px,#e7ecf3 10px 20px);
        display:flex; align-items:center; justify-content:center;
    }
    .ss-img-preview img { width:100%; height:100%; object-fit:cover; display:block; }
    .ss-img-caption { font-size:13px; font-weight:600; color:#374151; }
    .ss-file { font-size:12px; color:#64748b; }
    .ss-file::file-selector-button {
        margin-right:10px; padding:7px 12px; border:1px solid #e2e8f0; border-radius:8px;
        background:#fff; color:#0A5C2F; font-weight:600; font-size:12px; cursor:pointer;
        font-family:inherit; transition:background .15s;
    }
    .ss-file::file-selector-button:hover { background:#f1f5f9; }
    .ss-hint { font-size:11px; color:#94a3b8; }

    .ss-note {
        display:flex; gap:10px; align-items:flex-start;
        background:rgba(10,92,47,.05); border:1px solid rgba(10,92,47,.18);
        border-radius:10px; padding:14px 16px; font-size:13px; color:#374151; line-height:1.55;
    }
    .ss-note svg { width:18px; height:18px; color:#0A5C2F; flex-shrink:0; margin-top:1px; }

    /* Sticky save bar — always visible across tabs */
    .ss-save-bar {
        position:fixed; bottom:0; right:0; left:var(--sidebar-w); z-index:50;
        display:flex; align-items:center; justify-content:space-between; gap:12px;
        padding:14px 28px; background:rgba(255,255,255,.92);
        backdrop-filter:blur(6px); -webkit-backdrop-filter:blur(6px);
        border-top:1px solid #e2e8f0; box-shadow:0 -6px 18px rgba(15,23,42,.05);
    }
    .ss-save-hint { font-size:13px; color:#64748b; }
    .ss-save-hint strong { color:#0f172a; }

    @media (max-width: 860px) {
        .ss-layout { grid-template-columns:1fr; }
        .ss-tabs {
            position:static; flex-direction:row; overflow-x:auto;
            -webkit-overflow-scrolling:touch;
        }
        .ss-tab { white-space:nowrap; }
        .ss-tab-num { display:none; }
    }
    /* Match the layout's sidebar breakpoint so the bar never underlaps the sidebar */
    @media (max-width: 768px) {
        .ss-save-bar { left:0; }
    }
</style>
@endsection

@section('content')


@if($errors->any())
    <div class="alert alert-error">{{ $errors->first() }}</div>
@endif

@php
    // Image fields => default landing-page asset used when nothing is uploaded yet.
    $img = function ($key, $default) {
        return \App\Models\SiteSetting::image($key, asset($default));
    };
@endphp

<form method="POST" action="{{ route('admin.site-settings.update') }}" enctype="multipart/form-data">
    @csrf

    <div class="ss-layout">
        {{-- ===================== SECTION NAV ===================== --}}
        <nav class="ss-tabs" aria-label="Settings sections">
            <button type="button" class="ss-tab active" data-tab="hero">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                Hero
            </button>
            <button type="button" class="ss-tab" data-tab="features">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                Features
                <span class="ss-tab-num">3</span>
            </button>
            <button type="button" class="ss-tab" data-tab="showcase">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                Showcase
            </button>
            <button type="button" class="ss-tab" data-tab="about">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                About
            </button>
            <button type="button" class="ss-tab" data-tab="mission">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342"/></svg>
                Mission &amp; Vision
            </button>
            <button type="button" class="ss-tab" data-tab="values">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z"/></svg>
                Core Values
                <span class="ss-tab-num">5</span>
            </button>
            <button type="button" class="ss-tab" data-tab="faq">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                FAQ
                <span class="ss-tab-num">4</span>
            </button>
        </nav>

        {{-- ===================== PANELS ===================== --}}
        <div class="ss-panels">
            {{-- HERO --}}
            <section class="ss-panel active" id="panel-hero">
                <div class="ss-card">
                    <div class="ss-grid ss-grid-2">
                        <div class="ss-field">
                            <label>Hero Title</label>
                            <input type="text" name="hero_title" class="ss-input"
                                value="{{ old('hero_title', $settings['hero_title'] ?? '') }}"
                                placeholder="Your Campus Marketplace, All in One Place">
                            <p class="ss-hint" style="margin-top:6px;">HTML is allowed here (e.g. coloured words).</p>
                        </div>
                        <div class="ss-field">
                            <label>Hero Subtitle</label>
                            <textarea name="hero_subtitle" class="ss-textarea" rows="3"
                                placeholder="Browse products, discover concessionaires...">{{ old('hero_subtitle', $settings['hero_subtitle'] ?? '') }}</textarea>
                        </div>
                        <div class="ss-span-2">
                            <div class="ss-img-card" style="max-width:420px;">
                                <span class="ss-img-caption">Hero Image</span>
                                <div class="ss-img-preview">
                                    <img id="preview_hero_image" src="{{ $img('hero_image', 'images/vector-1-transparent.png') }}" alt="Hero">
                                </div>
                                <input type="file" name="hero_image" accept="image/*" class="ss-file"
                                    onchange="ssPreview(this,'preview_hero_image')">
                                <span class="ss-hint">PNG/JPG, up to 4&nbsp;MB. Leave empty to keep the current image.</span>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {{-- FEATURES --}}
            <section class="ss-panel" id="panel-features">
                <div class="ss-card">
                    <div class="ss-field" style="margin-bottom:18px;">
                        <label>Section Title</label>
                        <input type="text" name="features_title" class="ss-input"
                            value="{{ old('features_title', $settings['features_title'] ?? '') }}"
                            placeholder="Everything Your Office Needs, In One System">
                    </div>
                    <div class="ss-grid ss-grid-3">
                        @foreach([1,2,3] as $i)
                        <div class="ss-sub">
                            <span class="ss-sub-label">Feature {{ $i }}</span>
                            <div class="ss-field">
                                <label>Title</label>
                                <input type="text" name="feature_{{ $i }}_title" class="ss-input"
                                    value="{{ old('feature_'.$i.'_title', $settings['feature_'.$i.'_title'] ?? '') }}">
                            </div>
                            <div class="ss-field">
                                <label>Description</label>
                                <textarea name="feature_{{ $i }}_desc" class="ss-textarea" rows="4">{{ old('feature_'.$i.'_desc', $settings['feature_'.$i.'_desc'] ?? '') }}</textarea>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </section>

            {{-- SHOWCASE --}}
            <section class="ss-panel" id="panel-showcase">
                <div class="ss-card">
                    <div class="ss-grid ss-grid-2" style="margin-bottom:18px;">
                        <div class="ss-field">
                            <label>Showcase Heading</label>
                            <input type="text" name="showcase_title" class="ss-input"
                                value="{{ old('showcase_title', $settings['showcase_title'] ?? '') }}"
                                placeholder="Campus Concessionaires">
                        </div>
                        <div class="ss-field">
                            <label>Showcase Description</label>
                            <textarea name="showcase_subtitle" class="ss-textarea" rows="3">{{ old('showcase_subtitle', $settings['showcase_subtitle'] ?? '') }}</textarea>
                        </div>
                    </div>
                    <div class="ss-note">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <div>
                            The carousel cards are generated automatically from your <strong>approved &amp; active concessionaires</strong>.
                            Each approved concessionaire appears with the carousel image they upload and their business name &mdash;
                            the more you approve, the more slides show here. Only the heading and description above are edited from this page.
                        </div>
                    </div>
                </div>
            </section>

            {{-- ABOUT --}}
            <section class="ss-panel" id="panel-about">
                <div class="ss-card">
                    <div class="ss-img-card" style="max-width:420px;">
                        <span class="ss-img-caption">About Image</span>
                        <div class="ss-img-preview">
                            <img id="preview_about_image" src="{{ $img('about_image', 'images/sample picture.jpg') }}" alt="About">
                        </div>
                        <input type="file" name="about_image" accept="image/*" class="ss-file"
                            onchange="ssPreview(this,'preview_about_image')">
                        <span class="ss-hint">PNG/JPG, up to 4&nbsp;MB. Leave empty to keep the current image.</span>
                    </div>
                </div>
            </section>

            {{-- MISSION & VISION --}}
            <section class="ss-panel" id="panel-mission">

                <div class="ss-card">
                    <div class="ss-grid ss-grid-2">
                        <div class="ss-field">
                            <label>University Vision</label>
                            <textarea name="vision" class="ss-textarea" rows="6">{{ old('vision', $settings['vision'] ?? '') }}</textarea>
                        </div>
                        <div class="ss-field">
                            <label>University Mission</label>
                            <textarea name="mission" class="ss-textarea" rows="6">{{ old('mission', $settings['mission'] ?? '') }}</textarea>
                        </div>
                    </div>
                </div>
            </section>

            {{-- CORE VALUES --}}
            <section class="ss-panel" id="panel-values">
                <div class="ss-card">
                    <div class="ss-grid" style="grid-template-columns:repeat(auto-fit,minmax(180px,1fr));">
                        @foreach([1,2,3,4,5] as $i)
                        <div class="ss-field">
                            <label>Core Value {{ $i }}</label>
                            <input type="text" name="core_value_{{ $i }}" class="ss-input"
                                value="{{ old('core_value_'.$i, $settings['core_value_'.$i] ?? '') }}"
                                placeholder="e.g. Integrity">
                        </div>
                        @endforeach
                    </div>
                </div>
            </section>

            {{-- FAQ --}}
            <section class="ss-panel" id="panel-faq">
                <div class="ss-card">
                    <div class="ss-grid ss-grid-2">
                        @foreach([1,2,3,4] as $i)
                        <div class="ss-sub">
                            <span class="ss-sub-label">FAQ {{ $i }}</span>
                            <div class="ss-field">
                                <label>Question</label>
                                <input type="text" name="faq_{{ $i }}_question" class="ss-input"
                                    value="{{ old('faq_'.$i.'_question', $settings['faq_'.$i.'_question'] ?? '') }}"
                                    placeholder="Leave empty to hide this FAQ">
                            </div>
                            <div class="ss-field">
                                <label>Answer</label>
                                <textarea name="faq_{{ $i }}_answer" class="ss-textarea" rows="3">{{ old('faq_'.$i.'_answer', $settings['faq_'.$i.'_answer'] ?? '') }}</textarea>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </section>
        </div>
    </div>

    {{-- Sticky save bar --}}
    <div class="ss-save-bar">
        <span class="ss-save-hint">You're editing <strong id="ss-active-label">Hero</strong> &mdash; changes to every section save together.</span>
        <button type="submit" class="btn btn-green" style="padding:11px 26px;font-size:14px;">
            <svg style="width:16px;height:16px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            Save Changes
        </button>
    </div>
</form>
@endsection

@section('scripts')
<script>
    function ssPreview(input, targetId) {
        if (!input.files || !input.files[0]) return;
        var reader = new FileReader();
        reader.onload = function (e) {
            document.getElementById(targetId).src = e.target.result;
        };
        reader.readAsDataURL(input.files[0]);
    }

    (function () {
        var tabs = Array.prototype.slice.call(document.querySelectorAll('.ss-tab'));
        var panels = Array.prototype.slice.call(document.querySelectorAll('.ss-panel'));
        var activeLabel = document.getElementById('ss-active-label');
        var STORAGE_KEY = 'ssActiveTab';

        function activate(name) {
            var matched = false;
            tabs.forEach(function (t) {
                var on = t.dataset.tab === name;
                t.classList.toggle('active', on);
                if (on && activeLabel) {
                    activeLabel.textContent = t.textContent.trim().replace(/\s+\d+$/, '');
                }
            });
            panels.forEach(function (p) {
                var on = p.id === 'panel-' + name;
                p.classList.toggle('active', on);
                if (on) matched = true;
            });
            if (matched) {
                try { localStorage.setItem(STORAGE_KEY, name); } catch (e) {}
            }
            return matched;
        }

        tabs.forEach(function (t) {
            t.addEventListener('click', function () { activate(t.dataset.tab); });
        });

        var initial = 'hero';
        try {
            var saved = localStorage.getItem(STORAGE_KEY);
            if (saved && document.getElementById('panel-' + saved)) initial = saved;
        } catch (e) {}
        activate(initial);
    })();
</script>
@endsection
