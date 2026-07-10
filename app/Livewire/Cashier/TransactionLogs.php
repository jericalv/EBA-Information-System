<?php

namespace App\Livewire\Cashier;

use App\Models\SalesOrder;
use Livewire\Component;
use Livewire\WithPagination;

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

        // Summary of the whole selected range (all pages), shown as statcards.
        $totalSales = (float) SalesOrder::query()
            ->whereDate('created_at', '>=', $start)
            ->whereDate('created_at', '<=', $end)
            ->sum('total_amount');

        return view('livewire.cashier.transaction-logs', [
            'orders' => $orders,
            'totalTransactions' => $orders->total(),
            'totalSales' => $totalSales,
        ]);
    }
}
