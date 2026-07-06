@extends('faculty.layout')

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
    .page-head .page-title {
        margin: 4px 0 0;
        font-size: 21px;
        line-height: 1.15;
    }
    .page-date {
        font-family: var(--font-mono);
        font-size: 12px;
        color: var(--muted);
        white-space: nowrap;
    }
    .page-head-actions {
        display: flex;
        align-items: center;
        gap: 14px;
    }
    .report-menu {
        top: 44px;
        min-width: 190px;
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
    .dstat-value {
        font-family: var(--font-mono);
        font-size: 30px;
        font-weight: 600;
        line-height: 1.1;
        letter-spacing: -0.03em;
        color: var(--ink);
        font-variant-numeric: tabular-nums;
    }
    .dstat-foot {
        margin-top: auto;
        padding-top: 12px;
        border-top: 1px solid var(--line);
        font-size: 12px;
        color: var(--muted);
    }

    /* ---------- Chart panels ---------- */
    .dashboard-charts-row {
        display: grid;
        grid-template-columns: minmax(0, 7fr) minmax(0, 5fr);
        gap: 20px;
        align-items: start;
    }
    .chart-panel {
        padding: 20px 22px;
        border-radius: 14px;
        min-width: 0;
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
        outline: 2px solid rgba(31, 41, 55, 0.40);
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
    .chart-panel.no-anim .chart-panel-head,
    .chart-panel.no-anim .chart-body,
    .chart-panel.no-anim .chart-body-inner {
        transition: none;
    }
    /* Reserve chart height before ApexCharts draws, so the grid track never
       animates from 0 to full height on load (the collapse-then-expand flash). */
    #chart_monthly_applications,
    #chart_status_distribution,
    #chart_revenue,
    #chart_units_by_type {
        min-height: 320px;
    }
    /* Belt-and-suspenders: no collapse transitions until charts have mounted. */
    .dashboard-page.charts-loading .chart-panel-head,
    .dashboard-page.charts-loading .chart-body,
    .dashboard-page.charts-loading .chart-body-inner {
        transition: none !important;
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
<script>
    // Pre-paint restore: collapse saved panels via CSS before they render, so a
    // hard refresh never flashes the expanded state before the main script runs.
    (function () {
        try {
            var panelIds = ['panel_monthly', 'panel_status', 'panel_revenue', 'panel_units', 'panel_top_items'];
            var css = '';
            panelIds.forEach(function (id) {
                if (localStorage.getItem('facultyDash:collapsed:' + id) === '1') {
                    css += '#' + id + ' .chart-panel-head{margin-bottom:0!important;transition:none!important}'
                        + '#' + id + ' .chart-body{grid-template-rows:0fr!important;transition:none!important}'
                        + '#' + id + ' .chart-body-inner{opacity:0!important;transition:none!important}';
                }
            });
            if (css) {
                var style = document.createElement('style');
                style.id = 'collapse-preload';
                style.textContent = css;
                document.head.appendChild(style);
            }
        } catch (e) {}
    })();
</script>
@endsection

@section('content')
    @php
        $facultyFirstName = \Illuminate\Support\Str::of(auth()->user()?->name ?? 'Faculty')->before(' ');
    @endphp

    <div class="dashboard-page charts-loading">
        <div class="page-head">
            <div>
                <span class="eyebrow">Faculty overview</span>
                <h1 class="page-title">Welcome back, {{ $facultyFirstName }}</h1>
            </div>
            <div class="page-head-actions">
                <span class="page-date">{{ now()->format('l, F d, Y') }}</span>
                <div class="chart-menu-wrap" id="download_all_wrap">
                    <button type="button" class="btn btn-outline" data-menu-btn aria-haspopup="true" aria-expanded="false">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                            <polyline points="7 10 12 15 17 10"></polyline>
                            <line x1="12" y1="15" x2="12" y2="3"></line>
                        </svg>
                        Download reports
                    </button>
                    <div class="chart-menu pop report-menu" data-menu hidden></div>
                </div>
            </div>
        </div>

        <section class="dstat-grid" aria-label="Partnership statistics">
            <article class="dstat-card">
                <span class="dstat-label">Total applications</span>
                <span class="dstat-value" data-count-to="{{ $totalApplications }}">0</span>
                <span class="dstat-foot">All partnership records on file</span>
            </article>

            <article class="dstat-card">
                <span class="dstat-label">Pending review</span>
                <span class="dstat-value" data-count-to="{{ $pendingCount }}">0</span>
                <span class="dstat-foot">Awaiting faculty or admin action</span>
            </article>

            <article class="dstat-card">
                <span class="dstat-label">Approved</span>
                <span class="dstat-value" data-count-to="{{ $approvedCount }}">0</span>
                <span class="dstat-foot">{{ number_format($totalConcessionaires) }} active {{ \Illuminate\Support\Str::plural('concessionaire', $totalConcessionaires) }}</span>
            </article>

            <article class="dstat-card">
                <span class="dstat-label">Reviewed by me</span>
                <span class="dstat-value" data-count-to="{{ $reviewedByMe }}">0</span>
                <span class="dstat-foot">Recommendations you submitted</span>
            </article>
        </section>

        <div class="dashboard-charts-row">
            <div class="panel chart-panel" id="panel_monthly">
                <div class="chart-panel-head">
                    <div>
                        <h2 class="panel-title">Applications per month</h2>
                        <p class="panel-sub">Submissions over the last 6 months.</p>
                    </div>
                    <div class="chart-menu-wrap">
                        <button type="button" class="chart-menu-btn" data-menu-btn aria-haspopup="true" aria-expanded="false" aria-label="Applications per month options">&#8943;</button>
                        <div class="chart-menu pop" data-menu hidden></div>
                    </div>
                </div>
                <div class="chart-body">
                    <div class="chart-body-inner">
                        <div id="chart_monthly_applications"></div>
                    </div>
                </div>
            </div>

            <div class="panel chart-panel" id="panel_status">
                <div class="chart-panel-head">
                    <div>
                        <h2 class="panel-title">Status distribution</h2>
                        <p class="panel-sub">Applications by current status.</p>
                    </div>
                    <div class="chart-menu-wrap">
                        <button type="button" class="chart-menu-btn" data-menu-btn aria-haspopup="true" aria-expanded="false" aria-label="Status distribution options">&#8943;</button>
                        <div class="chart-menu pop" data-menu hidden></div>
                    </div>
                </div>
                <div class="chart-body">
                    <div class="chart-body-inner">
                        <div id="chart_status_distribution"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="dashboard-charts-row">
            <div class="panel chart-panel" id="panel_revenue">
                <div class="chart-panel-head">
                    <div>
                        <h2 class="panel-title">Revenue</h2>
                        <p class="panel-sub">Uniform &amp; book sales over the last 6 months.</p>
                    </div>
                    <div class="chart-menu-wrap">
                        <button type="button" class="chart-menu-btn" data-menu-btn aria-haspopup="true" aria-expanded="false" aria-label="Revenue options">&#8943;</button>
                        <div class="chart-menu pop" data-menu hidden></div>
                    </div>
                </div>
                <div class="chart-body">
                    <div class="chart-body-inner">
                        <div id="chart_revenue"></div>
                    </div>
                </div>
            </div>

            <div class="panel chart-panel" id="panel_units">
                <div class="chart-panel-head">
                    <div>
                        <h2 class="panel-title">Units sold</h2>
                        <p class="panel-sub">Uniforms vs. books per month.</p>
                    </div>
                    <div class="chart-menu-wrap">
                        <button type="button" class="chart-menu-btn" data-menu-btn aria-haspopup="true" aria-expanded="false" aria-label="Units sold options">&#8943;</button>
                        <div class="chart-menu pop" data-menu hidden></div>
                    </div>
                </div>
                <div class="chart-body">
                    <div class="chart-body-inner">
                        <div id="chart_units_by_type"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="panel chart-panel" id="panel_top_items">
            <div class="chart-panel-head">
                <div>
                    <h2 class="panel-title">Items bought</h2>
                    <p class="panel-sub">Total units sold per item, all time.</p>
                </div>
                <div class="chart-menu-wrap">
                    <button type="button" class="chart-menu-btn" data-menu-btn aria-haspopup="true" aria-expanded="false" aria-label="Items bought options">&#8943;</button>
                    <div class="chart-menu pop" data-menu hidden></div>
                </div>
            </div>
            <div class="chart-body">
                <div class="chart-body-inner">
                    <div id="chart_top_items" style="min-height: {{ max(260, count($topItemLabels) * 46 + 40) }}px;"></div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    // ---------- Stat counters ----------
    document.querySelectorAll('.dstat-value[data-count-to]').forEach(function (element) {
        const target = parseFloat(element.getAttribute('data-count-to')) || 0;

        const formatValue = function (value) {
            return Math.round(value).toLocaleString('en-US');
        };

        if (prefersReducedMotion || target === 0) {
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

    // ---------- Chart panel menus: collapse + downloads ----------
    function csvEscape(value) {
        value = String(value === null || value === undefined ? '' : value);
        return /[",\n]/.test(value) ? '"' + value.replace(/"/g, '""') + '"' : value;
    }

    function rowsToCSV(rows) {
        return rows.map(function (row) {
            return row.map(csvEscape).join(',');
        }).join('\r\n');
    }

    function downloadCSVString(filename, csvString) {
        var blob = new Blob(['﻿' + csvString], { type: 'text/csv;charset=utf-8;' });
        var link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = filename;
        link.click();
        URL.revokeObjectURL(link.href);
    }

    function downloadCSV(filename, headers, rows) {
        downloadCSVString(filename, rowsToCSV([headers].concat(rows)));
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
        document.querySelectorAll('[data-menu-btn]').forEach(function (btn) {
            btn.setAttribute('aria-expanded', 'false');
        });
    }

    function wireChartPanel(panelId, actions) {
        var panel = document.getElementById(panelId);
        if (!panel) return;

        var btn = panel.querySelector('[data-menu-btn]');
        var menu = panel.querySelector('[data-menu]');
        if (!btn || !menu) return;

        // Restore the saved collapse state without replaying the open/close animation.
        var storageKey = 'facultyDash:collapsed:' + panelId;
        var savedCollapsed = false;
        try { savedCollapsed = localStorage.getItem(storageKey) === '1'; } catch (e) {}
        if (savedCollapsed) {
            panel.classList.add('no-anim', 'is-collapsed');
            requestAnimationFrame(function () {
                requestAnimationFrame(function () {
                    panel.classList.remove('no-anim');
                });
            });
        }

        var collapseItem = document.createElement('button');
        collapseItem.type = 'button';
        collapseItem.className = 'chart-menu-item';
        collapseItem.textContent = panel.classList.contains('is-collapsed') ? 'Expand' : 'Collapse';
        collapseItem.addEventListener('click', function () {
            var collapsed = panel.classList.toggle('is-collapsed');
            collapseItem.textContent = collapsed ? 'Expand' : 'Collapse';
            try { localStorage.setItem(storageKey, collapsed ? '1' : '0'); } catch (e) {}
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
    var appMonthLabels = @json($appMonthLabels);
    var appMonthData = @json($appMonthData);
    var statusLabels = @json($statusLabelsFormatted);
    var statusData = @json($statusData);
    var salesMonthLabels = @json($salesMonthLabels);
    var revenueData = @json($revenueData);
    var unitsUniformsData = @json($unitsUniformsData);
    var unitsBooksData = @json($unitsBooksData);
    var topItemLabels = @json($topItemLabels);
    var topItemData = @json($topItemData);

    var pesoFormatter = new Intl.NumberFormat('en-PH', { style: 'currency', currency: 'PHP', minimumFractionDigits: 0, maximumFractionDigits: 0 });
    function formatPeso(value) { return pesoFormatter.format(Math.round(value || 0)); }

    var chartFont = getComputedStyle(document.documentElement).getPropertyValue('--font-ui').trim() || 'Manrope, sans-serif';

    // Re-enable collapse transitions only once the charts have drawn, so the
    // initial render never plays a collapse/expand animation.
    var dashboardPageEl = document.querySelector('.dashboard-page');
    var chartsExpected = document.querySelectorAll('.chart-body-inner > div').length;
    var chartsMounted = 0;
    function releaseChartTransitions() {
        if (dashboardPageEl) {
            dashboardPageEl.classList.remove('charts-loading');
        }
    }
    function chartMountedTick() {
        chartsMounted++;
        if (chartsMounted >= chartsExpected) {
            requestAnimationFrame(function () {
                requestAnimationFrame(releaseChartTransitions);
            });
        }
    }
    // Fallback so transitions are never stuck off if a chart fails to mount.
    setTimeout(releaseChartTransitions, 1500);

    var chartBase = {
        fontFamily: chartFont,
        foreColor: '#687180',
        toolbar: { show: false },
        animations: { enabled: !prefersReducedMotion },
        events: { mounted: chartMountedTick }
    };

    var monthlyChart = null;
    var statusChart = null;
    var revenueChart = null;
    var unitsChart = null;
    var topItemsChart = null;

    if (document.querySelector('#chart_monthly_applications') && typeof ApexCharts !== 'undefined') {
        var monthlyOptions = {
            chart: Object.assign({}, chartBase, {
                height: 320,
                type: 'area',
                width: '100%'
            }),
            series: [
                { name: 'Applications', data: appMonthData }
            ],
            colors: ['#1F2937'],
            stroke: { curve: 'smooth', width: 2.5, lineCap: 'round' },
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.26,
                    opacityTo: 0.02,
                    stops: [0, 95]
                }
            },
            dataLabels: { enabled: false },
            markers: {
                size: 3.5,
                strokeColors: '#fff',
                strokeWidth: 2,
                hover: { size: 5 }
            },
            xaxis: {
                categories: appMonthLabels,
                labels: { style: { fontSize: '11px', fontWeight: 600 } },
                axisBorder: { show: false },
                axisTicks: { show: false }
            },
            yaxis: {
                min: 0,
                forceNiceScale: true,
                labels: {
                    style: { fontSize: '11px' },
                    formatter: function (value) { return Math.round(value); }
                }
            },
            tooltip: {
                theme: 'dark',
                style: { fontSize: '12px' },
                y: {
                    formatter: function (value) {
                        return Math.round(value) + ' ' + (Math.round(value) === 1 ? 'application' : 'applications');
                    }
                }
            },
            grid: {
                borderColor: '#EEF0F3',
                strokeDashArray: 4,
                padding: { left: 6, right: 6 }
            }
        };

        monthlyChart = new ApexCharts(document.querySelector('#chart_monthly_applications'), monthlyOptions);

        // Short delay so the CSS Grid wrapper settles on its final width before drawing
        setTimeout(function () {
            monthlyChart.render();
        }, 50);
    }

    if (document.querySelector('#chart_status_distribution') && typeof ApexCharts !== 'undefined') {
        var statusOptions = {
            chart: Object.assign({}, chartBase, {
                height: 320,
                type: 'bar',
                width: '100%'
            }),
            series: [
                { name: 'Applications', data: statusData }
            ],
            colors: ['#D97706', '#64748B', '#1F2937', '#B3261E', '#94A3B8'],
            plotOptions: {
                bar: {
                    columnWidth: '44%',
                    borderRadius: 4,
                    borderRadiusApplication: 'end',
                    distributed: true
                }
            },
            dataLabels: { enabled: false },
            legend: { show: false },
            xaxis: {
                categories: statusLabels,
                labels: {
                    style: { fontSize: '11px', fontWeight: 600 },
                    hideOverlappingLabels: false,
                    trim: false
                },
                axisBorder: { show: false },
                axisTicks: { show: false }
            },
            yaxis: {
                min: 0,
                forceNiceScale: true,
                labels: {
                    style: { fontSize: '11px' },
                    formatter: function (value) { return Math.round(value); }
                }
            },
            tooltip: {
                theme: 'dark',
                style: { fontSize: '12px' },
                y: {
                    formatter: function (value) {
                        return Math.round(value) + ' ' + (Math.round(value) === 1 ? 'application' : 'applications');
                    }
                }
            },
            grid: {
                borderColor: '#EEF0F3',
                strokeDashArray: 4,
                padding: { left: 6, right: 6 }
            }
        };

        statusChart = new ApexCharts(document.querySelector('#chart_status_distribution'), statusOptions);

        setTimeout(function () {
            statusChart.render();
        }, 50);
    }

    // ---------- Revenue (area) ----------
    if (document.querySelector('#chart_revenue') && typeof ApexCharts !== 'undefined') {
        var revenueOptions = {
            chart: Object.assign({}, chartBase, {
                height: 320,
                type: 'area',
                width: '100%'
            }),
            series: [
                { name: 'Revenue', data: revenueData }
            ],
            colors: ['#1F2937'],
            stroke: { curve: 'smooth', width: 2.5, lineCap: 'round' },
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.26,
                    opacityTo: 0.02,
                    stops: [0, 95]
                }
            },
            dataLabels: { enabled: false },
            markers: {
                size: 3.5,
                strokeColors: '#fff',
                strokeWidth: 2,
                hover: { size: 5 }
            },
            xaxis: {
                categories: salesMonthLabels,
                labels: { style: { fontSize: '11px', fontWeight: 600 } },
                axisBorder: { show: false },
                axisTicks: { show: false }
            },
            yaxis: {
                min: 0,
                forceNiceScale: true,
                labels: {
                    style: { fontSize: '11px' },
                    formatter: function (value) { return formatPeso(value); }
                }
            },
            tooltip: {
                theme: 'dark',
                style: { fontSize: '12px' },
                y: {
                    formatter: function (value) { return formatPeso(value); }
                }
            },
            grid: {
                borderColor: '#EEF0F3',
                strokeDashArray: 4,
                padding: { left: 6, right: 6 }
            }
        };

        revenueChart = new ApexCharts(document.querySelector('#chart_revenue'), revenueOptions);

        setTimeout(function () {
            revenueChart.render();
        }, 50);
    }

    // ---------- Units sold: uniforms vs books (stacked bar) ----------
    if (document.querySelector('#chart_units_by_type') && typeof ApexCharts !== 'undefined') {
        var unitsOptions = {
            chart: Object.assign({}, chartBase, {
                height: 320,
                type: 'bar',
                stacked: true,
                width: '100%'
            }),
            series: [
                { name: 'Uniforms', data: unitsUniformsData },
                { name: 'Books', data: unitsBooksData }
            ],
            colors: ['#1F2937', '#94A3B8'],
            plotOptions: {
                bar: {
                    columnWidth: '46%',
                    borderRadius: 4,
                    borderRadiusApplication: 'end'
                }
            },
            dataLabels: { enabled: false },
            legend: {
                position: 'top',
                horizontalAlign: 'right',
                fontSize: '12px',
                fontWeight: 600,
                markers: { radius: 3 },
                itemMargin: { horizontal: 10 }
            },
            xaxis: {
                categories: salesMonthLabels,
                labels: { style: { fontSize: '11px', fontWeight: 600 } },
                axisBorder: { show: false },
                axisTicks: { show: false }
            },
            yaxis: {
                min: 0,
                forceNiceScale: true,
                labels: {
                    style: { fontSize: '11px' },
                    formatter: function (value) { return Math.round(value); }
                }
            },
            tooltip: {
                theme: 'dark',
                style: { fontSize: '12px' },
                y: {
                    formatter: function (value) {
                        return Math.round(value) + ' ' + (Math.round(value) === 1 ? 'unit' : 'units');
                    }
                }
            },
            grid: {
                borderColor: '#EEF0F3',
                strokeDashArray: 4,
                padding: { left: 6, right: 6 }
            }
        };

        unitsChart = new ApexCharts(document.querySelector('#chart_units_by_type'), unitsOptions);

        setTimeout(function () {
            unitsChart.render();
        }, 50);
    }

    // ---------- Items bought: per item (horizontal bar) ----------
    if (document.querySelector('#chart_top_items') && typeof ApexCharts !== 'undefined') {
        var topItemsHeight = Math.max(260, topItemLabels.length * 46 + 40);
        var topItemsOptions = {
            chart: Object.assign({}, chartBase, {
                height: topItemsHeight,
                type: 'bar',
                width: '100%'
            }),
            series: [
                { name: 'Units sold', data: topItemData }
            ],
            colors: ['#1F2937'],
            plotOptions: {
                bar: {
                    horizontal: true,
                    barHeight: '58%',
                    borderRadius: 4,
                    borderRadiusApplication: 'end'
                }
            },
            dataLabels: {
                enabled: true,
                style: { fontSize: '11px', fontWeight: 600, colors: ['#fff'] },
                offsetX: -2,
                formatter: function (value) { return Math.round(value); }
            },
            legend: { show: false },
            xaxis: {
                categories: topItemLabels,
                labels: {
                    style: { fontSize: '11px' },
                    formatter: function (value) { return Math.round(value); }
                },
                axisBorder: { show: false },
                axisTicks: { show: false }
            },
            yaxis: {
                labels: { style: { fontSize: '11.5px', fontWeight: 600 } }
            },
            tooltip: {
                theme: 'dark',
                style: { fontSize: '12px' },
                y: {
                    formatter: function (value) {
                        return Math.round(value) + ' ' + (Math.round(value) === 1 ? 'unit' : 'units');
                    }
                }
            },
            grid: {
                borderColor: '#EEF0F3',
                strokeDashArray: 4,
                padding: { left: 6, right: 6 }
            }
        };

        topItemsChart = new ApexCharts(document.querySelector('#chart_top_items'), topItemsOptions);

        setTimeout(function () {
            topItemsChart.render();
        }, 50);
    }

    // ---------- Report registry (shared by per-panel menus and "Download all") ----------
    var reportRegistry = [
        {
            panel: 'panel_monthly',
            chart: function () { return monthlyChart; },
            pngName: 'applications-per-month.png',
            csvName: 'applications-per-month.csv',
            csvHeaders: ['Month', 'Applications'],
            csvRows: function () {
                return appMonthLabels.map(function (label, index) {
                    return [label, appMonthData[index] || 0];
                });
            }
        },
        {
            panel: 'panel_status',
            chart: function () { return statusChart; },
            pngName: 'status-distribution.png',
            csvName: 'status-distribution.csv',
            csvHeaders: ['Status', 'Applications'],
            csvRows: function () {
                return statusLabels.map(function (label, index) {
                    return [label, statusData[index] || 0];
                });
            }
        },
        {
            panel: 'panel_revenue',
            chart: function () { return revenueChart; },
            pngName: 'revenue.png',
            csvName: 'revenue.csv',
            csvHeaders: ['Month', 'Revenue'],
            csvRows: function () {
                return salesMonthLabels.map(function (label, index) {
                    return [label, revenueData[index] || 0];
                });
            }
        },
        {
            panel: 'panel_units',
            chart: function () { return unitsChart; },
            pngName: 'units-sold.png',
            csvName: 'units-sold.csv',
            csvHeaders: ['Month', 'Uniforms', 'Books'],
            csvRows: function () {
                return salesMonthLabels.map(function (label, index) {
                    return [label, unitsUniformsData[index] || 0, unitsBooksData[index] || 0];
                });
            }
        },
        {
            panel: 'panel_top_items',
            chart: function () { return topItemsChart; },
            pngName: 'items-bought.png',
            csvName: 'items-bought.csv',
            csvHeaders: ['Item', 'Units sold'],
            csvRows: function () {
                return topItemLabels.map(function (label, index) {
                    return [label, topItemData[index] || 0];
                });
            }
        }
    ];

    // ---------- Wire the three-dot menus ----------
    reportRegistry.forEach(function (report) {
        wireChartPanel(report.panel, [
            {
                label: 'Download PNG',
                run: function () { downloadPNG(report.chart, report.pngName); }
            },
            {
                label: 'Download CSV',
                run: function () { downloadCSV(report.csvName, report.csvHeaders, report.csvRows()); }
            }
        ]);
    });

    // wireChartPanel has now applied the real .is-collapsed classes, so the
    // pre-paint override can be dropped (leaving it would block expanding).
    var collapsePreload = document.getElementById('collapse-preload');
    if (collapsePreload) {
        collapsePreload.remove();
    }

    // ---------- Download all reports (combined into a single file) ----------
    function reportTitle(report) {
        var el = document.querySelector('#' + report.panel + ' .panel-title');
        return el ? el.textContent.trim() : report.csvName;
    }

    function dashboardDateLabel() {
        var el = document.querySelector('.page-date');
        return el ? el.textContent.trim() : '';
    }

    // One CSV with each report as a titled section, separated by a blank line.
    function downloadAllCSV() {
        var blocks = reportRegistry.map(function (report) {
            var section = [[reportTitle(report)], report.csvHeaders].concat(report.csvRows());
            return rowsToCSV(section);
        });
        downloadCSVString('faculty-dashboard-reports.csv', blocks.join('\r\n\r\n'));
    }

    // One PNG that stacks every chart vertically with a header and per-chart titles.
    function downloadAllPNG() {
        var scale = 2;

        var loaders = reportRegistry.map(function (report) {
            var chart = report.chart();
            if (!chart) return Promise.resolve(null);

            return chart.dataURI({ scale: scale }).then(function (output) {
                return new Promise(function (resolve) {
                    var img = new Image();
                    img.onload = function () { resolve({ img: img, title: reportTitle(report) }); };
                    img.onerror = function () { resolve(null); };
                    img.src = output.imgURI;
                });
            }).catch(function () { return null; });
        });

        Promise.all(loaders).then(function (items) {
            items = items.filter(Boolean);
            if (!items.length) return;

            var pad = 32 * scale;
            var gap = 26 * scale;
            var titleH = 30 * scale;
            var headerH = 56 * scale;

            var maxImgW = 0;
            items.forEach(function (it) { maxImgW = Math.max(maxImgW, it.img.width); });

            var canvasW = maxImgW + pad * 2;
            var canvasH = pad + headerH + gap;
            items.forEach(function (it) { canvasH += titleH + it.img.height + gap; });
            canvasH += pad - gap;

            var canvas = document.createElement('canvas');
            canvas.width = canvasW;
            canvas.height = canvasH;
            var ctx = canvas.getContext('2d');

            ctx.fillStyle = '#ffffff';
            ctx.fillRect(0, 0, canvasW, canvasH);
            ctx.textBaseline = 'top';

            var y = pad;
            ctx.fillStyle = '#1B232B';
            ctx.font = '700 ' + (22 * scale) + 'px ' + chartFont;
            ctx.fillText('Faculty Dashboard Reports', pad, y);
            ctx.fillStyle = '#687180';
            ctx.font = '600 ' + (13 * scale) + 'px ' + chartFont;
            ctx.fillText(dashboardDateLabel(), pad, y + (28 * scale));
            y += headerH + gap;

            items.forEach(function (it) {
                ctx.fillStyle = '#1B232B';
                ctx.font = '700 ' + (15 * scale) + 'px ' + chartFont;
                ctx.fillText(it.title, pad, y);
                y += titleH;
                ctx.drawImage(it.img, pad, y, it.img.width, it.img.height);
                y += it.img.height + gap;
            });

            var link = document.createElement('a');
            link.href = canvas.toDataURL('image/png');
            link.download = 'faculty-dashboard-reports.png';
            link.click();
        });
    }

    (function wireDownloadAll() {
        var wrap = document.getElementById('download_all_wrap');
        if (!wrap) return;

        var btn = wrap.querySelector('[data-menu-btn]');
        var menu = wrap.querySelector('[data-menu]');
        if (!btn || !menu) return;

        [
            { label: 'All reports (CSV)', run: downloadAllCSV },
            { label: 'All charts (PNG)', run: downloadAllPNG }
        ].forEach(function (action) {
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

        btn.addEventListener('click', function (event) {
            event.stopPropagation();
            var willOpen = menu.hidden;
            closeAllChartMenus();
            if (willOpen) {
                menu.hidden = false;
                btn.setAttribute('aria-expanded', 'true');
            }
        });
    })();
</script>
@endsection
