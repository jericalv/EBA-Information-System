@auth
    @php
        $pendingBannerUser = auth()->user();
        $showPendingBanner = $pendingBannerUser->role === 'concessionaire' && ! $pendingBannerUser->is_approved;
    @endphp

    @if ($showPendingBanner)
        <style>
            .pending-app-banner {
                position: sticky;
                top: 0;
                z-index: 1200;
                background: #fffbeb;
                color: #854d0e;
                border-bottom: 1px solid #fcd34d;
                padding: 10px 14px;
                font-size: 13px;
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 12px;
            }
            .pending-app-banner a {
                color: #92400e;
                text-decoration: underline;
                font-weight: 700;
            }
            .pending-app-banner button {
                border: 0;
                background: transparent;
                color: #92400e;
                font-size: 18px;
                line-height: 1;
                cursor: pointer;
                padding: 0 2px;
            }
            .pending-app-banner.hidden {
                display: none;
            }
        </style>
        <div class="pending-app-banner" id="pendingAppBanner" role="status" aria-live="polite">
            <div>
                Your application is pending. Complete your documents in
                <a href="{{ route('application') }}">My Application</a>.
            </div>
            <button type="button" aria-label="Dismiss" onclick="dismissPendingAppBanner()">&times;</button>
        </div>
        <script>
            (function () {
                var key = 'pending_app_banner_dismissed';
                var banner = document.getElementById('pendingAppBanner');
                if (!banner) {
                    return;
                }
                if (window.sessionStorage.getItem(key) === '1') {
                    banner.classList.add('hidden');
                }
                window.dismissPendingAppBanner = function () {
                    banner.classList.add('hidden');
                    window.sessionStorage.setItem(key, '1');
                };
            })();
        </script>
    @endif
@endauth
