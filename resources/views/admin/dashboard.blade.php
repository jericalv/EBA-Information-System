@extends('admin.layout')

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
        gap: 10px;
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
        font-family: var(--font-ui);
        font-size: 34px;
        font-weight: 800;
        line-height: 1.05;
        letter-spacing: -0.03em;
        color: var(--ink);
        font-variant-numeric: tabular-nums;
    }
    .dstat-value.is-pine { color: var(--pine); }
    .dstat-value.is-amber { color: var(--amber); }
    .dstat-value.is-danger { color: var(--danger); }
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
    .dashboard-charts-row.alt {
        grid-template-columns: minmax(0, 5fr) minmax(0, 7fr);
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
    .chart-panel.is-collapsed .chart-panel-head { margin-bottom: 0; }
    .chart-menu-wrap { position: relative; flex-shrink: 0; }
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
    .chart-menu-btn[aria-expanded="true"] { background: var(--pine-soft); color: var(--ink); }
    .chart-menu-btn:focus-visible { outline: 2px solid rgba(10, 92, 47, 0.45); outline-offset: 2px; }
    .chart-menu {
        position: absolute;
        right: 0;
        top: 36px;
        min-width: 176px;
        z-index: 40;
        padding: 5px;
    }
    .chart-menu[hidden] { display: none; }
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
    .chart-menu-item:hover { background: var(--pine-soft); }
    .chart-menu-divider { height: 1px; margin: 5px 4px; background: var(--line); }
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
    .chart-panel.is-collapsed .chart-body { grid-template-rows: 0fr; }
    .chart-panel.is-collapsed .chart-body-inner { opacity: 0; }
    .chart-empty { margin: 8px 0 0; color: var(--muted); font-size: 13.5px; }

    .chart-panel.no-anim .chart-panel-head,
    .chart-panel.no-anim .chart-body,
    .chart-panel.no-anim .chart-body-inner { transition: none; }
    /* Reserve chart height before ApexCharts draws so the grid track never
       animates from 0 to full height on load (the collapse-then-expand flash). */
    #chart_admin_monthly { min-height: 320px; }
    #chart_admin_status { min-height: 300px; }
    #chart_admin_trend { min-height: 300px; }
    #chart_admin_top { min-height: 320px; }
    /* Belt-and-suspenders: no collapse transitions until charts have mounted. */
    .dashboard-page.charts-loading .chart-panel-head,
    .dashboard-page.charts-loading .chart-body,
    .dashboard-page.charts-loading .chart-body-inner { transition: none !important; }

    @media (max-width: 1100px) {
        .dstat-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .dashboard-charts-row,
        .dashboard-charts-row.alt { grid-template-columns: 1fr; }
    }
    @media (max-width: 640px) {
        .dstat-grid { grid-template-columns: 1fr; }
    }
</style>
<script>
    // Pre-paint restore: collapse saved panels via CSS before they render, so a
    // hard refresh never flashes the expanded state before the main script runs.
    (function () {
        try {
            var panelIds = ['panel_monthly', 'panel_status', 'panel_trend', 'panel_top'];
            var css = '';
            panelIds.forEach(function (id) {
                if (localStorage.getItem('adminDash:collapsed:' + id) === '1') {
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
        $adminFirstName = \Illuminate\Support\Str::of(auth()->user()?->name ?? 'Admin')->before(' ');
        $pendingCount = (int) ($stats['pending_partnerships'] ?? 0);
    @endphp

    <div class="dashboard-page charts-loading">
        <div class="page-head">
            <div>
                <span class="eyebrow">Admin overview</span>
                <h1 class="page-title">Welcome back, {{ $adminFirstName }}</h1>
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

        <section class="dstat-grid" aria-label="System statistics">
            <article class="dstat-card">
                <span class="dstat-label">Total users</span>
                <span class="dstat-value is-pine" data-count-to="{{ $stats['total_users'] }}">0</span>
                <span class="dstat-foot">All registered accounts</span>
            </article>

            <article class="dstat-card">
                <span class="dstat-label">Administrators</span>
                <span class="dstat-value" data-count-to="{{ $stats['admins'] }}">0</span>
                <span class="dstat-foot">With full system access</span>
            </article>

            <article class="dstat-card">
                <span class="dstat-label">Cashiers</span>
                <span class="dstat-value" data-count-to="{{ $stats['cashiers'] }}">0</span>
                <span class="dstat-foot">Payment staff accounts</span>
            </article>

            <article class="dstat-card">
                <span class="dstat-label">Pending review</span>
                <span class="dstat-value {{ $pendingCount > 0 ? 'is-amber' : '' }}" data-count-to="{{ $pendingCount }}">0</span>
                <span class="dstat-foot">Partnership applications awaiting action</span>
            </article>
        </section>

        <div class="dashboard-charts-row">
            <div class="panel chart-panel" id="panel_monthly">
                <div class="chart-panel-head">
                    <div>
                        <h2 class="panel-title">Monthly payments</h2>
                        <p class="panel-sub">Revenue collected over the last 6 months.</p>
                    </div>
                    <div class="chart-menu-wrap">
                        <button type="button" class="chart-menu-btn" data-menu-btn aria-haspopup="true" aria-expanded="false" aria-label="Monthly payments options">&#8943;</button>
                        <div class="chart-menu pop" data-menu hidden></div>
                    </div>
                </div>
                <div class="chart-body">
                    <div class="chart-body-inner">
                        <div id="chart_admin_monthly"></div>
                    </div>
                </div>
            </div>

            <div class="panel chart-panel" id="panel_status">
                <div class="chart-panel-head">
                    <div>
                        <h2 class="panel-title">Application status</h2>
                        <p class="panel-sub">Partnership applications by outcome.</p>
                    </div>
                    <div class="chart-menu-wrap">
                        <button type="button" class="chart-menu-btn" data-menu-btn aria-haspopup="true" aria-expanded="false" aria-label="Application status options">&#8943;</button>
                        <div class="chart-menu pop" data-menu hidden></div>
                    </div>
                </div>
                <div class="chart-body">
                    <div class="chart-body-inner">
                        <div id="chart_admin_status"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="dashboard-charts-row alt">
            <div class="panel chart-panel" id="panel_trend">
                <div class="chart-panel-head">
                    <div>
                        <h2 class="panel-title">Applications over time</h2>
                        <p class="panel-sub">New partnership applications per month.</p>
                    </div>
                    <div class="chart-menu-wrap">
                        <button type="button" class="chart-menu-btn" data-menu-btn aria-haspopup="true" aria-expanded="false" aria-label="Applications over time options">&#8943;</button>
                        <div class="chart-menu pop" data-menu hidden></div>
                    </div>
                </div>
                <div class="chart-body">
                    <div class="chart-body-inner">
                        <div id="chart_admin_trend"></div>
                    </div>
                </div>
            </div>

            <div class="panel chart-panel" id="panel_top">
                <div class="chart-panel-head">
                    <div>
                        <h2 class="panel-title">Top concessionaires</h2>
                        <p class="panel-sub">Ranked by total store and product reviews.</p>
                    </div>
                    <div class="chart-menu-wrap">
                        <button type="button" class="chart-menu-btn" data-menu-btn aria-haspopup="true" aria-expanded="false" aria-label="Top concessionaires options">&#8943;</button>
                        <div class="chart-menu pop" data-menu hidden></div>
                    </div>
                </div>
                <div class="chart-body">
                    <div class="chart-body-inner">
                        <div id="chart_admin_top"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Users -->
        <div class="card">
            <div class="card-header">
                <h3>Recent users</h3>
                <a href="{{ route('admin.users') }}" class="btn btn-outline btn-sm">View all</a>
            </div>
            <div class="card-body">
                <table>
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Role</th>
                            <th>Joined</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($stats['recent_users'] as $user)
                            <tr>
                                <td>
                                    <div class="user-cell">
                                        <div class="user-avatar">{{ $user->initials() }}</div>
                                        <div>
                                            <div class="user-name">{{ $user->name }}</div>
                                            <div class="user-email">{{ $user->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge {{ $user->role === 'admin' ? 'badge-admin' : 'badge-user' }}">
                                        {{ ucfirst($user->role) }}
                                    </span>
                                </td>
                                <td><span class="table-num table-dim">{{ $user->created_at->format('M d, Y') }}</span></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" style="text-align:center;padding:32px;color:var(--faint);">No users yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    // ---------- Count-up (stat values) ----------
    document.querySelectorAll('[data-count-to]').forEach(function (element) {
        const target = parseFloat(element.getAttribute('data-count-to')) || 0;
        const decimals = parseInt(element.getAttribute('data-decimals') || '0', 10);
        const prefix = element.getAttribute('data-prefix') || '';

        const formatValue = function (value) {
            return prefix + Number(value).toLocaleString('en-US', {
                minimumFractionDigits: decimals,
                maximumFractionDigits: decimals
            });
        };

        if (prefersReducedMotion || target === 0) { element.textContent = formatValue(target); return; }

        const duration = 900;
        const start = performance.now();
        const tick = function (now) {
            const progress = Math.min(1, (now - start) / duration);
            const eased = 1 - Math.pow(1 - progress, 3);
            element.textContent = formatValue(target * eased);
            if (progress < 1) requestAnimationFrame(tick);
        };
        requestAnimationFrame(tick);
    });

    // ================= Chart panel menus: collapse + downloads =================
    function csvEscape(value) {
        value = String(value === null || value === undefined ? '' : value);
        return /[",\n]/.test(value) ? '"' + value.replace(/"/g, '""') + '"' : value;
    }

    function rowsToCSV(rows) {
        return rows.map(function (row) { return row.map(csvEscape).join(','); }).join('\r\n');
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
        document.querySelectorAll('.chart-menu').forEach(function (menu) { menu.hidden = true; });
        document.querySelectorAll('[data-menu-btn]').forEach(function (btn) { btn.setAttribute('aria-expanded', 'false'); });
    }

    function wireChartPanel(panelId, actions) {
        var panel = document.getElementById(panelId);
        if (!panel) return;

        var btn = panel.querySelector('[data-menu-btn]');
        var menu = panel.querySelector('[data-menu]');
        if (!btn || !menu) return;

        // Restore the saved collapse state without replaying the open/close animation.
        var storageKey = 'adminDash:collapsed:' + panelId;
        var savedCollapsed = false;
        try { savedCollapsed = localStorage.getItem(storageKey) === '1'; } catch (e) {}
        if (savedCollapsed) {
            panel.classList.add('no-anim', 'is-collapsed');
            requestAnimationFrame(function () {
                requestAnimationFrame(function () { panel.classList.remove('no-anim'); });
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
                item.addEventListener('click', function () { closeAllChartMenus(); action.run(); });
                menu.appendChild(item);
            });
        }

        btn.addEventListener('click', function (event) {
            event.stopPropagation();
            var willOpen = menu.hidden;
            closeAllChartMenus();
            if (willOpen) { menu.hidden = false; btn.setAttribute('aria-expanded', 'true'); }
        });
    }

    document.addEventListener('click', function (event) {
        if (!event.target.closest('.chart-menu-wrap')) closeAllChartMenus();
    });
    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') closeAllChartMenus();
    });

    // ================= Charts =================
    const appStatus = @json($applicationStatusData);
    const monthlyPayments = @json($monthlyPaymentsData);
    const appTrend = @json($applicationsTrendData);
    const topConc = @json($topConcessionairesData);
    const statusTotal = (appStatus.pending || 0) + (appStatus.approved || 0) + (appStatus.rejected || 0);

    var pesoFormatter = new Intl.NumberFormat('en-PH', { style: 'currency', currency: 'PHP', minimumFractionDigits: 0, maximumFractionDigits: 0 });
    function formatPeso(value) { return pesoFormatter.format(Math.round(value || 0)); }

    var chartFont = getComputedStyle(document.documentElement).getPropertyValue('--font-ui').trim() || 'Manrope, sans-serif';

    // Re-enable collapse transitions only once the charts have drawn, so the
    // initial render never plays a collapse/expand animation.
    var dashboardPageEl = document.querySelector('.dashboard-page');
    var chartsMounted = 0;
    var chartsExpected = 0;
    function releaseChartTransitions() {
        if (dashboardPageEl) dashboardPageEl.classList.remove('charts-loading');
    }
    function chartMountedTick() {
        chartsMounted++;
        if (chartsExpected > 0 && chartsMounted >= chartsExpected) {
            requestAnimationFrame(function () { requestAnimationFrame(releaseChartTransitions); });
        }
    }
    // Fallback so transitions are never stuck off if a chart fails to mount.
    setTimeout(releaseChartTransitions, 1500);

    var chartBase = {
        fontFamily: chartFont,
        foreColor: '#66756C',
        toolbar: { show: false },
        animations: { enabled: !prefersReducedMotion },
        events: { mounted: chartMountedTick }
    };

    var monthlyChart = null;
    var statusChart = null;
    var trendChart = null;
    var topChart = null;

    if (document.querySelector('#chart_admin_monthly') && typeof ApexCharts !== 'undefined') {
        monthlyChart = new ApexCharts(document.querySelector('#chart_admin_monthly'), {
            chart: Object.assign({}, chartBase, { height: 320, type: 'bar', width: '100%' }),
            series: [{ name: 'Revenue', data: monthlyPayments.map(function (m) { return m.total; }) }],
            colors: ['#0A5C2F'],
            plotOptions: { bar: { columnWidth: '48%', borderRadius: 4, borderRadiusApplication: 'end' } },
            dataLabels: { enabled: false },
            xaxis: {
                categories: monthlyPayments.map(function (m) { return m.month; }),
                labels: { style: { fontSize: '11px', fontWeight: 600 } },
                axisBorder: { show: false },
                axisTicks: { show: false }
            },
            yaxis: {
                min: 0,
                forceNiceScale: true,
                labels: { style: { fontSize: '11px' }, formatter: function (v) { return formatPeso(v); } }
            },
            legend: { show: false },
            tooltip: {
                theme: 'dark', style: { fontSize: '12px' },
                y: { formatter: function (v) { return '₱' + Number(v).toLocaleString('en-US', { minimumFractionDigits: 2 }); } }
            },
            grid: { borderColor: '#EDF2EE', strokeDashArray: 4, padding: { left: 6, right: 6 } }
        });
        chartsExpected++;
        setTimeout(function () { monthlyChart.render(); }, 50);
    }

    var statusTarget = document.querySelector('#chart_admin_status');
    if (statusTarget && typeof ApexCharts !== 'undefined') {
        if (statusTotal === 0) {
            statusTarget.innerHTML = '<p class="chart-empty">No partnership applications yet.</p>';
        } else {
            statusChart = new ApexCharts(statusTarget, {
                chart: Object.assign({}, chartBase, { height: 300, type: 'donut', width: '100%' }),
                series: [appStatus.pending || 0, appStatus.approved || 0, appStatus.rejected || 0],
                labels: ['Pending', 'Approved', 'Rejected'],
                colors: ['#D97706', '#0A5C2F', '#B91C1C'],
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
                                value: { fontSize: '26px', fontWeight: 600, color: '#1A2B21', offsetY: -12, formatter: function (v) { return v; } },
                                total: {
                                    show: true, label: 'Applications', fontSize: '12px', color: '#66756C',
                                    formatter: function (w) { return w.globals.seriesTotals.reduce(function (a, b) { return a + b; }, 0); }
                                }
                            }
                        }
                    }
                },
                tooltip: { theme: 'dark', style: { fontSize: '12px' }, y: { formatter: function (v) { return v + ' ' + (v === 1 ? 'application' : 'applications'); } } },
                legend: { show: true, position: 'bottom', horizontalAlign: 'center', fontSize: '12px', fontWeight: 600, markers: { size: 5, shape: 'circle' }, itemMargin: { horizontal: 10 } },
                responsive: [{ breakpoint: 600, options: { chart: { height: 260 } } }]
            });
            chartsExpected++;
            setTimeout(function () { statusChart.render(); }, 50);
        }
    }

    if (document.querySelector('#chart_admin_trend') && typeof ApexCharts !== 'undefined') {
        trendChart = new ApexCharts(document.querySelector('#chart_admin_trend'), {
            chart: Object.assign({}, chartBase, { height: 300, type: 'area', width: '100%' }),
            series: [{ name: 'Applications', data: appTrend.map(function (m) { return m.count; }) }],
            colors: ['#0A5C2F'],
            stroke: { curve: 'smooth', width: 2.5 },
            dataLabels: { enabled: false },
            fill: {
                type: 'gradient',
                gradient: { shadeIntensity: 1, opacityFrom: 0.28, opacityTo: 0.03, stops: [0, 90, 100] }
            },
            markers: { size: 3, colors: ['#0A5C2F'], strokeColors: '#fff', strokeWidth: 2, hover: { size: 5 } },
            xaxis: {
                categories: appTrend.map(function (m) { return m.month; }),
                labels: { style: { fontSize: '11px', fontWeight: 600 } },
                axisBorder: { show: false },
                axisTicks: { show: false }
            },
            yaxis: {
                min: 0,
                forceNiceScale: true,
                labels: { style: { fontSize: '11px' }, formatter: function (v) { return Math.round(v); } }
            },
            legend: { show: false },
            tooltip: { theme: 'dark', style: { fontSize: '12px' } },
            grid: { borderColor: '#EDF2EE', strokeDashArray: 4, padding: { left: 6, right: 6 } }
        });
        chartsExpected++;
        setTimeout(function () { trendChart.render(); }, 50);
    }

    var topTarget = document.querySelector('#chart_admin_top');
    if (topTarget && typeof ApexCharts !== 'undefined') {
        if (!topConc.length) {
            topTarget.innerHTML = '<p class="chart-empty">No reviewed concessionaires yet.</p>';
        } else {
            topChart = new ApexCharts(topTarget, {
                chart: Object.assign({}, chartBase, { height: 320, type: 'bar', width: '100%' }),
                series: [{ name: 'Reviews', data: topConc.map(function (c) { return c.reviews; }) }],
                colors: ['#0A5C2F'],
                plotOptions: { bar: { horizontal: true, barHeight: '55%', borderRadius: 4, borderRadiusApplication: 'end' } },
                dataLabels: { enabled: false },
                xaxis: {
                    categories: topConc.map(function (c) { return c.name; }),
                    labels: { style: { fontSize: '11px' }, formatter: function (v) { return Math.round(v); } },
                    axisBorder: { show: false },
                    axisTicks: { show: false }
                },
                yaxis: { labels: { style: { fontSize: '11.5px', fontWeight: 600 }, maxWidth: 180 } },
                legend: { show: false },
                tooltip: { theme: 'dark', style: { fontSize: '12px' }, y: { formatter: function (v) { return v + ' ' + (v === 1 ? 'review' : 'reviews'); } } },
                grid: { borderColor: '#EDF2EE', strokeDashArray: 4, padding: { left: 6, right: 6 } }
            });
            chartsExpected++;
            setTimeout(function () { topChart.render(); }, 50);
        }
    }

    // ---------- Report registry (shared by per-panel menus and "Download reports") ----------
    var reportRegistry = [
        {
            panel: 'panel_monthly',
            chart: function () { return monthlyChart; },
            pngName: 'monthly-payments.png',
            csvName: 'monthly-payments.csv',
            csvHeaders: ['Month', 'Revenue'],
            csvRows: function () {
                return monthlyPayments.map(function (m) { return [m.month, m.total]; });
            }
        },
        {
            panel: 'panel_status',
            chart: function () { return statusChart; },
            pngName: 'application-status.png',
            csvName: 'application-status.csv',
            csvHeaders: ['Status', 'Applications'],
            csvRows: function () {
                return [
                    ['Pending', appStatus.pending || 0],
                    ['Approved', appStatus.approved || 0],
                    ['Rejected', appStatus.rejected || 0]
                ];
            }
        },
        {
            panel: 'panel_trend',
            chart: function () { return trendChart; },
            pngName: 'applications-over-time.png',
            csvName: 'applications-over-time.csv',
            csvHeaders: ['Month', 'Applications'],
            csvRows: function () {
                return appTrend.map(function (m) { return [m.month, m.count]; });
            }
        },
        {
            panel: 'panel_top',
            chart: function () { return topChart; },
            pngName: 'top-concessionaires.png',
            csvName: 'top-concessionaires.csv',
            csvHeaders: ['Concessionaire', 'Reviews'],
            csvRows: function () {
                return topConc.map(function (c) { return [c.name, c.reviews]; });
            }
        }
    ];

    reportRegistry.forEach(function (report) {
        var hasChart = !!report.chart();
        wireChartPanel(report.panel, [
            { label: 'Download PNG', available: hasChart, run: function () { downloadPNG(report.chart, report.pngName); } },
            { label: 'Download CSV', run: function () { downloadCSV(report.csvName, report.csvHeaders, report.csvRows()); } }
        ]);
    });

    // wireChartPanel has now applied the real .is-collapsed classes, so the
    // pre-paint override can be dropped (leaving it would block expanding).
    var collapsePreload = document.getElementById('collapse-preload');
    if (collapsePreload) collapsePreload.remove();

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
        downloadCSVString('admin-dashboard-reports.csv', blocks.join('\r\n\r\n'));
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
            ctx.fillStyle = '#1A2B21';
            ctx.font = '700 ' + (22 * scale) + 'px ' + chartFont;
            ctx.fillText('Admin Dashboard Reports', pad, y);
            ctx.fillStyle = '#66756C';
            ctx.font = '600 ' + (13 * scale) + 'px ' + chartFont;
            ctx.fillText(dashboardDateLabel(), pad, y + (28 * scale));
            y += headerH + gap;

            items.forEach(function (it) {
                ctx.fillStyle = '#1A2B21';
                ctx.font = '700 ' + (15 * scale) + 'px ' + chartFont;
                ctx.fillText(it.title, pad, y);
                y += titleH;
                ctx.drawImage(it.img, pad, y, it.img.width, it.img.height);
                y += it.img.height + gap;
            });

            var link = document.createElement('a');
            link.href = canvas.toDataURL('image/png');
            link.download = 'admin-dashboard-reports.png';
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
            item.addEventListener('click', function () { closeAllChartMenus(); action.run(); });
            menu.appendChild(item);
        });

        btn.addEventListener('click', function (event) {
            event.stopPropagation();
            var willOpen = menu.hidden;
            closeAllChartMenus();
            if (willOpen) { menu.hidden = false; btn.setAttribute('aria-expanded', 'true'); }
        });
    })();
</script>
@endsection
