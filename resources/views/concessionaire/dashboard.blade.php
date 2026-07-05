@extends('concessionaire.layout')

@section('title', 'Dashboard')

@section('extra-css')
<style>
    .dashboard-page {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    /* ---------- Page head ---------- */
    .page-head {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
        margin-bottom: 2px;
    }
    .page-title {
        font-size: 21px;
        font-weight: 800;
        letter-spacing: -0.02em;
        color: var(--ink);
        margin-top: 4px;
        line-height: 1.15;
    }
    .page-date {
        font-family: var(--font-mono);
        font-size: 12px;
        color: var(--muted);
        white-space: nowrap;
        padding-bottom: 3px;
    }

    /* ---------- Stat cards ---------- */
    .dstat-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 16px;
    }
    .dstat-card {
        background: #fff;
        border: 1px solid var(--line);
        border-radius: 14px;
        padding: 20px;
        display: flex;
        flex-direction: column;
        gap: 8px;
        box-shadow: var(--shadow-card);
        min-width: 0;
    }
    .dstat-label {
        font-family: var(--font-mono);
        font-size: 10.5px;
        font-weight: 600;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        color: var(--muted);
    }
    .dstat-value-row {
        display: flex;
        align-items: baseline;
        gap: 5px;
    }
    .dstat-value {
        font-family: var(--font-mono);
        font-size: 30px;
        font-weight: 600;
        line-height: 1.1;
        letter-spacing: -0.03em;
        color: var(--ink);
        font-variant-numeric: tabular-nums;
    }
    .dstat-unit {
        font-family: var(--font-mono);
        font-size: 13px;
        color: var(--faint);
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
    .dstat-foot-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        margin-top: auto;
        padding-top: 12px;
        border-top: 1px solid var(--line);
    }
    .dstat-foot {
        font-size: 12px;
        color: var(--muted);
        min-width: 0;
    }
    .dstat-chip {
        font-family: var(--font-mono);
        font-size: 11px;
        font-weight: 600;
        padding: 3px 8px;
        border-radius: 5px;
        white-space: nowrap;
        flex-shrink: 0;
    }
    .dstat-chip.up { background: #E5F3EA; color: var(--pine); }
    .dstat-chip.down { background: #FBEAEA; color: #B3261E; }
    .dstat-chip.neutral { background: #F0F2F0; color: var(--muted); }

    /* ---------- Chart panels ---------- */
    .dashboard-charts-row {
        display: grid;
        grid-template-columns: minmax(0, 5fr) minmax(0, 7fr);
        gap: 20px;
        align-items: start;
    }
    .charts-left-column,
    .charts-right-column {
        display: flex;
        flex-direction: column;
        gap: 20px;
        min-width: 0;
    }
    .chart-panel {
        padding: 20px 22px;
        border-radius: 14px;
    }
    .chart-panel-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 16px;
        transition: margin-bottom 0.3s ease;
    }
    .chart-panel.is-collapsed .chart-panel-head {
        margin-bottom: 0;
    }
    .chart-menu-wrap {
        position: relative;
        flex-shrink: 0;
    }
    .chart-menu-btn {
        width: 30px;
        height: 30px;
        border: 1px solid transparent;
        border-radius: 6px;
        background: transparent;
        color: var(--muted);
        font-size: 18px;
        line-height: 1;
        cursor: pointer;
        transition: background-color 0.15s ease, color 0.15s ease;
    }
    .chart-menu-btn:hover,
    .chart-menu-btn[aria-expanded="true"] {
        background: var(--pine-soft);
        color: var(--ink);
    }
    .chart-menu-btn:focus-visible {
        outline: 2px solid rgba(10, 92, 47, 0.45);
        outline-offset: 2px;
    }
    .chart-menu {
        position: absolute;
        right: 0;
        top: 36px;
        min-width: 176px;
        z-index: 40;
        padding: 5px;
    }
    .chart-menu[hidden] {
        display: none;
    }
    .chart-menu-item {
        display: block;
        width: 100%;
        border: 0;
        border-radius: 6px;
        background: none;
        padding: 8px 11px;
        font-family: var(--font-ui);
        font-size: 13px;
        font-weight: 600;
        color: var(--ink);
        text-align: left;
        cursor: pointer;
        transition: background-color 0.12s ease;
    }
    .chart-menu-item:hover {
        background: var(--pine-soft);
    }
    .chart-menu-divider {
        height: 1px;
        margin: 5px 4px;
        background: var(--line);
    }
    .chart-body {
        display: grid;
        grid-template-rows: 1fr;
        transition: grid-template-rows 0.3s ease;
    }
    .chart-body-inner {
        overflow: hidden;
        min-height: 0;
        opacity: 1;
        transition: opacity 0.25s ease;
    }
    .chart-panel.is-collapsed .chart-body {
        grid-template-rows: 0fr;
    }
    .chart-panel.is-collapsed .chart-body-inner {
        opacity: 0;
    }
    .chart-empty {
        margin: 8px 0 0;
        color: var(--muted);
        font-size: 13.5px;
    }

    /* ---------- Rating bars ---------- */
    .rating-bars {
        display: grid;
        gap: 12px;
        padding-top: 2px;
    }
    .rating-bar-row {
        display: grid;
        grid-template-columns: 34px 1fr 66px;
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
        transition: width 0.9s cubic-bezier(0.22, 1, 0.36, 1);
    }
    .rating-bar-count {
        font-family: var(--font-mono);
        font-size: 12px;
        color: var(--ink);
        text-align: right;
        font-variant-numeric: tabular-nums;
        white-space: nowrap;
    }
    .rating-bar-count small {
        color: var(--muted);
        font-size: 10.5px;
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
        .dstat-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
        .dashboard-charts-row {
            grid-template-columns: 1fr;
        }
    }
    @media (max-width: 640px) {
        .dstat-grid {
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

        <div class="page-head">
            <div>
                <span class="eyebrow"></span>
                <h1 class="page-title"></h1>
            </div>
            <span class="page-date">{{ now()->format('l, F d, Y') }}</span>
        </div>

        <section class="dstat-grid" aria-label="Store statistics">
            <article class="dstat-card">
                <span class="dstat-label">Average rating</span>
                <div class="dstat-value-row">
                    <span class="dstat-value" data-count-to="{{ number_format($averageRating, 1, '.', '') }}" data-decimals="1">0.0</span>
                    <span class="dstat-unit">/ 5</span>
                </div>
                <div class="stat-stars" aria-hidden="true">@for ($i = 1; $i <= 5; $i++)<span class="{{ $i <= $filledStars ? 'filled' : '' }}">&#9733;</span>@endfor</div>
                <div class="dstat-foot-row">
                    <span class="dstat-foot">From {{ number_format($totalReviews) }} store {{ \Illuminate\Support\Str::plural('review', $totalReviews) }}</span>
                    <span class="dstat-chip" id="chip_rating"></span>
                </div>
            </article>

            <article class="dstat-card">
                <span class="dstat-label">Store reviews</span>
                <div class="dstat-value-row">
                    <span class="dstat-value" data-count-to="{{ $totalReviews }}">0</span>
                </div>
                <div class="dstat-foot-row">
                    <span class="dstat-foot">Plus {{ number_format($productReviewCount) }} {{ \Illuminate\Support\Str::plural('review', $productReviewCount) }} on products</span>
                    <span class="dstat-chip" id="chip_reviews"></span>
                </div>
            </article>

            <article class="dstat-card">
                <span class="dstat-label">Products</span>
                <div class="dstat-value-row">
                    <span class="dstat-value" data-count-to="{{ $totalProducts }}">0</span>
                </div>
                <div class="dstat-foot-row">
                    <span class="dstat-foot">{{ $availableProducts }} available &middot; {{ $unavailableProducts }} hidden</span>
                    <span class="dstat-chip" id="chip_products"></span>
                </div>
            </article>

            <article class="dstat-card">
                <span class="dstat-label">Paid this month</span>
                <div class="dstat-value-row">
                    <span class="dstat-value" data-count-to="{{ number_format((float) $monthlyPaymentsTotal, 2, '.', '') }}" data-decimals="2" data-prefix="&#8369;">&#8369;0.00</span>
                </div>
                <div class="dstat-foot-row">
                    <span class="dstat-foot">
                        @if ($monthlyFee > 0)
                            Monthly fee &#8369;{{ number_format($monthlyFee, 2) }}
                        @else
                            All recorded payments this month
                        @endif
                    </span>
                    <span class="dstat-chip" id="chip_payments"></span>
                </div>
            </article>
        </section>

        <div class="dashboard-charts-row">
            <div class="charts-left-column">
                <div class="panel chart-panel" id="panel_ratings">
                    <div class="chart-panel-head">
                        <div>
                            <h2 class="panel-title">Ratings snapshot</h2>
                            <p class="panel-sub">How students rate your store.</p>
                        </div>
                        <div class="chart-menu-wrap">
                            <button type="button" class="chart-menu-btn" data-menu-btn aria-haspopup="true" aria-expanded="false" aria-label="Ratings snapshot options">&#8943;</button>
                            <div class="chart-menu pop" data-menu hidden></div>
                        </div>
                    </div>
                    <div class="chart-body">
                        <div class="chart-body-inner">
                            <div class="rating-bars">
                                @for ($stars = 5; $stars >= 1; $stars--)
                                    @php
                                        $count = (int) ($ratingBreakdown[$stars] ?? 0);
                                        $percent = $totalReviews > 0 ? round(($count / $totalReviews) * 100) : 0;
                                    @endphp
                                    <div class="rating-bar-row">
                                        <span class="rating-bar-label">{{ $stars }}&#9733;</span>
                                        <div class="rating-bar-track">
                                            <div class="rating-bar-fill" data-width="{{ $percent }}" style="transition-delay: {{ (5 - $stars) * 90 }}ms;"></div>
                                        </div>
                                        <span class="rating-bar-count">{{ $count }} <small>&middot; {{ $percent }}%</small></span>
                                    </div>
                                @endfor
                            </div>
                        </div>
                    </div>
                </div>

                <div class="panel chart-panel" id="panel_categories">
                    <div class="chart-panel-head">
                        <div>
                            <h2 class="panel-title">Products by category</h2>
                            <p class="panel-sub">Current catalog composition.</p>
                        </div>
                        <div class="chart-menu-wrap">
                            <button type="button" class="chart-menu-btn" data-menu-btn aria-haspopup="true" aria-expanded="false" aria-label="Products by category options">&#8943;</button>
                            <div class="chart-menu pop" data-menu hidden></div>
                        </div>
                    </div>
                    <div class="chart-body">
                        <div class="chart-body-inner">
                            <div id="chart_product_categories"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="charts-right-column">
                <div class="panel chart-panel" id="panel_trends">
                    <div class="chart-panel-head">
                        <div>
                            <h2 class="panel-title">Review trends</h2>
                            <p class="panel-sub">Reviews and average rating over the last 6 months.</p>
                        </div>
                        <div class="chart-menu-wrap">
                            <button type="button" class="chart-menu-btn" data-menu-btn aria-haspopup="true" aria-expanded="false" aria-label="Review trends options">&#8943;</button>
                            <div class="chart-menu pop" data-menu hidden></div>
                        </div>
                    </div>
                    <div class="chart-body">
                        <div class="chart-body-inner">
                            <div id="chart_review_trends"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    requestAnimationFrame(function () {
        document.querySelectorAll('.rating-bar-fill[data-width]').forEach(function (bar) {
            const width = Number(bar.getAttribute('data-width') || 0);
            bar.style.width = Math.max(0, Math.min(100, width)) + '%';
        });
    });

    document.querySelectorAll('.dstat-value[data-count-to]').forEach(function (element) {
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

    // ---------- Month-over-month chips (real data from statSparklines) ----------
    var statSparklines = @json($statSparklines);

    function setTrendChip(id, data, options) {
        options = options || {};
        var el = document.getElementById(id);
        if (!el) return;
        if (!Array.isArray(data) || data.length < 2) {
            el.style.display = 'none';
            return;
        }

        var delta = data[data.length - 1] - data[data.length - 2];
        var cls = delta > 0 ? 'up' : (delta < 0 ? 'down' : 'neutral');
        var arrow = delta > 0 ? '↑' : (delta < 0 ? '↓' : '—');
        var magnitude = Math.abs(delta).toLocaleString('en-US', {
            minimumFractionDigits: options.decimals || 0,
            maximumFractionDigits: options.decimals || 0
        });

        el.classList.add(cls);
        el.textContent = delta === 0 ? '— steady' : arrow + ' ' + (options.prefix || '') + magnitude;
        el.title = 'Compared with last month';
    }

    setTrendChip('chip_rating', statSparklines.rating, { decimals: 1 });
    setTrendChip('chip_reviews', statSparklines.reviews);
    setTrendChip('chip_products', statSparklines.products);
    setTrendChip('chip_payments', statSparklines.payments, { prefix: '₱', decimals: 2 });

    // ---------- Chart panel menus: collapse + downloads ----------
    function downloadCSV(filename, headers, rows) {
        var esc = function (value) {
            value = String(value === null || value === undefined ? '' : value);
            return /[",\n]/.test(value) ? '"' + value.replace(/"/g, '""') + '"' : value;
        };
        var csv = [headers].concat(rows).map(function (row) {
            return row.map(esc).join(',');
        }).join('\r\n');
        var blob = new Blob(['﻿' + csv], { type: 'text/csv;charset=utf-8;' });
        var link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = filename;
        link.click();
        URL.revokeObjectURL(link.href);
    }

    function downloadPNG(getChart, filename) {
        var chart = typeof getChart === 'function' ? getChart() : getChart;
        if (!chart) return;
        chart.dataURI({ scale: 2 }).then(function (output) {
            var link = document.createElement('a');
            link.href = output.imgURI;
            link.download = filename;
            link.click();
        });
    }

    function closeAllChartMenus() {
        document.querySelectorAll('.chart-menu').forEach(function (menu) {
            menu.hidden = true;
        });
        document.querySelectorAll('.chart-menu-btn').forEach(function (btn) {
            btn.setAttribute('aria-expanded', 'false');
        });
    }

    function wireChartPanel(panelId, actions) {
        var panel = document.getElementById(panelId);
        if (!panel) return;

        var btn = panel.querySelector('[data-menu-btn]');
        var menu = panel.querySelector('[data-menu]');
        if (!btn || !menu) return;

        var collapseItem = document.createElement('button');
        collapseItem.type = 'button';
        collapseItem.className = 'chart-menu-item';
        collapseItem.textContent = 'Collapse';
        collapseItem.addEventListener('click', function () {
            var collapsed = panel.classList.toggle('is-collapsed');
            collapseItem.textContent = collapsed ? 'Expand' : 'Collapse';
            closeAllChartMenus();
        });
        menu.appendChild(collapseItem);

        var downloads = (actions || []).filter(function (action) { return action.available !== false; });
        if (downloads.length) {
            var divider = document.createElement('div');
            divider.className = 'chart-menu-divider';
            menu.appendChild(divider);

            downloads.forEach(function (action) {
                var item = document.createElement('button');
                item.type = 'button';
                item.className = 'chart-menu-item';
                item.textContent = action.label;
                item.addEventListener('click', function () {
                    closeAllChartMenus();
                    action.run();
                });
                menu.appendChild(item);
            });
        }

        btn.addEventListener('click', function (event) {
            event.stopPropagation();
            var willOpen = menu.hidden;
            closeAllChartMenus();
            if (willOpen) {
                menu.hidden = false;
                btn.setAttribute('aria-expanded', 'true');
            }
        });
    }

    document.addEventListener('click', function (event) {
        if (!event.target.closest('.chart-menu-wrap')) {
            closeAllChartMenus();
        }
    });
    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            closeAllChartMenus();
        }
    });

    // ---------- Charts ----------
    const chartFont = getComputedStyle(document.documentElement).getPropertyValue('--font-ui').trim() || 'Manrope, sans-serif';
    const chartBase = {
        fontFamily: chartFont,
        foreColor: '#66756C',
        toolbar: { show: false },
        animations: { enabled: !prefersReducedMotion }
    };

    var reviewTrendData = @json($reviewTrendData);
    var ratingBreakdown = @json($ratingBreakdown);
    var trendChart = null;
    var categoryChart = null;

    if (document.querySelector('#chart_review_trends') && typeof ApexCharts !== 'undefined') {
        var trendOptions = {
            chart: Object.assign({}, chartBase, {
                height: 360,
                type: 'line',
                stacked: false,
                width: '100%'
            }),
            stroke: {
                width: [0, 0, 2.5],
                curve: 'smooth',
                lineCap: 'round'
            },
            plotOptions: {
                bar: { columnWidth: '42%', borderRadius: 4, borderRadiusApplication: 'end' }
            },
            colors: ['#9CC5AC', '#0A5C2F', '#D97706'],
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
            markers: {
                size: [0, 0, 3.5],
                strokeColors: '#fff',
                strokeWidth: 2,
                hover: { size: 5 }
            },
            xaxis: {
                type: 'category',
                labels: { style: { fontSize: '11px', fontWeight: 600 } },
                axisBorder: { show: false },
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
                position: 'top',
                horizontalAlign: 'right',
                fontSize: '12px',
                fontWeight: 600,
                markers: { size: 5, shape: 'circle' },
                itemMargin: { horizontal: 10 },
                offsetY: -4
            },
            tooltip: {
                shared: true,
                intersect: false,
                theme: 'dark',
                style: { fontSize: '12px' },
                y: [
                    { formatter: function (v) { return Math.round(v) + ' reviews'; } },
                    { formatter: function (v) { return Math.round(v) + ' reviews'; } },
                    { formatter: function (v) { return (v || 0).toFixed(1) + ' / 5'; } }
                ]
            },
            grid: {
                borderColor: '#EDF2EE',
                strokeDashArray: 4,
                padding: { left: 6, right: 6 }
            }
        };

        trendChart = new ApexCharts(document.querySelector('#chart_review_trends'), trendOptions);

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
                    height: 260,
                    type: 'donut',
                    width: '100%'
                }),
                series: [
                    categoryData.food || 0,
                    categoryData.beverage || 0,
                    categoryData.snack || 0
                ],
                labels: ['Food', 'Beverage', 'Snack'],
                colors: ['#0A5C2F', '#6FAF8D', '#D97706'],
                stroke: { colors: ['#ffffff'], width: 3 },
                dataLabels: { enabled: false },
                plotOptions: {
                    pie: {
                        expandOnClick: false,
                        donut: {
                            size: '76%',
                            labels: {
                                show: true,
                                name: { fontSize: '12px', color: '#66756C', offsetY: 18 },
                                value: {
                                    fontSize: '26px',
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
                tooltip: {
                    theme: 'dark',
                    style: { fontSize: '12px' },
                    y: {
                        formatter: function (value) {
                            return value + ' ' + (value === 1 ? 'product' : 'products');
                        }
                    }
                },
                legend: {
                    show: true,
                    position: 'bottom',
                    horizontalAlign: 'center',
                    fontSize: '12px',
                    fontWeight: 600,
                    markers: { size: 5, shape: 'circle' },
                    itemMargin: { horizontal: 10 }
                },
                responsive: [
                    {
                        breakpoint: 600,
                        options: {
                            chart: { height: 230 }
                        }
                    }
                ]
            };

            categoryChart = new ApexCharts(categoryTarget, categoryOptions);
            categoryChart.render();
        }
    }

    // ---------- Wire the three-dot menus ----------
    wireChartPanel('panel_ratings', [
        {
            label: 'Download CSV',
            run: function () {
                var rows = [5, 4, 3, 2, 1].map(function (stars) {
                    var count = Number(ratingBreakdown[stars] || 0);
                    var total = [5, 4, 3, 2, 1].reduce(function (sum, s) { return sum + Number(ratingBreakdown[s] || 0); }, 0);
                    var percent = total > 0 ? Math.round((count / total) * 100) : 0;
                    return [stars + ' stars', count, percent + '%'];
                });
                downloadCSV('ratings-snapshot.csv', ['Rating', 'Count', 'Share'], rows);
            }
        }
    ]);

    wireChartPanel('panel_categories', [
        {
            label: 'Download PNG',
            available: categoryTotal > 0,
            run: function () { downloadPNG(function () { return categoryChart; }, 'products-by-category.png'); }
        },
        {
            label: 'Download CSV',
            run: function () {
                downloadCSV('products-by-category.csv', ['Category', 'Products'], [
                    ['Food', categoryData.food || 0],
                    ['Beverage', categoryData.beverage || 0],
                    ['Snack', categoryData.snack || 0]
                ]);
            }
        }
    ]);

    wireChartPanel('panel_trends', [
        {
            label: 'Download PNG',
            run: function () { downloadPNG(function () { return trendChart; }, 'review-trends.png'); }
        },
        {
            label: 'Download CSV',
            run: function () {
                downloadCSV(
                    'review-trends.csv',
                    ['Month', 'Product Reviews', 'Store Reviews', 'Avg Rating'],
                    reviewTrendData.map(function (m) {
                        return [m.month, m.product_reviews, m.store_reviews, m.avg_rating];
                    })
                );
            }
        }
    ]);
</script>
@endsection
