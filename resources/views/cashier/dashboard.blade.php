@extends('cashier.layout')

@section('title', 'Dashboard')

@section('extra-css')
<style>
    .dashboard-page {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }
    .dashboard-page .page-head {
        margin-bottom: 2px;
    }

    /* ---------- Stat cards ---------- */
    .dstat-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 16px;
    }
    .dstat-card {
        position: relative;
        background: #fff;
        border: 1px solid var(--line);
        border-radius: 14px;
        padding: 18px 18px 0;
        display: flex;
        flex-direction: column;
        box-shadow: var(--shadow-card);
        min-width: 0;
        overflow: hidden;
    }
    .dstat-top {
        display: flex;
        align-items: center;
        gap: 9px;
        margin-bottom: 14px;
    }
    .dstat-icon {
        width: 30px;
        height: 30px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        background: var(--pine-soft);
        color: var(--pine);
    }
    .dstat-icon svg { width: 16px; height: 16px; }
    .dstat-card.tone-amber .dstat-icon { background: #FDF8EC; color: #B45309; }
    .dstat-card.tone-red .dstat-icon { background: #FBEAEA; color: #B3261E; }
    .dstat-label {
        font-family: var(--font-mono);
        font-size: 10.5px;
        font-weight: 600;
        letter-spacing: 0.1em;
        text-transform: uppercase;
        color: var(--muted);
        line-height: 1.2;
    }
    .dstat-value-row {
        display: flex;
        align-items: baseline;
        gap: 5px;
    }
    .dstat-value {
        font-family: var(--font-mono);
        font-size: 32px;
        font-weight: 600;
        line-height: 1.05;
        letter-spacing: -0.03em;
        color: var(--ink);
        font-variant-numeric: tabular-nums;
    }
    .dstat-unit {
        font-family: var(--font-mono);
        font-size: 13px;
        color: var(--faint);
    }
    .dstat-delta-row {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-top: 10px;
        margin-bottom: 14px;
    }
    .dstat-chip {
        display: inline-flex;
        align-items: center;
        font-family: var(--font-mono);
        font-size: 11px;
        font-weight: 600;
        padding: 3px 8px;
        border-radius: 5px;
        white-space: nowrap;
        flex-shrink: 0;
    }
    .dstat-chip.ok { background: #E5F3EA; color: var(--pine); }
    .dstat-chip.warn { background: #FDF8EC; color: #92400E; }
    .dstat-chip.bad { background: #FBEAEA; color: #B3261E; }
    .dstat-chip.neutral { background: #F0F2F0; color: var(--muted); }
    .dstat-context {
        font-size: 12px;
        color: var(--muted);
        min-width: 0;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .dstat-spark {
        margin: auto -18px 0;
        height: 46px;
        min-height: 46px;
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
        $cashierFirstName = \Illuminate\Support\Str::of(auth()->user()?->name ?? 'Cashier')->before(' ');
        $monthlySeries = collect($cashierMonthlyPayments ?? []);
        $collectedThisMonth = (float) ($monthlySeries->last()['total'] ?? 0);
        $activeCount = (int) ($activeConcessionairesCount ?? 0);
        $readyCount = (int) ($readyToRecordCount ?? 0);
        $overdue = (int) ($overdueCount ?? 0);
    @endphp

    <div class="dashboard-page">
        @if (session('success'))
            <div class="alert alert-success" style="margin-bottom:0;">{{ session('success') }}</div>
        @endif

        <div class="page-head">
            <div>
                <span class="eyebrow">Cashier overview</span>
                <h1 class="page-title">Welcome back, {{ $cashierFirstName }}</h1>
            </div>
            <span class="page-date">{{ now()->format('l, F d, Y') }}</span>
        </div>

        <section class="dstat-grid" aria-label="Collection statistics">
            <article class="dstat-card">
                <div class="dstat-top">
                    <span class="dstat-icon">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </span>
                    <span class="dstat-label">Collected this month</span>
                </div>
                <div class="dstat-value-row">
                    <span class="dstat-value" data-count-to="{{ number_format($collectedThisMonth, 2, '.', '') }}" data-decimals="2" data-prefix="&#8369;">&#8369;0.00</span>
                </div>
                <div class="dstat-delta-row">
                    <span class="dstat-chip" id="chip_collected"></span>
                    <span class="dstat-context">vs last month</span>
                </div>
                <div class="dstat-spark" id="spark_collected"></div>
            </article>

            <article class="dstat-card">
                <div class="dstat-top">
                    <span class="dstat-icon">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </span>
                    <span class="dstat-label">Active concessionaires</span>
                </div>
                <div class="dstat-value-row">
                    <span class="dstat-value" data-count-to="{{ $activeCount }}">0</span>
                </div>
                <div class="dstat-delta-row">
                    <span class="dstat-chip" id="chip_active"></span>
                    <span class="dstat-context">Approved &amp; under contract</span>
                </div>
                <div class="dstat-spark" id="spark_active"></div>
            </article>

            <article class="dstat-card tone-amber">
                <div class="dstat-top">
                    <span class="dstat-icon">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </span>
                    <span class="dstat-label">Ready to record</span>
                </div>
                <div class="dstat-value-row">
                    <span class="dstat-value" data-count-to="{{ $readyCount }}">0</span>
                </div>
                <div class="dstat-delta-row">
                    <span class="dstat-chip" id="chip_ready"></span>
                    <span class="dstat-context">Awaiting this month&rsquo;s payment</span>
                </div>
                <div class="dstat-spark" id="spark_ready"></div>
            </article>

            <article class="dstat-card tone-red">
                <div class="dstat-top">
                    <span class="dstat-icon">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                        </svg>
                    </span>
                    <span class="dstat-label">Overdue this month</span>
                </div>
                <div class="dstat-value-row">
                    <span class="dstat-value" data-count-to="{{ $overdue }}">0</span>
                </div>
                <div class="dstat-delta-row">
                    <span class="dstat-chip" id="chip_overdue"></span>
                    <span class="dstat-context">No payment past due date</span>
                </div>
                <div class="dstat-spark" id="spark_overdue"></div>
            </article>
        </section>

        <div class="dashboard-charts-row">
            <div class="panel chart-panel" id="panel_collections">
                <div class="chart-panel-head">
                    <div>
                        <h2 class="panel-title">Collections trend</h2>
                        <p class="panel-sub">Payments recorded over the last 6 months.</p>
                    </div>
                    <div class="chart-menu-wrap">
                        <button type="button" class="chart-menu-btn" data-menu-btn aria-haspopup="true" aria-expanded="false" aria-label="Collections trend options">&#8943;</button>
                        <div class="chart-menu pop" data-menu hidden></div>
                    </div>
                </div>
                <div class="chart-body">
                    <div class="chart-body-inner">
                        <div id="chart_cashier_monthly"></div>
                    </div>
                </div>
            </div>

            <div class="panel chart-panel" id="panel_types">
                <div class="chart-panel-head">
                    <div>
                        <h2 class="panel-title">Payment methods</h2>
                        <p class="panel-sub">How recorded payments were made.</p>
                    </div>
                    <div class="chart-menu-wrap">
                        <button type="button" class="chart-menu-btn" data-menu-btn aria-haspopup="true" aria-expanded="false" aria-label="Payment methods options">&#8943;</button>
                        <div class="chart-menu pop" data-menu hidden></div>
                    </div>
                </div>
                <div class="chart-body">
                    <div class="chart-body-inner">
                        <div id="chart_cashier_types"></div>
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

    // ---------- Count-up statcard values ----------
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

    var cashierMonthly = @json($cashierMonthlyPayments);
    var statSparklines = @json($statSparklines);

    // ---------- Month-over-month delta chips ----------
    function setTrendChip(id, data, options) {
        options = options || {};
        var el = document.getElementById(id);
        if (!el) return;
        if (!Array.isArray(data) || data.length < 2) {
            el.style.display = 'none';
            return;
        }

        var delta = Number(data[data.length - 1]) - Number(data[data.length - 2]);
        var upClass = options.upClass || 'ok';
        var downClass = options.downClass || 'bad';
        var cls = delta > 0 ? upClass : (delta < 0 ? downClass : 'neutral');
        var arrow = delta > 0 ? '↑' : (delta < 0 ? '↓' : '—');
        var magnitude = Math.abs(delta).toLocaleString('en-US', {
            minimumFractionDigits: options.decimals || 0,
            maximumFractionDigits: options.decimals || 0
        });

        el.classList.add(cls);
        el.textContent = delta === 0 ? '— steady' : arrow + ' ' + (options.prefix || '') + magnitude;
        el.title = 'Compared with last month';
    }

    setTrendChip('chip_collected', statSparklines.collections, { prefix: '₱', decimals: 2 });
    setTrendChip('chip_active', statSparklines.active);
    setTrendChip('chip_ready', statSparklines.ready, { upClass: 'warn', downClass: 'ok' });
    setTrendChip('chip_overdue', statSparklines.overdue, { upClass: 'bad', downClass: 'ok' });

    // ---------- Stat-card sparklines ----------
    function renderSpark(selector, data, color) {
        var target = document.querySelector(selector);
        if (!target || typeof ApexCharts === 'undefined') return;
        if (!Array.isArray(data) || !data.length) return;

        new ApexCharts(target, {
            chart: {
                type: 'area',
                height: 46,
                sparkline: { enabled: true },
                animations: { enabled: !prefersReducedMotion }
            },
            stroke: { curve: 'smooth', width: 2, lineCap: 'round' },
            fill: {
                type: 'gradient',
                gradient: { shadeIntensity: 1, opacityFrom: 0.32, opacityTo: 0.02, stops: [0, 100] }
            },
            series: [{ name: '', data: data }],
            colors: [color],
            dataLabels: { enabled: false },
            tooltip: { enabled: false }
        }).render();
    }

    renderSpark('#spark_collected', statSparklines.collections, '#0A5C2F');
    renderSpark('#spark_active', statSparklines.active, '#0A5C2F');
    renderSpark('#spark_ready', statSparklines.ready, '#B45309');
    renderSpark('#spark_overdue', statSparklines.overdue, '#B3261E');

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

    var monthlyChart = null;
    var typesChart = null;

    if (document.querySelector('#chart_cashier_monthly') && typeof ApexCharts !== 'undefined') {
        var monthlyOptions = {
            chart: Object.assign({}, chartBase, {
                height: 340,
                type: 'line',
                width: '100%'
            }),
            series: [
                {
                    name: 'Collected',
                    type: 'column',
                    data: cashierMonthly.map(function (m) { return m.total; })
                },
                {
                    name: 'Trend',
                    type: 'line',
                    data: cashierMonthly.map(function (m) { return m.total; })
                }
            ],
            stroke: { width: [0, 2.5], curve: 'smooth', lineCap: 'round' },
            plotOptions: {
                bar: { columnWidth: '42%', borderRadius: 4, borderRadiusApplication: 'end' }
            },
            colors: ['#9CC5AC', '#0A5C2F'],
            fill: { opacity: [1, 1], type: 'solid' },
            dataLabels: { enabled: false },
            labels: cashierMonthly.map(function (m) { return m.month; }),
            markers: {
                size: [0, 3.5],
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
            yaxis: {
                labels: {
                    style: { fontSize: '11px' },
                    formatter: function (value) { return '₱' + Number(value).toLocaleString(); }
                }
            },
            legend: { show: false },
            tooltip: {
                shared: true,
                intersect: false,
                theme: 'dark',
                style: { fontSize: '12px' },
                y: { formatter: function (value) { return '₱' + Number(value).toLocaleString('en-US', { minimumFractionDigits: 2 }); } }
            },
            grid: {
                borderColor: '#EDF2EE',
                strokeDashArray: 4,
                padding: { left: 6, right: 6 }
            }
        };

        monthlyChart = new ApexCharts(document.querySelector('#chart_cashier_monthly'), monthlyOptions);

        // Short delay so the CSS Grid wrapper settles on its final width before drawing
        setTimeout(function () {
            monthlyChart.render();
        }, 50);
    }

    var cashierTypes = @json($cashierPaymentTypes);
    var typesTarget = document.querySelector('#chart_cashier_types');
    var typesTotal = (cashierTypes.cash || 0) + (cashierTypes.check || 0) + (cashierTypes.bank_transfer || 0);

    if (typesTarget && typeof ApexCharts !== 'undefined') {
        if (typesTotal === 0) {
            typesTarget.innerHTML = '<p class="chart-empty">No payments recorded yet.</p>';
        } else {
            var typesOptions = {
                chart: Object.assign({}, chartBase, {
                    height: 300,
                    type: 'donut',
                    width: '100%'
                }),
                series: [
                    cashierTypes.cash || 0,
                    cashierTypes.check || 0,
                    cashierTypes.bank_transfer || 0
                ],
                labels: ['Cash', 'Check', 'Bank Transfer'],
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
                                    label: 'Payments',
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
                            return value + ' ' + (value === 1 ? 'payment' : 'payments');
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
                            chart: { height: 260 }
                        }
                    }
                ]
            };

            typesChart = new ApexCharts(typesTarget, typesOptions);
            setTimeout(function () {
                typesChart.render();
            }, 50);
        }
    }

    // ---------- Wire the three-dot menus ----------
    wireChartPanel('panel_collections', [
        {
            label: 'Download PNG',
            run: function () { downloadPNG(function () { return monthlyChart; }, 'collections-trend.png'); }
        },
        {
            label: 'Download CSV',
            run: function () {
                downloadCSV(
                    'collections-trend.csv',
                    ['Month', 'Collected'],
                    cashierMonthly.map(function (m) { return [m.month, m.total]; })
                );
            }
        }
    ]);

    wireChartPanel('panel_types', [
        {
            label: 'Download PNG',
            available: typesTotal > 0,
            run: function () { downloadPNG(function () { return typesChart; }, 'payment-methods.png'); }
        },
        {
            label: 'Download CSV',
            run: function () {
                downloadCSV('payment-methods.csv', ['Method', 'Payments'], [
                    ['Cash', cashierTypes.cash || 0],
                    ['Check', cashierTypes.check || 0],
                    ['Bank Transfer', cashierTypes.bank_transfer || 0]
                ]);
            }
        }
    ]);
</script>
@endsection
