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
        $facultyFirstName = \Illuminate\Support\Str::of(auth()->user()?->name ?? 'Faculty')->before(' ');
    @endphp

    <div class="dashboard-page">
        <div class="page-head">
            <div>
                <span class="eyebrow">Faculty overview</span>
                <h1 class="page-title">Welcome back, {{ $facultyFirstName }}</h1>
            </div>
            <span class="page-date">{{ now()->format('l, F d, Y') }}</span>
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
    var appMonthLabels = @json($appMonthLabels);
    var appMonthData = @json($appMonthData);
    var statusLabels = @json($statusLabelsFormatted);
    var statusData = @json($statusData);

    var chartFont = getComputedStyle(document.documentElement).getPropertyValue('--font-ui').trim() || 'Manrope, sans-serif';
    var chartBase = {
        fontFamily: chartFont,
        foreColor: '#687180',
        toolbar: { show: false },
        animations: { enabled: !prefersReducedMotion }
    };

    var monthlyChart = null;
    var statusChart = null;

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

    // ---------- Wire the three-dot menus ----------
    wireChartPanel('panel_monthly', [
        {
            label: 'Download PNG',
            run: function () { downloadPNG(function () { return monthlyChart; }, 'applications-per-month.png'); }
        },
        {
            label: 'Download CSV',
            run: function () {
                downloadCSV(
                    'applications-per-month.csv',
                    ['Month', 'Applications'],
                    appMonthLabels.map(function (label, index) {
                        return [label, appMonthData[index] || 0];
                    })
                );
            }
        }
    ]);

    wireChartPanel('panel_status', [
        {
            label: 'Download PNG',
            run: function () { downloadPNG(function () { return statusChart; }, 'status-distribution.png'); }
        },
        {
            label: 'Download CSV',
            run: function () {
                downloadCSV(
                    'status-distribution.csv',
                    ['Status', 'Applications'],
                    statusLabels.map(function (label, index) {
                        return [label, statusData[index] || 0];
                    })
                );
            }
        }
    ]);
</script>
@endsection
