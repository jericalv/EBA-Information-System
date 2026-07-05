@extends('admin.layout')

@section('title', 'Dashboard')

@section('extra-css')
<style>
    /* Stats Grid */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 20px;
        margin-bottom: 28px;
    }

    .stat-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        border-top-width: 3px;
        position: relative;
        display: flex;
        flex-direction: column;
        gap: 8px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        overflow: hidden;
        cursor: pointer;
    }
    .stat-card::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 80px;
        opacity: 0.03;
        pointer-events: none;
        background: linear-gradient(180deg, transparent 0%, currentColor 100%);
        transition: opacity 0.3s ease;
    }
    .stat-card:hover {
        transform: translateY(-4px) scale(1.02);
        box-shadow: 0 12px 28px rgba(0,0,0,0.12), 0 0 40px rgba(0,0,0,0.04);
        border-color: currentColor;
    }
    .stat-card:hover::after {
        opacity: 0.05;
    }
    .stat-card:active {
        transform: translateY(-2px) scale(1.01);
        transition: all 0.1s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .stat-card:nth-child(1) { 
        border-top-color: #10b981;
        color: #10b981;
    }
    .stat-card:nth-child(2) { 
        border-top-color: #f59e0b;
        color: #f59e0b;
    }
    .stat-card:nth-child(3) { 
        border-top-color: #3b82f6;
        color: #3b82f6;
    }
    .stat-card:nth-child(4) { 
        border-top-color: #8b5cf6;
        color: #8b5cf6;
    }

    .stat-card-top {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 8px;
    }
    .stat-card-info {
        flex: 1;
        min-width: 0;
    }
    .stat-card-icon {
        width: 52px;
        height: 52px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .stat-card:hover .stat-card-icon {
        transform: scale(1.1) rotate(5deg);
    }
    .stat-card:active .stat-card-icon {
        transform: scale(1.05) rotate(2deg);
    }
    .stat-card-icon svg {
        width: 26px;
        height: 26px;
        color: #fff;
    }
    .stat-icon-green {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        box-shadow: 0 4px 14px rgba(16, 185, 129, 0.3);
    }
    .stat-icon-gold {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        box-shadow: 0 4px 14px rgba(245, 158, 11, 0.3);
    }
    .stat-icon-blue {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        box-shadow: 0 4px 14px rgba(59, 130, 246, 0.3);
    }
    .stat-icon-purple {
        background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
        box-shadow: 0 4px 14px rgba(139, 92, 246, 0.3);
    }
    .stat-card-label {
        font-size: 12px;
        color: #64748b;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        margin-bottom: 6px;
    }
    .stat-card-value {
        font-size: 36px;
        font-weight: 800;
        color: #0f172a;
        line-height: 1;
        margin-bottom: 12px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .stat-card:hover .stat-card-value {
        transform: translateX(2px);
        color: #1e293b;
    }
    .stat-card-sparkline {
        height: 50px;
        margin: 0 -8px;
        position: relative;
        z-index: 1;
    }
    .stat-card-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding-top: 12px;
        border-top: 1px solid #f1f5f9;
        position: relative;
        z-index: 1;
    }
    .stat-card-footer-label {
        font-size: 11px;
        font-weight: 600;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .stat-card-trend {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 4px 8px;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 700;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .stat-card:hover .stat-card-trend {
        transform: scale(1.05);
    }
    .stat-card-trend.up {
        background: #dcfce7;
        color: #166534;
    }
    .stat-card-trend.down {
        background: #fee2e2;
        color: #991b1b;
    }
    .stat-card-trend.neutral {
        background: #f1f5f9;
        color: #64748b;
    }

    /* Charts Grid */
    .dashboard-charts-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 20px;
        margin-bottom: 28px;
    }
    .dashboard-chart-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        padding: 24px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        overflow: hidden;
        position: relative;
    }
    .dashboard-chart-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
        transition: left 0.5s ease;
    }
    .dashboard-chart-card:hover {
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
        transform: translateY(-4px);
        border-color: #cbd5e1;
    }
    .dashboard-chart-card:hover::before {
        left: 100%;
    }
    .dashboard-chart-card .apexcharts-title-text {
        font-size: 16px !important;
        font-weight: 700 !important;
        color: #0f172a !important;
        fill: #0f172a !important;
    }

    @media (max-width: 1024px) {
        .dashboard-charts-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection

@section('content')
    <!-- Stats -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-card-top">
                <div class="stat-card-info">
                    <div class="stat-card-label">Total Users</div>
                    <div class="stat-card-value">{{ number_format($stats['total_users']) }}</div>
                </div>
                <div class="stat-card-icon stat-icon-green">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                    </svg>
                </div>
            </div>
            <div class="stat-card-sparkline" id="sparkline_users"></div>
            <div class="stat-card-footer">
                <span class="stat-card-footer-label">All registered</span>
                @php
                    $userChange = count($usersTrend) > 1 ? end($usersTrend) - $usersTrend[0] : 0;
                @endphp
                <span class="stat-card-trend {{ $userChange > 0 ? 'up' : ($userChange < 0 ? 'down' : 'neutral') }}">
                    {{ $userChange > 0 ? '↑' : ($userChange < 0 ? '↓' : '—') }} {{ abs($userChange) }}
                </span>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-card-top">
                <div class="stat-card-info">
                    <div class="stat-card-label">Administrators</div>
                    <div class="stat-card-value">{{ number_format($stats['admins']) }}</div>
                </div>
                <div class="stat-card-icon stat-icon-gold">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                    </svg>
                </div>
            </div>
            <div class="stat-card-sparkline" id="sparkline_admins"></div>
            <div class="stat-card-footer">
                <span class="stat-card-footer-label">System admins</span>
                @php
                    $adminChange = count($adminsTrend) > 1 ? end($adminsTrend) - $adminsTrend[0] : 0;
                @endphp
                <span class="stat-card-trend {{ $adminChange > 0 ? 'up' : ($adminChange < 0 ? 'down' : 'neutral') }}">
                    {{ $adminChange > 0 ? '↑' : ($adminChange < 0 ? '↓' : '—') }} {{ abs($adminChange) }}
                </span>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-card-top">
                <div class="stat-card-info">
                    <div class="stat-card-label">Cashiers</div>
                    <div class="stat-card-value">{{ number_format($stats['cashiers']) }}</div>
                </div>
                <div class="stat-card-icon stat-icon-blue">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                        <circle cx="12" cy="7" r="4"/>
                    </svg>
                </div>
            </div>
            <div class="stat-card-sparkline" id="sparkline_cashiers"></div>
            <div class="stat-card-footer">
                <span class="stat-card-footer-label">Payment staff</span>
                @php
                    $cashierChange = count($cashiersTrend) > 1 ? end($cashiersTrend) - $cashiersTrend[0] : 0;
                @endphp
                <span class="stat-card-trend {{ $cashierChange > 0 ? 'up' : ($cashierChange < 0 ? 'down' : 'neutral') }}">
                    {{ $cashierChange > 0 ? '↑' : ($cashierChange < 0 ? '↓' : '—') }} {{ abs($cashierChange) }}
                </span>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-card-top">
                <div class="stat-card-info">
                    <div class="stat-card-label">Pending Review</div>
                    <div class="stat-card-value">{{ number_format($stats['pending_partnerships']) }}</div>
                </div>
                <div class="stat-card-icon stat-icon-purple">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M9 12h6m-6 4h6m2 5H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5.586a1 1 0 0 1 .707.293l5.414 5.414a1 1 0 0 1 .293.707V19a2 2 0 0 1-2 2z"/>
                    </svg>
                </div>
            </div>
            <div class="stat-card-sparkline" id="sparkline_partnerships"></div>
            <div class="stat-card-footer">
                <span class="stat-card-footer-label">Applications</span>
                @php
                    $partnershipChange = count($partnershipsTrend) > 1 ? end($partnershipsTrend) - $partnershipsTrend[0] : 0;
                @endphp
                <span class="stat-card-trend {{ $partnershipChange > 0 ? 'up' : ($partnershipChange < 0 ? 'down' : 'neutral') }}">
                    {{ $partnershipChange > 0 ? '↑' : ($partnershipChange < 0 ? '↓' : '—') }} {{ abs($partnershipChange) }}
                </span>
            </div>
        </div>
    </div>

    <div class="dashboard-charts-grid">
        <div class="dashboard-chart-card">
            <div id="chart_application_status"></div>
        </div>
        <div class="dashboard-chart-card">
            <div id="chart_monthly_payments"></div>
        </div>
        <div class="dashboard-chart-card">
            <div id="chart_applications_trend"></div>
        </div>
        <div class="dashboard-chart-card">
            <div id="chart_top_concessionaires"></div>
        </div>
    </div>

    <!-- Recent Users -->
    <div class="card">
        <div class="card-header">
            <h3>Recent Users</h3>
            <a href="{{ route('admin.users') }}" class="btn btn-outline btn-sm">View All</a>
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
                            <td style="color:#64748b; font-size:13px;">
                                {{ $user->created_at->format('M d, Y') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" style="text-align:center;padding:32px;color:#94a3b8;">No users yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        (function () {
            if (typeof ApexCharts === 'undefined') {
                return;
            }

            // Sparkline Charts for Stat Cards
            function createSparkline(elementId, data, color) {
                var options = {
                    chart: {
                        type: 'area',
                        height: 50,
                        sparkline: { enabled: true },
                        animations: {
                            enabled: true,
                            easing: 'easeinout',
                            speed: 600,
                            dynamicAnimation: {
                                enabled: true,
                                speed: 300
                            }
                        }
                    },
                    series: [{
                        name: 'Trend',
                        data: data
                    }],
                    stroke: {
                        curve: 'smooth',
                        width: 2
                    },
                    fill: {
                        type: 'gradient',
                        gradient: {
                            shadeIntensity: 1,
                            opacityFrom: 0.4,
                            opacityTo: 0.1,
                            stops: [0, 90, 100]
                        }
                    },
                    colors: [color],
                    states: {
                        hover: {
                            filter: {
                                type: 'none'
                            }
                        },
                        active: {
                            filter: {
                                type: 'none'
                            }
                        }
                    },
                    tooltip: {
                        enabled: true,
                        theme: 'dark',
                        followCursor: true,
                        x: { show: false },
                        y: {
                            title: {
                                formatter: function() {
                                    return 'Total:';
                                }
                            },
                            formatter: function(val) {
                                return val.toLocaleString();
                            }
                        },
                        marker: {
                            show: true
                        }
                    }
                };
                var chart = new ApexCharts(document.querySelector('#' + elementId), options);
                chart.render();
            }

            // Render sparklines
            createSparkline('sparkline_users', @json($usersTrend), '#10b981');
            createSparkline('sparkline_admins', @json($adminsTrend), '#f59e0b');
            createSparkline('sparkline_cashiers', @json($cashiersTrend), '#3b82f6');
            createSparkline('sparkline_partnerships', @json($partnershipsTrend), '#8b5cf6');

            // Application Status Pie Chart
            var appStatusData = @json($applicationStatusData);
            var options = {
                chart: { 
                    height: 340, 
                    type: "pie", 
                    width: "100%", 
                    toolbar: { show: false },
                    animations: {
                        enabled: true,
                        easing: 'easeinout',
                        speed: 800,
                        animateGradually: {
                            enabled: true,
                            delay: 150
                        },
                        dynamicAnimation: {
                            enabled: true,
                            speed: 350
                        }
                    }
                },
                series: [appStatusData.pending || 0, appStatusData.approved || 0, appStatusData.rejected || 0],
                labels: ["Pending", "Approved", "Rejected"],
                colors: ["#f59e0b", "#10b981", "#ef4444"],
                legend: {
                    show: true, 
                    position: "bottom", 
                    horizontalAlign: "center", 
                    fontSize: "13px",
                    fontWeight: 600,
                    markers: { radius: 4 },
                    onItemHover: {
                        highlightDataSeries: true
                    }
                },
                title: { 
                    text: "Application Status Breakdown", 
                    align: "left", 
                    style: { 
                        fontSize: "16px",
                        fontWeight: 700,
                        color: '#0f172a'
                    } 
                },
                dataLabels: {
                    style: {
                        fontSize: '12px',
                        fontWeight: 700
                    },
                    dropShadow: {
                        enabled: false
                    }
                },
                stroke: {
                    width: 2,
                    colors: ['#fff']
                },
                states: {
                    hover: {
                        filter: {
                            type: 'lighten',
                            value: 0.15
                        }
                    },
                    active: {
                        allowMultipleDataPointsSelection: false,
                        filter: {
                            type: 'darken',
                            value: 0.2
                        }
                    }
                },
                tooltip: {
                    enabled: true,
                    theme: 'dark',
                    style: {
                        fontSize: '13px'
                    },
                    y: {
                        formatter: function(val) {
                            return val + ' applications';
                        }
                    }
                },
                responsive: [{ 
                    breakpoint: 600, 
                    options: { 
                        chart: { height: 280 }, 
                        legend: { position: 'bottom' } 
                    } 
                }]
            };
            var chart1 = new ApexCharts(document.querySelector("#chart_application_status"), options);
            chart1.render();

            // Monthly Payments Bar Chart
            var monthlyPayments = @json($monthlyPaymentsData);
            options = {
                chart: { 
                    height: 360, 
                    type: "bar", 
                    width: "100%", 
                    toolbar: { show: false },
                    animations: {
                        enabled: true,
                        easing: 'easeinout',
                        speed: 800,
                        animateGradually: {
                            enabled: true,
                            delay: 150
                        },
                        dynamicAnimation: {
                            enabled: true,
                            speed: 350
                        }
                    }
                },
                plotOptions: { 
                    bar: { 
                        horizontal: false, 
                        columnWidth: "55%", 
                        borderRadius: 8,
                        borderRadiusApplication: 'end',
                        distributed: false
                    } 
                },
                states: {
                    hover: {
                        filter: {
                            type: 'darken',
                            value: 0.15
                        }
                    },
                    active: {
                        allowMultipleDataPointsSelection: false,
                        filter: {
                            type: 'darken',
                            value: 0.35
                        }
                    }
                },
                dataLabels: { enabled: false },
                stroke: { show: true, width: 2, colors: ["transparent"] },
                series: [{ name: "Revenue (₱)", data: monthlyPayments.map(m => m.total) }],
                colors: ["#3b82f6"],
                xaxis: { 
                    categories: monthlyPayments.map(m => m.month),
                    labels: {
                        style: {
                            fontSize: '12px',
                            fontWeight: 600
                        }
                    }
                },
                yaxis: { 
                    title: { 
                        text: "₱ Amount",
                        style: {
                            fontSize: '13px',
                            fontWeight: 600,
                            color: '#64748b'
                        }
                    },
                    labels: {
                        style: {
                            fontSize: '12px'
                        }
                    }
                },
                grid: { 
                    borderColor: "#f1f5f9",
                    strokeDashArray: 4
                },
                fill: { 
                    opacity: 1,
                    type: 'gradient',
                    gradient: {
                        shade: 'light',
                        type: 'vertical',
                        shadeIntensity: 0.5,
                        opacityFrom: 0.95,
                        opacityTo: 0.85,
                    }
                },
                tooltip: { 
                    enabled: true,
                    theme: 'dark',
                    followCursor: true,
                    intersect: false,
                    shared: true,
                    y: { 
                        formatter: function(val) { 
                            return "₱" + val.toLocaleString(); 
                        } 
                    },
                    style: {
                        fontSize: '13px'
                    },
                    marker: {
                        show: true
                    }
                },
                title: { 
                    text: "Monthly Payments — Last 6 Months", 
                    align: "left", 
                    style: { 
                        fontSize: "16px",
                        fontWeight: 700,
                        color: '#0f172a'
                    } 
                }
            };
            var chart2 = new ApexCharts(document.querySelector("#chart_monthly_payments"), options);
            chart2.render();

            // Applications Trend Area Chart
            var appTrend = @json($applicationsTrendData);
            options = {
                chart: { 
                    height: 360, 
                    type: "area", 
                    width: "100%", 
                    toolbar: { show: false },
                    animations: {
                        enabled: true,
                        easing: 'easeinout',
                        speed: 800,
                        animateGradually: {
                            enabled: true,
                            delay: 150
                        },
                        dynamicAnimation: {
                            enabled: true,
                            speed: 350
                        }
                    }
                },
                dataLabels: { enabled: false },
                stroke: { 
                    curve: "smooth", 
                    width: 3
                },
                series: [{ name: "Applications", data: appTrend.map(m => m.count) }],
                colors: ["#8b5cf6"],
                xaxis: { 
                    categories: appTrend.map(m => m.month),
                    labels: {
                        style: {
                            fontSize: '12px',
                            fontWeight: 600
                        }
                    }
                },
                yaxis: {
                    labels: {
                        style: {
                            fontSize: '12px'
                        }
                    }
                },
                grid: { 
                    borderColor: "#f1f5f9",
                    strokeDashArray: 4
                },
                fill: { 
                    type: "gradient", 
                    gradient: { 
                        shadeIntensity: 1, 
                        opacityFrom: 0.5, 
                        opacityTo: 0.05,
                        stops: [0, 90, 100]
                    } 
                },
                markers: {
                    size: 4,
                    colors: ['#8b5cf6'],
                    strokeColors: '#fff',
                    strokeWidth: 2,
                    hover: {
                        size: 8,
                        sizeOffset: 4
                    }
                },
                states: {
                    hover: {
                        filter: {
                            type: 'none'
                        }
                    },
                    active: {
                        allowMultipleDataPointsSelection: false,
                        filter: {
                            type: 'darken',
                            value: 0.1
                        }
                    }
                },
                tooltip: {
                    enabled: true,
                    theme: 'dark',
                    followCursor: true,
                    intersect: false,
                    shared: true,
                    style: {
                        fontSize: '13px'
                    },
                    marker: {
                        show: true
                    }
                },
                title: { 
                    text: "Applications Over Time", 
                    align: "left", 
                    style: { 
                        fontSize: "16px",
                        fontWeight: 700,
                        color: '#0f172a'
                    } 
                }
            };
            var chart3 = new ApexCharts(document.querySelector("#chart_applications_trend"), options);
            chart3.render();

            // Top Concessionaires Horizontal Bar Chart
            var topConc = @json($topConcessionairesData);
            options = {
                chart: { 
                    height: 360, 
                    type: "bar", 
                    width: "100%", 
                    toolbar: { show: false },
                    animations: {
                        enabled: true,
                        easing: 'easeinout',
                        speed: 800,
                        animateGradually: {
                            enabled: true,
                            delay: 150
                        },
                        dynamicAnimation: {
                            enabled: true,
                            speed: 350
                        }
                    }
                },
                plotOptions: { 
                    bar: { 
                        horizontal: true, 
                        borderRadius: 8,
                        barHeight: '65%'
                    } 
                },
                dataLabels: { 
                    enabled: false
                },
                series: [{ 
                    name: "Reviews",
                    data: topConc.map(c => c.reviews) 
                }],
                colors: ["#10b981"],
                xaxis: { 
                    categories: topConc.map(c => c.name), 
                    title: { 
                        text: "Total Reviews",
                        style: {
                            fontSize: '13px',
                            fontWeight: 600,
                            color: '#64748b'
                        }
                    },
                    labels: {
                        style: {
                            fontSize: '12px'
                        }
                    }
                },
                yaxis: {
                    labels: {
                        style: {
                            fontSize: '12px',
                            fontWeight: 600
                        }
                    }
                },
                grid: { 
                    borderColor: "#f1f5f9",
                    strokeDashArray: 4,
                    xaxis: {
                        lines: {
                            show: true
                        }
                    }
                },
                fill: {
                    type: 'gradient',
                    gradient: {
                        shade: 'light',
                        type: 'horizontal',
                        shadeIntensity: 0.5,
                        opacityFrom: 0.95,
                        opacityTo: 0.85,
                    }
                },
                states: {
                    hover: {
                        filter: {
                            type: 'darken',
                            value: 0.15
                        }
                    },
                    active: {
                        allowMultipleDataPointsSelection: false,
                        filter: {
                            type: 'darken',
                            value: 0.35
                        }
                    }
                },
                tooltip: {
                    enabled: true,
                    theme: 'dark',
                    followCursor: true,
                    intersect: false,
                    shared: true,
                    style: {
                        fontSize: '13px'
                    },
                    marker: {
                        show: true
                    }
                },
                title: { 
                    text: "Top Concessionaires by Reviews", 
                    align: "left", 
                    style: { 
                        fontSize: "16px",
                        fontWeight: 700,
                        color: '#0f172a'
                    } 
                }
            };
            var chart4 = new ApexCharts(document.querySelector("#chart_top_concessionaires"), options);
            chart4.render();
        })();
    </script>
@endsection
