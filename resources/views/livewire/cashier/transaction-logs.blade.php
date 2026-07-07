<div class="w-full">
    <div class="mb-4 flex flex-wrap items-start justify-between gap-3">
    </div>

    <div class="mb-3 flex flex-wrap items-center justify-end gap-2">
        {{-- Date range picker: border lives on the wrapper so flatpickr can't override it --}}
        <div wire:ignore class="relative flex h-9 w-[240px] shrink-0 items-center overflow-hidden rounded-md border border-slate-300 bg-white shadow-sm focus-within:border-[#1a3c2e] focus-within:ring-2 focus-within:ring-[#1a3c2e]/10">
            <span class="pointer-events-none absolute left-0 flex h-full items-center pl-2.5 text-slate-400">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-3.5 w-3.5" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 2v3m8-3v3M3.75 9.75h16.5M5.25 4.5h13.5A1.5 1.5 0 0 1 20.25 6v13.5a1.5 1.5 0 0 1-1.5 1.5H5.25a1.5 1.5 0 0 1-1.5-1.5V6a1.5 1.5 0 0 1 1.5-1.5Z" />
                </svg>
            </span>
            <input
                x-data
                x-init="flatpickr($el, {
                    mode: 'range',
                    dateFormat: 'Y-m-d',
                    defaultDate: [$wire.startDate, $wire.endDate],
                    disableMobile: true,
                    onClose: function (selectedDates, dateStr, instance) {
                        if (selectedDates.length < 1) { return; }
                        var start = instance.formatDate(selectedDates[0], 'Y-m-d');
                        var end = instance.formatDate(selectedDates[selectedDates.length === 2 ? 1 : 0], 'Y-m-d');
                        $wire.set('startDate', start);
                        $wire.set('endDate', end);
                    }
                })"
                type="text"
                placeholder="YYYY-MM-DD to YYYY-MM-DD"
                class="h-full w-full cursor-pointer border-0 bg-transparent py-0 pl-8 pr-2 text-xs text-slate-700 outline-none ring-0 placeholder:text-slate-400"
            >
        </div>

        <a
            href="{{ route('staff.transaction-logs', ['start_date' => $startDate, 'end_date' => $endDate, 'export' => 'csv']) }}"
            class="inline-flex h-9 shrink-0 items-center whitespace-nowrap rounded-md bg-[#1a3c2e] px-4 text-xs font-semibold text-white transition hover:bg-[#214837]"
        >
            Export to Excel
        </a>
    </div>

    <div class="w-full overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
        @if ($orders->isEmpty())
            <div class="flex flex-col items-center justify-center gap-3 px-6 py-16 text-center">
                <span class="inline-flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-slate-400">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6 w-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                    </svg>
                </span>
                <div>
                    <p class="text-sm font-semibold text-slate-700">No transactions found</p>
                    @if($startDate === $endDate)
                        <p class="mt-1 text-xs text-slate-400">There are no sales records for <span class="font-medium text-slate-500">{{ $startDate }}</span>. Try selecting a different date.</p>
                    @else
                        <p class="mt-1 text-xs text-slate-400">There are no sales records from <span class="font-medium text-slate-500">{{ $startDate }}</span> to <span class="font-medium text-slate-500">{{ $endDate }}</span>. Try a different range.</p>
                    @endif
                </div>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full min-w-full">
                    <thead class="bg-slate-50/90">
                        <tr>
                            <th class="px-4 py-2.5 text-left text-[10px] font-semibold uppercase tracking-[0.08em] text-slate-500">Order ID</th>
                            <th class="px-4 py-2.5 text-left text-[10px] font-semibold uppercase tracking-[0.08em] text-slate-500">Date</th>
                            <th class="px-4 py-2.5 text-left text-[10px] font-semibold uppercase tracking-[0.08em] text-slate-500">Cashier</th>
                            <th class="px-4 py-2.5 text-left text-[10px] font-semibold uppercase tracking-[0.08em] text-slate-500">Payment Method</th>
                            <th class="px-4 py-2.5 text-left text-[10px] font-semibold uppercase tracking-[0.08em] text-slate-500">Total Price</th>
                            <th class="px-4 py-2.5 text-left text-[10px] font-semibold uppercase tracking-[0.08em] text-slate-500">Items</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($orders as $order)
                            <tr class="border-t border-slate-100 align-top">
                                <td class="whitespace-nowrap px-4 py-3 text-xs font-bold text-slate-900">#{{ $order->id }}</td>
                                <td class="whitespace-nowrap px-4 py-3 text-xs text-slate-600">{{ $order->created_at?->format('M d, Y h:i A') ?? '—' }}</td>
                                <td class="whitespace-nowrap px-4 py-3 text-xs text-slate-700">{{ $order->cashier?->name ?? 'N/A' }}</td>
                                <td class="whitespace-nowrap px-4 py-3 text-xs">
                                    <span class="inline-flex items-center rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-semibold capitalize text-emerald-800">
                                        {{ $order->payment_type }}
                                    </span>
                                </td>
                                <td class="whitespace-nowrap px-4 py-3 text-xs font-extrabold text-[#1a3c2e]">₱{{ number_format((float) $order->total_amount, 2) }}</td>
                                <td class="min-w-[250px] px-4 py-3">
                                    <div class="flex flex-wrap gap-1.5">
                                        @forelse ($order->items as $item)
                                            <span class="inline-flex items-center gap-1 rounded border border-slate-200 bg-slate-50 px-1.5 py-0.5 text-[10px] font-medium text-slate-700">
                                                {{ $item->uniformStock?->item_name ?? 'Unknown Item' }}
                                                <span class="rounded bg-slate-200 px-1 py-0 text-[9px] font-bold text-slate-800">x{{ $item->quantity }}</span>
                                                <span class="text-slate-400">@ ₱{{ number_format((float) $item->price_at_sale, 2) }}</span>
                                            </span>
                                        @empty
                                            <span class="text-[11px] text-slate-400">No item details available.</span>
                                        @endforelse
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="border-t border-slate-100 px-4 py-2.5">
                {{-- Faculty portal uses its own styled pagination; cashier keeps the default. --}}
                {{ $orders->links(auth()->user()?->role === 'faculty' ? 'faculty.partials.livewire-pagination' : null) }}
            </div>
        @endif
    </div>
</div>
