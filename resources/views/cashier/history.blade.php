@extends('cashier.layout')

@section('title', 'Payment History')

@section('content')
    <div class="page-head">
        <div>
            <span class="eyebrow">Collections</span>
            <h1 class="page-title">Payment History</h1>
        </div>
        <span class="page-date">{{ now()->format('l, F d, Y') }}</span>
    </div>

    <div class="card">
        <div class="card-header">
            <div style="display:flex;align-items:flex-end;justify-content:space-between;gap:12px;flex-wrap:wrap;">
                <div>
                    <h2 class="panel-title">Recent payment history</h2>
                    <p class="panel-sub">Every payment recorded across all concessionaires.</p>
                </div>
                <div style="display:inline-flex;align-items:center;gap:8px;">
                    <a href="{{ route('cashier.payments.history.view') }}" target="_blank" rel="noopener" class="btn btn-secondary btn-sm">View History</a>
                    <a href="{{ route('cashier.payments.history.pdf') }}" class="btn btn-primary btn-sm">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M12 4v12m0 0l-4-4m4 4l4-4"/>
                        </svg>
                        Download History
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="history-filter-bar">
                <div class="table-search">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                        <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
                    </svg>
                    <input type="text" id="search_box" placeholder="Search concessionaire&hellip;" autocomplete="off">
                </div>
            </div>

            @if ($recentPayments->isEmpty())
                <div class="empty-state">No payments recorded yet.</div>
            @else
                <table class="payments-table" id="payments_table">
                    <thead>
                        <tr>
                            <th>Concessionaire</th>
                            <th>Amount Paid</th>
                            <th>Payment Date</th>
                            <th>Payment Type</th>
                            <th>Recorded At</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="payments_tbody">
                        @foreach ($recentPayments as $payment)
                            <tr class="payment-row"
                                data-name="{{ strtolower($payment->concessionaire?->business_name ?: $payment->concessionaire?->name) }}">
                                <td><span class="table-strong">{{ $payment->concessionaire?->business_name ?: $payment->concessionaire?->name }}</span></td>
                                <td><span class="table-num is-pine">&#8369;{{ number_format((float) $payment->amount, 2) }}</span></td>
                                <td><span class="table-num">{{ $payment->payment_date?->format('M d, Y') ?: '—' }}</span></td>
                                <td>{{ ucfirst(str_replace('_', ' ', $payment->payment_type)) }}</td>
                                <td><span class="table-num">{{ $payment->created_at?->setTimezone('Asia/Manila')->format('M d, Y h:i A') ?? '—' }}</span></td>
                                <td>
                                    <a class="btn btn-secondary btn-xs" href="{{ route('cashier.payments.receipt', $payment->id) }}">
                                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:13px;height:13px;">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M12 4v12m0 0l-4-4m4 4l4-4"/>
                                        </svg>
                                        Receipt
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div id="no_results_message" style="display:none;text-align:center;padding:24px;color:var(--muted);font-size:13.5px;">No payments match your search.</div>
            @endif
        </div>
    </div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchBox = document.getElementById('search_box');
    const rows = document.querySelectorAll('.payment-row');
    const noResultsMsg = document.getElementById('no_results_message');
    const table = document.getElementById('payments_table');

    function filterRows() {
        const query = searchBox.value.toLowerCase();
        let visibleCount = 0;

        rows.forEach(row => {
            const name = row.getAttribute('data-name') || '';

            if (name.includes(query)) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        if (table) {
            if (visibleCount === 0 && rows.length > 0) {
                table.style.display = 'none';
                noResultsMsg.style.display = 'block';
            } else {
                table.style.display = '';
                noResultsMsg.style.display = 'none';
            }
        }
    }

    if (searchBox) {
        searchBox.addEventListener('input', filterRows);

        // Prefill from the navbar search palette (?q=…) and filter immediately
        const initialQuery = new URLSearchParams(window.location.search).get('q');
        if (initialQuery) {
            searchBox.value = initialQuery;
            filterRows();
        }
    }
});
</script>
@endsection
