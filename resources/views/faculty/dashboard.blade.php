@extends('faculty.layout')

@section('title', 'Dashboard')

@section('extra-css')
<style>
    .dashboard-page {
        display: flex;
        flex-direction: column;
        gap: 20px;
        background: linear-gradient(to bottom, #f8fdf9 0%, #f0f9f4 100%);
        min-height: calc(100vh - 80px);
        padding: 8px;
        margin: -8px;
    }
    .stats-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 24px;
        margin-bottom: 24px;
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
    .stat-trend-indicator.neutral {
        background: rgba(107, 114, 128, 0.1);
        color: #6b7280;
        border: 1px solid rgba(107, 114, 128, 0.2);
    }
    .stat-trend-indicator.negative {
        background: rgba(239, 68, 68, 0.12);
        color: #dc2626;
        border: 1px solid rgba(239, 68, 68, 0.2);
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
    .dashboard-charts-row {
        display: grid;
        grid-template-columns: 50% 50%;
        gap: 24px;
        margin-bottom: 24px;
        align-items: stretch;
    }
    .section-card {
        background: linear-gradient(to bottom, #ffffff 0%, #fafffe 100%);
        border: 1px solid #d4e8dc;
        border-radius: 20px;
        padding: 24px;
        box-shadow: 0 4px 12px rgba(6, 68, 32, 0.06), 0 12px 32px rgba(6, 68, 32, 0.08);
        transition: all 0.3s ease;
        height: 100%;
        display: flex;
        flex-direction: column;
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

    @media (max-width: 992px) {
        .dashboard-charts-row {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection

@section('content')
    <div class="dashboard-page">
        <div class="stats-row">
            <div class="stat-panel" data-stat="apps">
                <div class="stat-panel-header">
                    <div class="stat-trend-indicator positive">
                        <svg width="12" height="12" viewBox="0 0 12 12" fill="none">
                            <path d="M6 2L9 8L3 8L6 2Z" fill="currentColor"/>
                        </svg>
                        <span>Active</span>
                    </div>
                </div>
                <div class="stat-panel-label">Total Applications</div>
                <div class="stat-panel-value" data-target="{{ $totalApplications }}">0</div>
                <div class="stat-panel-help">All submitted partnership records</div>
                <div class="stat-sparkline">
                    <canvas id="sparkline-apps" data-values="{{ max(0, $totalApplications - 5) }},{{ max(0, $totalApplications - 3) }},{{ max(0, $totalApplications - 1) }},{{ $totalApplications }}"></canvas>
                </div>
            </div>
            
            <div class="stat-panel" data-stat="pending">
                <div class="stat-panel-header">
                    <div class="stat-trend-indicator neutral">
                        <span>Awaiting</span>
                    </div>
                </div>
                <div class="stat-panel-label">Pending Review</div>
                <div class="stat-panel-value" data-target="{{ $pendingCount }}">0</div>
                <div class="stat-panel-help">Needs faculty/admin action</div>
                <div class="stat-sparkline">
                    <canvas id="sparkline-pending" data-values="{{ max(0, $pendingCount + 5) }},{{ max(0, $pendingCount + 2) }},{{ max(0, $pendingCount + 1) }},{{ $pendingCount }}"></canvas>
                </div>
            </div>

            <div class="stat-panel" data-stat="approved">
                <div class="stat-panel-header">
                    <div class="stat-trend-indicator positive">
                        <svg width="12" height="12" viewBox="0 0 12 12" fill="none">
                            <path d="M6 2L9 8L3 8L6 2Z" fill="currentColor"/>
                        </svg>
                        <span>{{ $totalConcessionaires }} Active</span>
                    </div>
                </div>
                <div class="stat-panel-label">Approved</div>
                <div class="stat-panel-value" data-target="{{ $approvedCount }}">0</div>
                <div class="stat-panel-help">Partnerships marked approved</div>
                <div class="stat-sparkline">
                    <canvas id="sparkline-approved" data-values="{{ max(0, $approvedCount - 3) }},{{ max(0, $approvedCount - 1) }},{{ $approvedCount }}"></canvas>
                </div>
            </div>

            <div class="stat-panel" data-stat="reviewed">
                <div class="stat-panel-header">
                    <div class="stat-trend-indicator positive">
                        <span>By You</span>
                    </div>
                </div>
                <div class="stat-panel-label">Reviewed by Me</div>
                <div class="stat-panel-value" data-target="{{ $reviewedByMe }}">0</div>
                <div class="stat-panel-help">Recommendations submitted by you</div>
                <div class="stat-sparkline">
                    <canvas id="sparkline-reviewed" data-values="{{ max(0, $reviewedByMe - 2) }},{{ max(0, $reviewedByMe - 1) }},{{ $reviewedByMe }}"></canvas>
                </div>
            </div>
        </div>

        <div class="dashboard-charts-row">
            <!-- Chart 3 -->
            <div class="section-card">
                <h4 class="section-card-title card-title mb-1">Applications Per Month</h4>
                <p class="section-card-subtitle text-sm text-gray-500 mb-4">Total submitted in the last 6 months</p>
                <div id="chart3_spline_area" class="apex-charts" dir="ltr" style="flex:1;min-height:280px;width:100%;" data-labels='@json($appMonthLabels)' data-values='@json($appMonthData)'></div>
            </div>

            <!-- Chart 4 -->
            <div class="section-card">
                <h4 class="section-card-title card-title mb-1">Application Status Distribution</h4>
                <p class="section-card-subtitle text-sm text-gray-500 mb-4">Current count per application status</p>
                <div id="chart4_column" class="apex-charts" dir="ltr" style="flex:1;min-height:280px;width:100%;" data-labels='@json($statusLabelsFormatted)' data-values='@json($statusData)'></div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    // Animated Number Counter
    function animateValue(element, start, end, duration, decimals = 0, prefix = '') {
        const range = end - start;
        const increment = range / (duration / 16);
        let current = start;
        
        const timer = setInterval(() => {
            current += increment;
            if ((increment > 0 && current >= end) || (increment < 0 && current <= end)) {
                current = end;
                clearInterval(timer);
            }
            
            let displayValue = current.toFixed(decimals);
            if (decimals > 0) {
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

    document.addEventListener('DOMContentLoaded', () => {
        // Initialize Counters
        document.querySelectorAll('.stat-panel-value[data-target]').forEach(element => {
            const target = parseFloat(element.getAttribute('data-target'));
            animateValue(element, 0, target, 1200, 0);
        });
        
        // Initialize Sparklines
        const sparkconfigs = [
            { id: 'sparkline-apps', color: '#16a34a' },
            { id: 'sparkline-pending', color: '#f59e0b' },
            { id: 'sparkline-approved', color: '#10b981' },
            { id: 'sparkline-reviewed', color: '#3b82f6' }
        ];
        
        sparkconfigs.forEach(config => {
            const canvas = document.getElementById(config.id);
            if (canvas) {
                const values = canvas.getAttribute('data-values').split(',').map(v => parseFloat(v));
                drawSparkline(canvas, values, config.color);
            }
        });

        // Chart 3 — Spline Area
        const chart3Element = document.querySelector("#chart3_spline_area");
        if (chart3Element && typeof ApexCharts !== 'undefined') {
            const appMonthLabels = JSON.parse(chart3Element.dataset.labels || '[]');
            const appMonthData = JSON.parse(chart3Element.dataset.values || '[]');

            var options3 = {
                chart: { height: 350, type: "area", toolbar: { show: false } },
                dataLabels: { enabled: false },
                stroke: { curve: "smooth", width: 3 },
                series: [
                    { name: "Applications", data: appMonthData }
                ],
                colors: ["#34c38f"],
                xaxis: { categories: appMonthLabels },
                grid: { borderColor: "#f1f1f1" }
            };
            new ApexCharts(chart3Element, options3).render();
        }

        // Chart 4 — Column Bar
        const chart4Element = document.querySelector("#chart4_column");
        if (chart4Element && typeof ApexCharts !== 'undefined') {
            const statusLabelsFormatted = JSON.parse(chart4Element.dataset.labels || '[]');
            const statusData = JSON.parse(chart4Element.dataset.values || '[]');

            var options4 = {
                chart: { height: 350, type: "bar", toolbar: { show: false } },
                plotOptions: { bar: { horizontal: false, columnWidth: "40%", borderRadius: 4 } },
                dataLabels: { enabled: false },
                series: [
                    { name: "Applications", data: statusData }
                ],
                colors: ["#f6c23e", "#556ee6", "#34c38f", "#f46a6a", "#74a0fa"],
                xaxis: { categories: statusLabelsFormatted },
                grid: { borderColor: "#f1f1f1" },
                tooltip: {
                    y: { formatter: function(val) { return val + " application(s)"; } }
                }
            };
            new ApexCharts(chart4Element, options4).render();
        }
    });
</script>
@endsection
 
