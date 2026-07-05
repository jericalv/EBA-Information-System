<?php

namespace App\Livewire\Cashier;

use App\Models\SalesOrder;
use Livewire\Component;
use Livewire\WithPagination;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TransactionLogs extends Component
{
    use WithPagination;

    public int $perPage = 10;
    public string $startDate = '';
    public string $endDate = '';

    public function mount(): void
    {
        $this->startDate = now()->toDateString();
        $this->endDate = now()->toDateString();
    }

    public function updatedStartDate(): void
    {
        $this->resetPage();
    }

    public function updatedEndDate(): void
    {
        $this->resetPage();
    }

    /**
     * Return the selected range as [start, end], ordered so a reversed
     * selection still returns results.
     *
     * @return array{0: string, 1: string}
     */
    private function normalizedRange(): array
    {
        $start = $this->startDate ?: now()->toDateString();
        $end = $this->endDate ?: $start;

        if ($start > $end) {
            [$start, $end] = [$end, $start];
        }

        return [$start, $end];
    }

    public function render()
    {
        [$start, $end] = $this->normalizedRange();

        $orders = SalesOrder::query()
            ->with(['items.uniformStock', 'cashier'])
            ->whereDate('created_at', '>=', $start)
            ->whereDate('created_at', '<=', $end)
            ->latest()
            ->paginate($this->perPage);

        return view('livewire.cashier.transaction-logs', [
            'orders' => $orders,
        ]);
    }

    public function exportToExcel(): StreamedResponse
    {
        [$start, $end] = $this->normalizedRange();

        $filename = 'transaction_logs_' . $start . '_to_' . $end . '.csv';

        return response()->streamDownload(function () use ($start, $end): void {
            $output = fopen('php://output', 'w');

            if (! $output) {
                return;
            }

            fputcsv($output, [
                'Quantity',
                'Cashier Name',
                'Payment Method',
                'Total Price',
                'Date',
            ]);

            SalesOrder::query()
                ->with(['cashier', 'items'])
                ->whereDate('created_at', '>=', $start)
                ->whereDate('created_at', '<=', $end)
                ->latest('id')
                ->chunkById(200, function ($orders) use ($output): void {
                    foreach ($orders as $order) {
                        fputcsv($output, [
                            $order->items->sum('quantity'),
                            $order->cashier?->name ?? 'N/A',
                            $order->payment_type,
                            number_format((float) $order->total_amount, 2, '.', ''),
                            optional($order->created_at)->format('M d, Y g:i A'),
                        ]);
                    }
                });

            fclose($output);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Cache-Control' => 'no-store, no-cache',
        ]);
    }
}
