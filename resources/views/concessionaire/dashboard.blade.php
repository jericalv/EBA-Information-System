@extends('concessionaire.layout')

@section('title', 'Dashboard')

@section('extra-css')
<style>
    .dashboard-shell {
        display: flex;
        flex-direction: column;
        gap: 24px;
    }
    .dashboard-page {
        display: flex;
        flex-direction: column;
        gap: 20px;
        background: linear-gradient(to bottom, #f8fdf9 0%, #f0f9f4 100%);
        min-height: calc(100vh - 80px);
        padding: 8px;
        margin: -8px;
    }
    @keyframes numberCountUp {
        from {
            opacity: 0;
            transform: scale(0.8);
        }
        to {
            opacity: 1;
            transform: scale(1);
        }
    }
    .stats-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 24px;
    }
    .stat-panel {
        background: linear-gradient(135deg, #ffffff 0%, #fafffe 50%, #f4fcf7 100%);
        border: 1px solid #d4e8dc;
        border-radius: 24px;
        padding: 28px;
        box-shadow: 0 4px 12px rgba(6, 68, 32, 0.06), 0 12px 32px rgba(6, 68, 32, 0.08);
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }
    .stat-panel::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #0A5C2F 0%, #1a8f50 50%, #0ea555 100%);
        transform: scaleX(0);
        transform-origin: left;
        transition: transform 0.5s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .stat-panel:hover {
        transform: translateY(-6px) scale(1.02);
        box-shadow: 0 12px 32px rgba(6, 68, 32, 0.12), 0 4px 16px rgba(6, 68, 32, 0.08);
        border-color: #b8d8c5;
    }
    .stat-panel:hover::before {
        transform: scaleX(1);
    }
    .stat-panel-header {
        display: flex;
        align-items: flex-start;
        justify-content: flex-end;
        margin-bottom: 16px;
    }
    .stat-panel-icon {
        width: 52px;
        height: 52px;
        border-radius: 14px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 26px;
        background: linear-gradient(135deg, rgba(10, 92, 47, 0.12), rgba(10, 92, 47, 0.06));
        border: 1px solid rgba(10, 92, 47, 0.18);
        box-shadow: 0 4px 12px rgba(10, 92, 47, 0.08);
        transition: all 0.3s ease;
    }
    .stat-panel:hover .stat-panel-icon {
        transform: rotate(-5deg) scale(1.05);
        box-shadow: 0 6px 16px rgba(10, 92, 47, 0.12);
    }
    .stat-trend-indicator {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 700;
        line-height: 1;
        transition: all 0.3s ease;
    }
    .stat-trend-indicator.positive {
        background: rgba(16, 185, 129, 0.12);
        color: #059669;
        border: 1px solid rgba(16, 185, 129, 0.2);
    }
    .stat-trend-indicator.negative {
        background: rgba(239, 68, 68, 0.12);
        color: #dc2626;
        border: 1px solid rgba(239, 68, 68, 0.2);
    }
    .stat-trend-indicator.neutral {
        background: rgba(107, 114, 128, 0.1);
        color: #6b7280;
        border: 1px solid rgba(107, 114, 128, 0.2);
    }
    .stat-panel-label {
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        color: #64748b;
        font-weight: 700;
        margin-bottom: 12px;
    }
    .stat-panel-value {
        font-size: 38px;
        line-height: 1;
        font-weight: 800;
        color: #0A5C2F;
        margin-bottom: 10px;
        letter-spacing: -0.03em;
    }
    .stat-panel-help {
        font-size: 12px;
        color: #64748b;
        font-weight: 500;
        margin-bottom: 12px;
    }
    .stat-sparkline {
        height: 32px;
        margin-top: 8px;
        position: relative;
    }
    .stat-sparkline canvas {
        width: 100%;
        height: 100%;
    }
    .dashboard-grid {
        display: grid;
        grid-template-columns: 1fr 1.2fr;
        gap: 24px;
    }
    .dashboard-charts-row {
        display: grid;
        grid-template-columns: 35% 65%;
        gap: 24px;
        align-items: stretch;
    }
    .charts-left-column {
        display: flex;
        flex-direction: column;
        gap: 24px;
        min-width: 0;
    }
    .charts-right-column {
        display: flex;
        flex-direction: column;
        height: 100%;
        min-width: 0;
    }
    .charts-right-column .section-card {
        flex: 1;
        height: 100%;
        display: flex;
        flex-direction: column;
    }
    #chart_review_trends {
        flex: 1;
        min-height: 250px;
        width: 100%;
    }
    .dashboard-charts .section-card {
        min-height: auto;
    }
    .chart-empty {
        margin: 6px 0 0;
        color: #64748b;
        font-size: 14px;
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
    .tabs-nav {
        overflow-y: hidden;
        scrollbar-width: none;
        -ms-overflow-style: none;
    }
    .tabs-nav::-webkit-scrollbar {
        width: 0;
        height: 0;
        display: none;
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
    .quick-actions {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
    }
    .quick-action {
        text-decoration: none;
        border: 1px solid #dbe5df;
        border-radius: 10px;
        padding: 12px;
        background: #f8fbf9;
        transition: all 0.18s ease;
    }
    .quick-action:hover {
        border-color: #a7c4b2;
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(6, 68, 32, 0.08);
    }
    .quick-action-title {
        color: var(--green-dark);
        font-size: 13px;
        font-weight: 800;
        margin-bottom: 2px;
    }
    .quick-action-help {
        color: #64748b;
        font-size: 12px;
        line-height: 1.45;
    }
    .rating-bars {
        display: grid;
        gap: 12px;
    }
    .rating-bar-row {
        display: grid;
        grid-template-columns: 36px 1fr auto;
        gap: 12px;
        align-items: center;
    }
    .rating-bar-label {
        font-size: 13px;
        color: #0A5C2F;
        font-weight: 800;
    }
    .rating-bar-track {
        height: 10px;
        background: linear-gradient(to right, #e8f3ec, #f0f7f3);
        border-radius: 999px;
        overflow: hidden;
        box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.08);
    }
    .rating-bar-fill {
        height: 100%;
        background: linear-gradient(90deg, #0A5C2F 0%, #1a8f50 50%, #0ea555 100%);
        box-shadow: 0 2px 4px rgba(10, 92, 47, 0.2);
        transition: width 0.6s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .rating-bar-count {
        font-size: 13px;
        color: #0A5C2F;
        font-weight: 700;
        min-width: 28px;
        text-align: right;
    }
    .list-stack {
        display: grid;
        gap: 14px;
    }
    .list-item {
        border: 1px solid #e5f0ea;
        border-radius: 12px;
        padding: 16px;
        background: linear-gradient(135deg, #ffffff 0%, #fafffe 100%);
        transition: all 0.2s ease;
    }
    .list-item:hover {
        border-color: #c5dace;
        box-shadow: 0 4px 12px rgba(6, 68, 32, 0.06);
        transform: translateY(-2px);
    }
    .list-item-head {
        display: flex;
        justify-content: space-between;
        gap: 8px;
        margin-bottom: 6px;
    }
    .list-item-title {
        font-size: 15px;
        font-weight: 700;
        color: #0A5C2F;
    }
    .list-item-meta {
        font-size: 12px;
        color: #64748b;
        font-weight: 600;
    }
    .list-item-text {
        font-size: 14px;
        color: #475569;
        line-height: 1.6;
    }
    .stars-inline {
        color: var(--gold);
        letter-spacing: 0.08em;
        font-size: 12px;
        font-weight: 700;
        white-space: nowrap;
    }
    .status-chip {
        display: inline-flex;
        align-items: center;
        border-radius: 999px;
        padding: 4px 12px;
        font-size: 11px;
        font-weight: 700;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08);
        transition: all 0.2s ease;
    }
    .status-chip:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.12);
    }
    .status-chip.available {
        background: linear-gradient(135deg, rgba(10, 92, 47, 0.15), rgba(10, 92, 47, 0.1));
        color: #0A5C2F;
        border: 1px solid rgba(10, 92, 47, 0.2);
    }
    .status-chip.hidden {
        background: linear-gradient(135deg, rgba(239, 68, 68, 0.15), rgba(239, 68, 68, 0.1));
        color: #dc2626;
        border: 1px solid rgba(239, 68, 68, 0.2);
    }
    .reviews-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 16px;
    }
    .reviews-summary {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }
    .summary-pill {
        display: inline-flex;
        align-items: center;
        padding: 8px 14px;
        border-radius: 999px;
        border: 1px solid #d4e8dc;
        background: linear-gradient(135deg, #ffffff, #f8fdf9);
        color: #0A5C2F;
        font-size: 12px;
        font-weight: 700;
        box-shadow: 0 2px 6px rgba(6, 68, 32, 0.08);
        transition: all 0.2s ease;
    }
    .summary-pill:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 10px rgba(6, 68, 32, 0.12);
        border-color: #c0dcc8;
    }
    .reviewer-line {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .reviewer-avatar {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        background: linear-gradient(135deg, #0A5C2F 0%, #1a8f50 100%);
        color: #fff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 15px;
        font-weight: 800;
        flex-shrink: 0;
        box-shadow: 0 4px 8px rgba(10, 92, 47, 0.2);
        border: 2px solid #fff;
    }
    .reviewer-name {
        font-size: 15px;
        font-weight: 700;
        color: #0A5C2F;
    }
    .empty-state-soft {
        text-align: center;
        border: 2px dashed #c5dace;
        background: linear-gradient(135deg, #f8fdf9 0%, #f0f9f4 100%);
        border-radius: 20px;
        padding: 48px 24px;
        transition: all 0.3s ease;
    }
    .empty-state-soft:hover {
        border-color: #a8c9b4;
        box-shadow: 0 4px 12px rgba(6, 68, 32, 0.06);
    }
    .empty-state-soft h3 {
        font-size: 22px;
        margin-bottom: 10px;
        color: #0A5C2F;
        font-weight: 800;
    }
    .empty-state-soft p {
        color: #64748b;
        font-size: 14px;
        line-height: 1.6;
    }
    @media (max-width: 992px) {
        .dashboard-grid {
            grid-template-columns: 1fr;
        }
    }
    @media (max-width: 768px) {
        .dashboard-charts-row {
            grid-template-columns: 1fr;
        }
        .quick-actions {
            grid-template-columns: 1fr;
        }
        .reviews-head {
            flex-direction: column;
            align-items: flex-start;
        }
        .stats-row {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection

@section('content')
    @php
        $currentTab = $activeTab ?? (request()->routeIs('concessionaire.reviews') ? 'reviews' : 'overview');
        $totalReviews = (int) ($stats['review_count'] ?? 0);
        $averageRating = (float) ($stats['rating'] ?? 0);
        $fiveStarShare = $totalReviews > 0
            ? round((($ratingBreakdown[5] ?? 0) / $totalReviews) * 100)
            : 0;
    @endphp

    <div class="dashboard-page">
        @if (!empty($showInactiveNotice) && $showInactiveNotice)
            <div class="alert alert-error">
                Your concessionaire account is currently inactive because the contract ended on
                <strong>{{ optional($contractPeriodEnd)->format('F d, Y') }}</strong>.
                Please contact admin to renew your contract.
            </div>
        @endif

        @if (!empty($unreadNotifications) && $unreadNotifications->isNotEmpty())
            @foreach ($unreadNotifications as $notification)
                @php
                    $message = $notification->data['message'] ?? 'Contract status update received.';
                    $daysRemaining = $notification->data['days_remaining'] ?? null;
                    $isExpired = is_null($daysRemaining);
                @endphp
                <div class="alert {{ $isExpired ? 'alert-error' : 'alert-warning' }} contract-banner" style="position:relative;padding-right:42px;">
                    {{ $message }}
                    <button type="button" onclick="this.parentElement.style.display='none'" style="position:absolute;right:10px;top:8px;background:none;border:none;color:inherit;font-size:20px;line-height:1;cursor:pointer;">&times;</button>
                </div>
            @endforeach
        @endif

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if ($currentTab === 'reviews')
            <div class="reviews-head">
                <div>
                    <h2 class="section-card-title" style="margin-bottom:2px;">Customer Reviews</h2>
                    <p class="section-card-subtitle" style="margin-bottom:0;">Feedback submitted by students for your business.</p>
                </div>
                <div class="reviews-summary">
                    <span class="summary-pill">Avg {{ number_format($averageRating, 1) }} ★</span>
                    <span class="summary-pill">{{ $totalReviews }} Total Reviews</span>
                    <span class="summary-pill">{{ $fiveStarShare }}% Five-Star Share</span>
                </div>
            </div>

            @if($customerReviews->isEmpty())
                <div class="empty-state-soft">
                    <h3>No Reviews Yet</h3>
                    <p>Reviews from students will appear here once they rate your business.</p>
                </div>
            @else
                <div class="list-stack">
                    @foreach($customerReviews as $review)
                        @php
                            $reviewerName = $review->user?->name ?? 'Student';
                            $reviewerInitial = strtoupper(substr($reviewerName, 0, 1));
                        @endphp
                        <div class="section-card" style="padding:14px 16px;">
                            <div class="list-item-head" style="margin-bottom:10px;">
                                <div class="reviewer-line">
                                    <span class="reviewer-avatar">{{ $reviewerInitial }}</span>
                                    <div>
                                        <div class="reviewer-name">{{ $reviewerName }}</div>
                                        <div class="list-item-meta">{{ $review->created_at?->diffForHumans() }}</div>
                                    </div>
                                </div>
                                <div class="stars-inline">{{ str_repeat('★', (int) $review->rating) }}{{ str_repeat('☆', 5 - (int) $review->rating) }}</div>
                            </div>
                            <div class="list-item-text">
                                {{ $review->comment ?: 'No written comment provided.' }}
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        @else
            <div class="dashboard-shell">
                <div class="stats-row">
                    <div class="stat-panel" data-stat="rating">
                        <div class="stat-panel-header">
                            <div class="stat-trend-indicator positive">
                                <svg width="12" height="12" viewBox="0 0 12 12" fill="none">
                                    <path d="M6 2L9 8L3 8L6 2Z" fill="currentColor"/>
                                </svg>
                                <span>8.5%</span>
                            </div>
                        </div>
                        <div class="stat-panel-label">Average Rating</div>
                        <div class="stat-panel-value" data-target="{{ number_format($averageRating, 1) }}">0.0</div>
                        <div class="stat-panel-help">Based on customer reviews</div>
                        <div class="stat-sparkline">
                            <canvas id="sparkline-rating" data-values="3.2,3.5,3.8,4.0,4.2,{{ number_format($averageRating, 1) }}"></canvas>
                        </div>
                    </div>
                    <div class="stat-panel" data-stat="reviews">
                        <div class="stat-panel-header">
                            <div class="stat-trend-indicator positive">
                                <svg width="12" height="12" viewBox="0 0 12 12" fill="none">
                                    <path d="M6 2L9 8L3 8L6 2Z" fill="currentColor"/>
                                </svg>
                                <span>12%</span>
                            </div>
                        </div>
                        <div class="stat-panel-label">Customer Reviews</div>
                        <div class="stat-panel-value" data-target="{{ $totalReviews }}">0</div>
                        <div class="stat-panel-help">Store-level feedback received</div>
                        <div class="stat-sparkline">
                            <canvas id="sparkline-reviews" data-values="{{ max(0, $totalReviews - 25) }},{{ max(0, $totalReviews - 20) }},{{ max(0, $totalReviews - 15) }},{{ max(0, $totalReviews - 10) }},{{ max(0, $totalReviews - 5) }},{{ $totalReviews }}"></canvas>
                        </div>
                    </div>
                    <div class="stat-panel" data-stat="products">
                        <div class="stat-panel-header">
                            <div class="stat-trend-indicator neutral">
                                <span>—</span>
                            </div>
                        </div>
                        <div class="stat-panel-label">Products</div>
                        <div class="stat-panel-value" data-target="{{ $totalProducts }}">0</div>
                        <div class="stat-panel-help">{{ $availableProducts }} available, {{ $unavailableProducts }} hidden</div>
                        <div class="stat-sparkline">
                            <canvas id="sparkline-products" data-values="{{ max(0, $totalProducts - 8) }},{{ max(0, $totalProducts - 6) }},{{ max(0, $totalProducts - 4) }},{{ max(0, $totalProducts - 2) }},{{ max(0, $totalProducts - 1) }},{{ $totalProducts }}"></canvas>
                        </div>
                    </div>
                    <div class="stat-panel" data-stat="payments">
                        <div class="stat-panel-header">
                            <div class="stat-trend-indicator positive">
                                <svg width="12" height="12" viewBox="0 0 12 12" fill="none">
                                    <path d="M6 2L9 8L3 8L6 2Z" fill="currentColor"/>
                                </svg>
                                <span>15%</span>
                            </div>
                        </div>
                        <div class="stat-panel-label">Payments This Month</div>
                        <div class="stat-panel-value" data-target="{{ $monthlyPaymentsTotal }}">0</div>
                        <div class="stat-panel-help">Recorded payment activity</div>
                        <div class="stat-sparkline">
                            <canvas id="sparkline-payments" data-values="{{ max(0, $monthlyPaymentsTotal * 0.6) }},{{ max(0, $monthlyPaymentsTotal * 0.7) }},{{ max(0, $monthlyPaymentsTotal * 0.8) }},{{ max(0, $monthlyPaymentsTotal * 0.9) }},{{ max(0, $monthlyPaymentsTotal * 0.95) }},{{ $monthlyPaymentsTotal }}"></canvas>
                        </div>
                    </div>
                </div>

                <div class="dashboard-charts-row">
                    <div class="charts-left-column">
                        <div class="section-card">
                            <div class="section-card-title">Ratings Snapshot</div>
                            <div class="section-card-subtitle">How students rate your store.</div>
                            <div class="rating-bars">
                                @for ($stars = 5; $stars >= 1; $stars--)
                                    @php
                                        $count = (int) ($ratingBreakdown[$stars] ?? 0);
                                        $percent = $totalReviews > 0 ? round(($count / $totalReviews) * 100) : 0;
                                    @endphp
                                    <div class="rating-bar-row">
                                        <span class="rating-bar-label">{{ $stars }}★</span>
                                        <div class="rating-bar-track">
                                            <div class="rating-bar-fill" data-width="{{ $percent }}"></div>
                                        </div>
                                        <span class="rating-bar-count">{{ $count }}</span>
                                    </div>
                                @endfor
                            </div>
                        </div>

                        <div class="section-card">
                            <div id="chart_product_categories"></div>
                        </div>
                    </div>

                    <div class="charts-right-column">
                        <div class="section-card">
                            <div id="chart_review_trends"></div>
                        </div>
                    </div>
                </div>

            </div>
        @endif
    </div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    document.querySelectorAll('.rating-bar-fill[data-width]').forEach(function (bar) {
        const width = Number(bar.getAttribute('data-width') || 0);
        bar.style.width = Math.max(0, Math.min(100, width)) + '%';
    });

    // Animated Number Counter
    function animateValue(element, start, end, duration, decimals = 0, prefix = '') {
        const range = end - start;
        const increment = range / (duration / 16);
        let current = start;
        const isPayment = element.closest('[data-stat="payments"]');
        
        const timer = setInterval(() => {
            current += increment;
            if ((increment > 0 && current >= end) || (increment < 0 && current <= end)) {
                current = end;
                clearInterval(timer);
            }
            
            let displayValue = current.toFixed(decimals);
            if (isPayment) {
                displayValue = '₱' + Number(current).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            } else if (decimals > 0) {
                displayValue = Number(current).toFixed(decimals);
            } else {
                displayValue = Math.floor(current);
            }
            
            element.textContent = prefix + displayValue;
        }, 16);
    }

    // Draw Sparkline Chart
    function drawSparkline(canvas, values, color = '#0A5C2F') {
        const ctx = canvas.getContext('2d');
        const dpr = window.devicePixelRatio || 1;
        const rect = canvas.getBoundingClientRect();
        
        canvas.width = rect.width * dpr;
        canvas.height = rect.height * dpr;
        ctx.scale(dpr, dpr);
        
        const width = rect.width;
        const height = rect.height;
        const padding = 4;
        const chartWidth = width - padding * 2;
        const chartHeight = height - padding * 2;
        
        const max = Math.max(...values, 1);
        const min = Math.min(...values, 0);
        const range = max - min || 1;
        
        const points = values.map((val, i) => {
            const x = padding + (i / (values.length - 1)) * chartWidth;
            const y = padding + chartHeight - ((val - min) / range) * chartHeight;
            return { x, y };
        });
        
        // Draw gradient fill
        const gradient = ctx.createLinearGradient(0, 0, 0, height);
        gradient.addColorStop(0, color + '40');
        gradient.addColorStop(1, color + '08');
        
        ctx.beginPath();
        ctx.moveTo(points[0].x, height - padding);
        points.forEach((point, i) => {
            if (i === 0) {
                ctx.lineTo(point.x, point.y);
            } else {
                const prev = points[i - 1];
                const cpx = (prev.x + point.x) / 2;
                ctx.quadraticCurveTo(prev.x, prev.y, cpx, (prev.y + point.y) / 2);
                ctx.quadraticCurveTo(cpx, (prev.y + point.y) / 2, point.x, point.y);
            }
        });
        ctx.lineTo(points[points.length - 1].x, height - padding);
        ctx.closePath();
        ctx.fillStyle = gradient;
        ctx.fill();
        
        // Draw line
        ctx.beginPath();
        points.forEach((point, i) => {
            if (i === 0) {
                ctx.moveTo(point.x, point.y);
            } else {
                const prev = points[i - 1];
                const cpx = (prev.x + point.x) / 2;
                ctx.quadraticCurveTo(prev.x, prev.y, cpx, (prev.y + point.y) / 2);
                ctx.quadraticCurveTo(cpx, (prev.y + point.y) / 2, point.x, point.y);
            }
        });
        ctx.strokeStyle = color;
        ctx.lineWidth = 2;
        ctx.lineCap = 'round';
        ctx.lineJoin = 'round';
        ctx.stroke();
        
        // Draw end point
        const lastPoint = points[points.length - 1];
        ctx.beginPath();
        ctx.arc(lastPoint.x, lastPoint.y, 3, 0, Math.PI * 2);
        ctx.fillStyle = color;
        ctx.fill();
        ctx.strokeStyle = '#ffffff';
        ctx.lineWidth = 2;
        ctx.stroke();
    }

    // Initialize stat cards
    setTimeout(() => {
        document.querySelectorAll('.stat-panel-value[data-target]').forEach(element => {
            const target = parseFloat(element.getAttribute('data-target'));
            const decimals = element.getAttribute('data-target').includes('.') ? 1 : 0;
            animateValue(element, 0, target, 1200, decimals);
        });
        
        // Draw sparklines
        const sparklineConfigs = [
            { id: 'sparkline-rating', color: '#f59e0b' },
            { id: 'sparkline-reviews', color: '#8b5cf6' },
            { id: 'sparkline-products', color: '#3b82f6' },
            { id: 'sparkline-payments', color: '#10b981' }
        ];
        
        sparklineConfigs.forEach(config => {
            const canvas = document.getElementById(config.id);
            if (canvas) {
                const valuesStr = canvas.getAttribute('data-values');
                const values = valuesStr.split(',').map(v => parseFloat(v) || 0);
                drawSparkline(canvas, values, config.color);
            }
        });
    }, 100);

        var reviewTrendData = @json($reviewTrendData);

        if (document.querySelector("#chart_review_trends") && typeof ApexCharts !== 'undefined') {
                var options = {
                    chart: {
                        height: "100%",
                        type: "line",
                        stacked: false,
                        width: "100%",
                        toolbar: { show: false }
                    },
                    stroke: {
                        width: [0, 2, 4],
                        curve: "smooth"
                    },
                    plotOptions: {
                        bar: { columnWidth: "50%" }
                    },
                    colors: ["#3b82f6", "#8b5cf6", "#f59e0b"],
                    series: [
                        {
                            name: "Product Reviews",
                            type: "column",
                            data: reviewTrendData.map(m => m.product_reviews)
                        },
                        {
                            name: "Store Reviews",
                            type: "area",
                            data: reviewTrendData.map(m => m.store_reviews)
                        },
                        {
                            name: "Avg Rating",
                            type: "line",
                            data: reviewTrendData.map(m => m.avg_rating)
                        }
                    ],
                    fill: {
                        opacity: [0.85, 0.25, 1],
                        gradient: {
                            inverseColors: false,
                            shade: "light",
                            type: "vertical",
                            opacityFrom: 0.85,
                            opacityTo: 0.55,
                            stops: [0, 100, 100, 100]
                        }
                    },
                    labels: reviewTrendData.map(m => m.month),
                    markers: { size: 0 },
                    xaxis: { type: "category" },
                    yaxis: [
                        {
                            title: { text: "Reviews" }
                        },
                        {
                            opposite: true,
                            title: { text: "Avg Rating" },
                            min: 0,
                            max: 5
                        }
                    ],
                    tooltip: {
                        shared: true,
                        intersect: false,
                        y: {
                            formatter: function (y) {
                                if (typeof y !== "undefined") {
                                    return y.toFixed(1);
                                }
                                return y;
                            }
                        }
                    },
                    grid: { borderColor: "#f1f1f1" },
                    title: {
                        text: "Review Trends — Last 6 Months",
                        align: "left",
                        style: { fontWeight: "500" }
                    }
                };

                var chart1 = new ApexCharts(document.querySelector("#chart_review_trends"), options);
                
                // Use a short delay so the CSS Grid wrapper calculates its absolute final width before drawing
                setTimeout(() => {
                    chart1.render();
                }, 50);
        }

        var categoryData = @json($productCategoryData);
        var categoryTarget = document.querySelector("#chart_product_categories");
        var categoryTotal = (categoryData.food || 0) + (categoryData.beverage || 0) + (categoryData.snack || 0);

        if (categoryTarget && typeof ApexCharts !== 'undefined') {
                if (categoryTotal === 0) {
                        categoryTarget.innerHTML = '<p class="chart-empty">No products yet.</p>';
                } else {
                        var options = {
                            chart: {
                                height: 280,
                                type: "pie",
                                width: "100%"
                            },
                            series: [
                                categoryData.food || 0,
                                categoryData.beverage || 0,
                                categoryData.snack || 0
                            ],
                            labels: ["Food", "Beverage", "Snack"],
                            colors: ["#3b82f6", "#f59e0b", "#ef4444"],
                            legend: {
                                show: true,
                                position: "bottom",
                                horizontalAlign: "center",
                                fontSize: "14px"
                            },
                            title: {
                                text: "Products by Category",
                                align: "left",
                                style: { fontWeight: "500" }
                            },
                            responsive: [
                                {
                                    breakpoint: 600,
                                    options: {
                                        chart: { height: 240 },
                                        legend: { show: false }
                                    }
                                }
                            ]
                        };

                        var chart2 = new ApexCharts(document.querySelector("#chart_product_categories"), options);
                        chart2.render();
                }
        }
</script>
@endsection
