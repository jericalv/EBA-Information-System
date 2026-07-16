<?php

namespace App\Http\Controllers;

use App\Models\SalesOrder;
use App\Models\SalesOrderItem;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class UniformReceiptController extends Controller
{
    /**
     * The booklet leaf is 90mm x 130mm; 1mm = 2.8346pt.
     */
    private const PAPER = [0, 0, 255.12, 368.50];

    /**
     * Overlay receipt for the pre-printed sales booklet. Streams a PDF at
     * the exact leaf size; ?preview=1 returns the calibration HTML view
     * instead. ?dx= / ?dy= nudge the whole layout in millimetres.
     */
    public function show(Request $request, SalesOrder $order)
    {
        $order->loadMissing('items.uniformStock');

        $layout = config('booklet-receipt');
        $layout['offset_x'] += (float) $request->query('dx', '0');
        $layout['offset_y'] += (float) $request->query('dy', '0');

        $lines = $order->items
            ->take((int) $layout['rows']['max'])
            ->map(function (SalesOrderItem $item): array {
                $qty = (int) $item->quantity;
                $name = $item->uniformStock?->item_name ?? 'Item';

                return [
                    'qty' => (string) $qty,
                    'unit' => $qty === 1 ? 'pc' : 'pcs',
                    'articles' => Str::limit($name . ($item->size ? " ({$item->size})" : ''), 26, '…'),
                    'price' => number_format((float) $item->price_at_sale, 2),
                    'amount' => number_format($qty * (float) $item->price_at_sale, 2),
                ];
            })
            ->values()
            ->all();

        $receipt = [
            'date' => ($order->created_at ?? now())->format('m/d/Y'),
            'sold_to' => Str::limit(trim((string) $request->query('sold_to', '')), 40, ''),
            'address' => Str::limit(trim((string) $request->query('address', '')), 45, ''),
            'lines' => $lines,
            'total' => number_format((float) $order->total_amount, 2),
        ];

        $data = [
            'order' => $order,
            'layout' => $layout,
            'receipt' => $receipt,
            'preview' => false,
        ];

        if ($request->boolean('preview')) {
            return view('pdf.uniform-booklet-receipt', ['preview' => true] + $data);
        }

        return Pdf::loadView('pdf.uniform-booklet-receipt', $data)
            ->setPaper(self::PAPER)
            ->stream(sprintf('booklet-receipt-%d.pdf', $order->id));
    }
}
