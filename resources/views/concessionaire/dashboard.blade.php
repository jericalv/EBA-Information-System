@extends('concessionaire.layout')

@section('title', 'Dashboard')

@section('extra-css')
<style>
    .dashboard-page {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .stat-stars {
        font-size: 13px;
        letter-spacing: 3px;
        line-height: 1;
        color: var(--line-strong);
        user-select: none;
    }
    .stat-stars .filled {
        color: var(--star);
    }

    /* ---------- Charts ---------- */
    .dashboard-charts-row {
        display: grid;
        grid-template-columns: minmax(0, 5fr) minmax(0, 7fr);
        gap: 20px;
        align-items: stretch;
    }
    .charts-left-column {
        display: flex;
        flex-direction: column;
        gap: 20px;
        min-width: 0;
    }
    .charts-right-column {
        display: flex;
        flex-direction: column;
        min-width: 0;
    }
    .chart-panel {
        padding: 20px 22px;
        display: flex;
        flex-direction: column;
    }
    .charts-right-column .chart-panel {
        flex: 1;
    }
    .chart-panel-head {
        margin-bottom: 14px;
    }
    #chart_review_trends {
        flex: 1;
        min-height: 280px;
        width: 100%;
    }
    .chart-empty {
        margin: 8px 0 0;
        color: var(--muted);
        font-size: 13.5px;
    }

    /* ---------- Rating bars ---------- */
    .rating-bars {
        display: grid;
        gap: 11px;
    }
    .rating-bar-row {
        display: grid;
        grid-template-columns: 34px 1fr auto;
        gap: 12px;
        align-items: center;
    }
    .rating-bar-label {
        font-family: var(--font-mono);
        font-size: 12px;
        color: var(--muted);
    }
    .rating-bar-track {
        height: 8px;
        background: #EEF3EF;
        border-radius: 999px;
        overflow: hidden;
    }
    .rating-bar-fill {
        height: 100%;
        width: 0;
        background: var(--pine);
        border-radius: 999px;
        transition: width 0.6s ease;
    }
    .rating-bar-count {
        font-family: var(--font-mono);
        font-size: 12px;
        color: var(--ink);
        min-width: 26px;
        text-align: right;
        font-variant-numeric: tabular-nums;
    }

    .contract-banner {
        position: relative;
        padding-right: 42px;
    }
    .contract-banner-dismiss {
        position: absolute;
        right: 10px;
        top: 8px;
        background: none;
        border: none;
        color: inherit;
        font-size: 18px;
        line-height: 1;
        cursor: pointer;
    }

    @media (max-width: 1100px) {
        .dashboard-charts-row {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection

@section('content')
    @php
        $totalReviews = (int) ($stats['review_count'] ?? 0);
        $averageRating = (float) ($stats['rating'] ?? 0);
        $filledStars = (int) round($averageRating);
        $monthlyFee = (float) ($user->monthly_fee ?? 0);
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
                <div class="alert {{ $isExpired ? 'alert-error' : 'alert-warning' }} contract-banner">
                    {{ $message }}
                    <button type="button" class="contract-banner-dismiss" onclick="this.parentElement.style.display='none'" aria-label="Dismiss notification">&times;</button>
                </div>
            @endforeach
        @endif

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <section class="stat-ledger" aria-label="Store statistics">
            <article class="stat-cell">
                <span class="eyebrow">Average rating</span>
                <div class="stat-value-row">
                    <span class="stat-value" data-count-to="{{ number_format($averageRating, 1, '.', '') }}" data-decimals="1">0.0</span>
                    <span class="stat-unit">/ 5</span>
                </div>
                <div class="stat-stars" aria-hidden="true">@for ($i = 1; $i <= 5; $i++)<span class="{{ $i <= $filledStars ? 'filled' : '' }}">&#9733;</span>@endfor</div>
                <span class="stat-foot">From {{ number_format($totalReviews) }} store {{ \Illuminate\Support\Str::plural('review', $totalReviews) }}</span>
            </article>

            <article class="stat-cell">
                <span class="eyebrow">Store reviews</span>
                <div class="stat-value-row">
                    <span class="stat-value" data-count-to="{{ $totalReviews }}">0</span>
                </div>
                <span class="stat-foot">Plus {{ number_format($productReviewCount) }} {{ \Illuminate\Support\Str::plural('review', $productReviewCount) }} on products</span>
            </article>

            <article class="stat-cell">
                <span class="eyebrow">Products</span>
                <div class="stat-value-row">
                    <span class="stat-value" data-count-to="{{ $totalProducts }}">0</span>
                </div>
                <span class="stat-foot">{{ $availableProducts }} available &middot; {{ $unavailableProducts }} hidden</span>
            </article>

            <article class="stat-cell">
                <span class="eyebrow">Paid this month</span>
                <div class="stat-value-row">
                    <span class="stat-value" data-count-to="{{ number_format((float) $monthlyPaymentsTotal, 2, '.', '') }}" data-decimals="2" data-prefix="&#8369;">&#8369;0.00</span>
                </div>
                <span class="stat-foot">
                    @if ($monthlyFee > 0)
                        Monthly fee &#8369;{{ number_format($monthlyFee, 2) }}
                    @else
                        All recorded payments this month
                    @endif
                </span>
            </article>
        </section>

        <div class="dashboard-charts-row">
            <div class="charts-left-column">
                <div class="panel chart-panel">
                    <div class="chart-panel-head">
                        <h2 class="panel-title">Ratings snapshot</h2>
                        <p class="panel-sub">How students rate your store.</p>
                    </div>
                    <div class="rating-bars">
                        @for ($stars = 5; $stars >= 1; $stars--)
                            @php
                                $count = (int) ($ratingBreakdown[$stars] ?? 0);
                                $percent = $totalReviews > 0 ? round(($count / $totalReviews) * 100) : 0;
                            @endphp
                            <div class="rating-bar-row">
                                <span class="rating-bar-label">{{ $stars }}&#9733;</span>
                                <div class="rating-bar-track">
                                    <div class="rating-bar-fill" data-width="{{ $percent }}"></div>
                                </div>
                                <span class="rating-bar-count">{{ $count }}</span>
                            </div>
                        @endfor
                    </div>
                </div>

                <div class="panel chart-panel">
                    <div class="chart-panel-head">
                        <h2 class="panel-title">Products by category</h2>
                        <p class="panel-sub">Current catalog composition.</p>
                    </div>
                    <div id="chart_product_categories"></div>
                </div>
            </div>

            <div class="charts-right-column">
                <div class="panel chart-panel">
                    <div class="chart-panel-head">
                        <h2 class="panel-title">Review trends</h2>
                        <p class="panel-sub">Reviews and average rating over the last 6 months.</p>
                    </div>
                    <div id="chart_review_trends"></div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    document.querySelectorAll('.rating-bar-fill[data-width]').forEach(function (bar) {
        const width = Number(bar.getAttribute('data-width') || 0);
        bar.style.width = Math.max(0, Math.min(100, width)) + '%';
    });

    document.querySelectorAll('.stat-value[data-count-to]').forEach(function (element) {
        const target = parseFloat(element.getAttribute('data-count-to')) || 0;
        const decimals = parseInt(element.getAttribute('data-decimals') || '0', 10);
        const prefix = element.getAttribute('data-prefix') || '';

        const formatValue = function (value) {
            return prefix + Number(value).toLocaleString('en-US', {
                minimumFractionDigits: decimals,
                maximumFractionDigits: decimals
            });
        };

        if (prefersReducedMotion) {
            element.textContent = formatValue(target);
            return;
        }

        const duration = 900;
        const start = performance.now();
        const tick = function (now) {
            const progress = Math.min(1, (now - start) / duration);
            const eased = 1 - Math.pow(1 - progress, 3);
            element.textContent = formatValue(target * eased);
            if (progress < 1) {
                requestAnimationFrame(tick);
            }
        };
        requestAnimationFrame(tick);
    });

    const chartFont = getComputedStyle(document.documentElement).getPropertyValue('--font-ui').trim() || 'Manrope, sans-serif';
    const chartBase = {
        fontFamily: chartFont,
        foreColor: '#66756C',
        toolbar: { show: false },
        animations: { enabled: !prefersReducedMotion }
    };

    var reviewTrendData = @json($reviewTrendData);

    if (document.querySelector('#chart_review_trends') && typeof ApexCharts !== 'undefined') {
        var trendOptions = {
            chart: Object.assign({}, chartBase, {
                height: '100%',
                type: 'line',
                stacked: false,
                width: '100%'
            }),
            stroke: {
                width: [0, 0, 2.5],
                curve: 'smooth'
            },
            plotOptions: {
                bar: { columnWidth: '45%', borderRadius: 3, borderRadiusApplication: 'end' }
            },
            colors: ['#9CC5AC', '#0A5C2F', '#B45309'],
            series: [
                {
                    name: 'Product Reviews',
                    type: 'column',
                    data: reviewTrendData.map(m => m.product_reviews)
                },
                {
                    name: 'Store Reviews',
                    type: 'column',
                    data: reviewTrendData.map(m => m.store_reviews)
                },
                {
                    name: 'Avg Rating',
                    type: 'line',
                    data: reviewTrendData.map(m => m.avg_rating)
                }
            ],
            fill: { opacity: [1, 1, 1], type: 'solid' },
            dataLabels: { enabled: false },
            labels: reviewTrendData.map(m => m.month),
            markers: { size: 0, hover: { size: 4 } },
            xaxis: {
                type: 'category',
                labels: { style: { fontSize: '11px' } },
                axisBorder: { color: '#E2E8E3' },
                axisTicks: { show: false }
            },
            yaxis: [
                {
                    seriesName: ['Product Reviews', 'Store Reviews'],
                    labels: {
                        style: { fontSize: '11px' },
                        formatter: function (value) { return Math.round(value); }
                    },
                    title: { text: 'Reviews', style: { fontSize: '11px', fontWeight: 500 } }
                },
                {
                    opposite: true,
                    seriesName: 'Avg Rating',
                    min: 0,
                    max: 5,
                    tickAmount: 5,
                    labels: { style: { fontSize: '11px' } },
                    title: { text: 'Avg Rating', style: { fontSize: '11px', fontWeight: 500 } }
                }
            ],
            legend: {
                position: 'bottom',
                horizontalAlign: 'center',
                fontSize: '12px',
                markers: { size: 5, shape: 'circle' },
                itemMargin: { horizontal: 10 }
            },
            tooltip: {
                shared: true,
                intersect: false,
                y: {
                    formatter: function (y) {
                        if (typeof y !== 'undefined') {
                            return y.toFixed(1);
                        }
                        return y;
                    }
                }
            },
            grid: { borderColor: '#EDF2EE' }
        };

        var trendChart = new ApexCharts(document.querySelector('#chart_review_trends'), trendOptions);

        // Short delay so the CSS Grid wrapper settles on its final width before drawing
        setTimeout(function () {
            trendChart.render();
        }, 50);
    }

    var categoryData = @json($productCategoryData);
    var categoryTarget = document.querySelector('#chart_product_categories');
    var categoryTotal = (categoryData.food || 0) + (categoryData.beverage || 0) + (categoryData.snack || 0);

    if (categoryTarget && typeof ApexCharts !== 'undefined') {
        if (categoryTotal === 0) {
            categoryTarget.innerHTML = '<p class="chart-empty">No products yet. Add your first product to see this chart.</p>';
        } else {
            var categoryOptions = {
                chart: Object.assign({}, chartBase, {
                    height: 250,
                    type: 'donut',
                    width: '100%'
                }),
                series: [
                    categoryData.food || 0,
                    categoryData.beverage || 0,
                    categoryData.snack || 0
                ],
                labels: ['Food', 'Beverage', 'Snack'],
                colors: ['#0A5C2F', '#6FAF8D', '#B45309'],
                stroke: { colors: ['#ffffff'], width: 2 },
                dataLabels: { enabled: false },
                plotOptions: {
                    pie: {
                        donut: {
                            size: '74%',
                            labels: {
                                show: true,
                                name: { fontSize: '12px', color: '#66756C', offsetY: 18 },
                                value: {
                                    fontSize: '24px',
                                    fontWeight: 600,
                                    color: '#1A2B21',
                                    offsetY: -12,
                                    formatter: function (value) { return value; }
                                },
                                total: {
                                    show: true,
                                    label: 'Products',
                                    fontSize: '12px',
                                    color: '#66756C',
                                    formatter: function (w) {
                                        return w.globals.seriesTotals.reduce(function (a, b) { return a + b; }, 0);
                                    }
                                }
                            }
                        }
                    }
                },
                legend: {
                    show: true,
                    position: 'bottom',
                    horizontalAlign: 'center',
                    fontSize: '12px',
                    markers: { size: 5, shape: 'circle' },
                    itemMargin: { horizontal: 10 }
                },
                responsive: [
                    {
                        breakpoint: 600,
                        options: {
                            chart: { height: 220 }
                        }
                    }
                ]
            };

            var categoryChart = new ApexCharts(categoryTarget, categoryOptions);
            categoryChart.render();
        }
    }
</script>
@endsection
