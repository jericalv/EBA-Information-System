@extends('admin.layout')

@section('title', 'Transaction Logs')

@section('extra-css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
        .tx-toolbar {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }
        .tx-title {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }
        .tx-subtitle {
            font-size: 13px;
            color: #64748b;
            font-weight: 500;
        }
        .tx-controls {
            margin-left: auto;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .tx-date-wrap {
            position: relative;
            width: 250px;
            flex-shrink: 0;
        }
        .tx-date-wrap svg {
            position: absolute;
            top: 50%;
            left: 10px;
            width: 16px;
            height: 16px;
            color: #64748b;
            transform: translateY(-50%);
            pointer-events: none;
        }
        #date-picker {
            width: 100%;
            height: 38px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 0 12px 0 34px;
            font-size: 14px;
            color: #1e293b;
            background: #f8fafc;
            outline: none;
            font-family: inherit;
        }
        #date-picker:focus {
            border-color: #0a5c2f;
            box-shadow: 0 0 0 3px rgba(10, 92, 47, 0.1);
            background: #fff;
        }
        .tx-export-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            height: 38px;
            padding: 0 16px;
            border-radius: 8px;
            background: var(--green);
            color: #fff;
            font-size: 14px;
            font-weight: 600;
            white-space: nowrap;
            text-decoration: none;
            transition: background 0.15s ease;
        }
        .tx-export-btn:hover { background: var(--green-light); }
        .tx-export-btn svg {
            width: 16px;
            height: 16px;
        }
        .tx-order-id {
            font-weight: 700;
            color: #0f172a;
        }
        .tx-total {
            font-weight: 700;
            color: #0a5c2f;
        }
        .tx-items {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
        }
        .tx-item-pill {
            display: inline-flex;
            align-items: center;
            border-radius: 999px;
            padding: 4px 10px;
            background: #f1f5f9;
            color: #334155;
            font-size: 12px;
            font-weight: 600;
        }
        .tx-muted {
            color: #94a3b8;
            font-size: 13px;
        }
        .tx-empty {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 48px 24px;
            text-align: center;
            color: #64748b;
        }
        .tx-empty svg {
            width: 42px;
            height: 42px;
            color: #cbd5e1;
        }
        .tx-empty-title {
            font-size: 15px;
            font-weight: 700;
            color: #475569;
        }
        @media (max-width: 900px) {
            .tx-controls {
                margin-left: 0;
                width: 100%;
                justify-content: flex-start;
            }
        }
    </style>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <div class="tx-title">
                <h3>Transaction Logs</h3>
                <p class="tx-subtitle">Sales transactions recorded by staff</p>
            </div>

            <div class="tx-controls">
                <div class="tx-date-wrap">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                        <rect x="3" y="4" width="18" height="18" rx="2"></rect>
                        <line x1="16" y1="2" x2="16" y2="6"></line>
                        <line x1="8" y1="2" x2="8" y2="6"></line>
                        <line x1="3" y1="10" x2="21" y2="10"></line>
                    </svg>
                    <input
                        id="date-picker"
                        type="text"
                        value="{{ $startDate }} to {{ $endDate }}"
                        aria-label="Select transaction log date range"
                    >
                </div>

                <a
                    id="export-btn"
                    href="{{ route('admin.transaction-logs', ['start_date' => $startDate, 'end_date' => $endDate, 'export' => 'csv']) }}"
                    class="tx-export-btn"
                >
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M12 3v12"></path>
                        <path d="m7 10 5 5 5-5"></path>
                        <path d="M5 21h14"></path>
                    </svg>
                    Export to Excel
                </a>
            </div>
        </div>

        <div class="card-body">
            @if ($orders->isEmpty())
                <div class="tx-empty">
                    <svg fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                        <rect x="3" y="4" width="18" height="16" rx="2"></rect>
                        <path d="M8 8h8"></path>
                        <path d="M8 12h8"></path>
                        <path d="M8 16h5"></path>
                    </svg>
                    <div class="tx-empty-title">
                        @if ($startDate === $endDate)
                            No transactions found for {{ \Illuminate\Support\Carbon::parse($startDate)->format('M d, Y') }}
                        @else
                            No transactions found from {{ \Illuminate\Support\Carbon::parse($startDate)->format('M d, Y') }} to {{ \Illuminate\Support\Carbon::parse($endDate)->format('M d, Y') }}
                        @endif
                    </div>
                </div>
            @else
                <table>
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Date</th>
                            <th>Cashier</th>
                            <th>Payment Method</th>
                            <th>Total Price</th>
                            <th>Items</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($orders as $order)
                            <tr>
                                <td class="tx-order-id">#{{ $order->id }}</td>
                                <td>{{ $order->created_at->format('M d, Y h:i A') }}</td>
                                <td>{{ $order->cashier?->name ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge badge-success" style="text-transform: capitalize;">
                                        {{ $order->payment_type }}
                                    </span>
                                </td>
                                <td class="tx-total">₱{{ number_format((float) $order->total_amount, 2) }}</td>
                                <td>
                                    @if ($order->items->isNotEmpty())
                                        <div class="tx-items">
                                            @foreach ($order->items as $item)
                                                <span class="tx-item-pill">
                                                    {{ $item->uniformStock?->item_name ?? 'Unknown Item' }} x{{ (int) $item->quantity }} @ ₱{{ number_format((float) $item->price_at_sale, 2) }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="tx-muted">No item details available</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        @if ($orders->hasPages())
            <div class="pagination-wrap">
                {{ $orders->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const dateInput = document.getElementById('date-picker');
            const exportBtn = document.getElementById('export-btn');
            const baseUrl = "{{ route('admin.transaction-logs') }}";

            if (!dateInput || typeof flatpickr === 'undefined') {
                return;
            }

            flatpickr(dateInput, {
                mode: 'range',
                dateFormat: 'Y-m-d',
                defaultDate: ['{{ $startDate }}', '{{ $endDate }}'],
                disableMobile: true,
                onClose: function (selectedDates, dateStr, instance) {
                    if (selectedDates.length < 1) {
                        return;
                    }

                    var start = instance.formatDate(selectedDates[0], 'Y-m-d');
                    var end = instance.formatDate(selectedDates[selectedDates.length === 2 ? 1 : 0], 'Y-m-d');
                    var qs = 'start_date=' + encodeURIComponent(start) + '&end_date=' + encodeURIComponent(end);

                    if (exportBtn) {
                        exportBtn.href = baseUrl + '?' + qs + '&export=csv';
                    }

                    window.location.href = baseUrl + '?' + qs;
                }
            });
        });
    </script>
@endsection