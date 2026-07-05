@extends('cashier.layout')

@section('title', 'Dashboard')

@section('content')
    <div class="summary-grid">
        <!-- Active Concessionaires Card -->
        <div class="summary-card bg-gradient-to-br from-blue-50 to-blue-100 border-blue-200 hover:-translate-y-1 hover:shadow-xl transition-all duration-200">
            <div class="flex justify-between items-start mb-3">
                <div>
                    <div class="summary-label text-blue-700">Active Concessionaires</div>
                    <div class="summary-value text-blue-900">{{ $activeConcessionairesCount ?? 0 }}</div>
                </div>
                <div class="p-3 bg-blue-500 rounded-lg">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
            </div>
            <div id="sparkline-active" class="mt-2" style="height: 50px;"></div>
        </div>

        <!-- Ready to Record Card -->
        <div class="summary-card bg-gradient-to-br from-emerald-50 to-emerald-100 border-emerald-200 hover:-translate-y-1 hover:shadow-xl transition-all duration-200">
            <div class="flex justify-between items-start mb-3">
                <div>
                    <div class="summary-label text-emerald-700">Ready to Record</div>
                    <div class="summary-value text-emerald-900">{{ $readyToRecordCount ?? 0 }}</div>
                </div>
                <div class="p-3 bg-emerald-500 rounded-lg">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <div id="sparkline-ready" class="mt-2" style="height: 50px;"></div>
        </div>

        <!-- Overdue This Month Card -->
        <div class="summary-card bg-gradient-to-br from-amber-50 to-amber-100 border-amber-200 hover:-translate-y-1 hover:shadow-xl transition-all duration-200">
            <div class="flex justify-between items-start mb-3">
                <div>
                    <div class="summary-label text-amber-700">Overdue This Month</div>
                    <div class="summary-value text-amber-900">{{ $overdueCount ?? 0 }}</div>
                </div>
                <div class="p-3 bg-amber-500 rounded-lg">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <div id="sparkline-overdue" class="mt-2" style="height: 50px;"></div>
        </div>
    </div>

    <div class="charts-grid">
        <div class="chart-card">
            <div id="chart_cashier_monthly"></div>
        </div>
        <div class="chart-card">
            <div id="chart_cashier_types"></div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        (() => {
            if (typeof ApexCharts !== 'undefined') {
                // Sparkline for Active Concessionaires
                var sparklineActive = new ApexCharts(document.querySelector("#sparkline-active"), {
                    chart: { type: 'area', height: 50, sparkline: { enabled: true } },
                    stroke: { curve: 'smooth', width: 2 },
                    fill: { opacity: 0.3, type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.5, opacityTo: 0.1 } },
                    series: [{ data: [3, 4, 3, 5, 4, 5, 5] }],
                    colors: ['#3b82f6'],
                    tooltip: { enabled: false }
                });
                sparklineActive.render();

                // Sparkline for Ready to Record
                var sparklineReady = new ApexCharts(document.querySelector("#sparkline-ready"), {
                    chart: { type: 'area', height: 50, sparkline: { enabled: true } },
                    stroke: { curve: 'smooth', width: 2 },
                    fill: { opacity: 0.3, type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.5, opacityTo: 0.1 } },
                    series: [{ data: [1, 2, 1, 3, 2, 1, 1] }],
                    colors: ['#10b981'],
                    tooltip: { enabled: false }
                });
                sparklineReady.render();

                // Sparkline for Overdue
                var sparklineOverdue = new ApexCharts(document.querySelector("#sparkline-overdue"), {
                    chart: { type: 'area', height: 50, sparkline: { enabled: true } },
                    stroke: { curve: 'smooth', width: 2 },
                    fill: { opacity: 0.3, type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.5, opacityTo: 0.1 } },
                    series: [{ data: [2, 1, 2, 1, 0, 1, 1] }],
                    colors: ['#f59e0b'],
                    tooltip: { enabled: false }
                });
                sparklineOverdue.render();

                var cashierMonthly = @json($cashierMonthlyPayments);
                var options1 = {
                    chart: { 
                        height: 350, 
                        type: "line", 
                        width: "100%", 
                        toolbar: { show: false },
                        dropShadow: { enabled: true, top: 4, left: 0, blur: 3, opacity: 0.1 }
                    },
                    series: [
                        { name: "Volume (Bar)", type: 'column', data: cashierMonthly.map(m => m.total) },
                        { name: "Trend (Line)", type: 'line', data: cashierMonthly.map(m => m.total) }
                    ],
                    stroke: { width: [0, 4], curve: 'smooth' },
                    plotOptions: { bar: { columnWidth: "35%", borderRadius: 6 } },
                    colors: ["#bae6fd", "#2563eb"],
                    fill: { type: ['solid', 'solid'] },
                    markers: { size: [0, 6], strokeColors: '#ffffff', strokeWidth: 2, hover: { size: 8 } },
                    xaxis: { 
                        categories: cashierMonthly.map(m => m.month),
                        axisBorder: { show: false },
                        axisTicks: { show: false },
                        labels: { style: { colors: '#64748b' } }
                    },
                    yaxis: { 
                        labels: { formatter: function(val) { return "₱" + Number(val).toLocaleString(); }, style: { colors: '#64748b' } }
                    },
                    grid: { borderColor: "#f1f5f9", strokeDashArray: 4 },
                    title: { text: "Revenue Growth Combo", align: "left", style: { fontWeight: "700", color: '#1e293b', fontSize: '16px' } },
                    legend: { show: false },
                    tooltip: { shared: true, intersect: false, theme: 'dark', y: { formatter: function(val) { return "₱" + Number(val).toLocaleString(); } } }
                };
                var chart1 = new ApexCharts(document.querySelector("#chart_cashier_monthly"), options1);
                chart1.render();

                var cashierTypes = @json($cashierPaymentTypes);
                var tCash = cashierTypes.cash || 0;
                var tCheck = cashierTypes.check || 0;
                var tBank = cashierTypes.bank_transfer || 0;
                var tTotal = tCash + tCheck + tBank;
                var pCash = tTotal ? Math.round((tCash/tTotal)*100) : 0;
                var pCheck = tTotal ? Math.round((tCheck/tTotal)*100) : 0;
                var pBank = tTotal ? Math.round((tBank/tTotal)*100) : 0;

                var options2 = {
                    chart: { 
                        height: 370, 
                        type: "radialBar",
                        dropShadow: { enabled: true, top: 3, left: 0, blur: 4, opacity: 0.1 }
                    },
                    series: [pCash, pCheck, pBank],
                    labels: ['Cash', 'Check', 'Bank Transfer'],
                    colors: ["#10b981", "#3b82f6", "#f59e0b"],
                    plotOptions: {
                        radialBar: {
                            startAngle: -135,
                            endAngle: 135,
                            hollow: { size: '40%' },
                            track: { background: '#f1f5f9', strokeWidth: '100%', margin: 12 },
                            dataLabels: {
                                name: { fontSize: '14px', color: '#64748b', offsetY: 20 },
                                value: { fontSize: '26px', fontWeight: 800, color: '#0f172a', offsetY: -12, formatter: function(val) { return val + "%"; } },
                                total: { show: true, label: 'Transactions', color: '#64748b', fontSize: '12px', formatter: function(w) { return tTotal; } }
                            }
                        }
                    },
                    stroke: { lineCap: 'round' },
                    legend: { show: true, position: 'bottom', horizontalAlign: 'center', fontSize: '14px', markers: { radius: 12 } },
                    title: { text: "Payment Distribution Gauge", align: "left", style: { fontWeight: "700", color: '#1e293b', fontSize: '16px' } }
                };
                var chart2 = new ApexCharts(document.querySelector("#chart_cashier_types"), options2);
                chart2.render();
            }
        })();
    </script>
@endsection
