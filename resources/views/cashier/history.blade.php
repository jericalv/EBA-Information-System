@extends('cashier.layout')

@section('title', 'Payment History')

@section('content')
    <div class="card payment-history-section">
        <div class="card-header">
            <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;">
                <strong style="font-size:16px;color:#111827;">Recent Payment History</strong>
                <div style="display:inline-flex;align-items:center;gap:8px;">
                    <a href="{{ route('cashier.payments.history.view') }}" target="_blank" rel="noopener" class="btn btn-outline"> View History</a>
                    <a href="{{ route('cashier.payments.history.pdf') }}" class="btn btn-green">📂 Download History</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            @php
                $historyFilterConcessionaires = $filterConcessionaires ?? [];
            @endphp

            <div class="history-filter-bar">
                <div>
                    <input type="text" id="search_box" placeholder="Search Concessionaire..." style="width: 100%; max-width: 400px; padding: 10px 14px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; outline: none; transition: border-color 0.2s;" onfocus="this.style.borderColor='#3b82f6'" onblur="this.style.borderColor='#d1d5db'">
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
                                <td>{{ $payment->concessionaire?->business_name ?: $payment->concessionaire?->name }}</td>
                                <td style="font-weight:800;color:#0a5c2f;">₱{{ number_format((float) $payment->amount, 2) }}</td>
                                <td>{{ $payment->payment_date?->format('M d, Y') ?: '—' }}</td>
                                <td>{{ ucfirst(str_replace('_', ' ', $payment->payment_type)) }}</td>
                                <td>{{ $payment->created_at?->setTimezone('Asia/Manila')->format('M d, Y h:i A') ?? '—' }}</td>
                                <td>
                                    <a class="history-receipt-link" href="{{ route('cashier.payments.receipt', $payment->id) }}">📂 Download Receipt</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div id="no_results_message" style="display:none; text-align:center; padding: 20px; color: #6b7280;">No payments match your filters.</div>
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
            const name = row.getAttribute('data-name');
            
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
    }
});
</script>
@endsection
