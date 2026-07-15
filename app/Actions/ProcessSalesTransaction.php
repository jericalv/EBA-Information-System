<?php

namespace App\Actions;

use App\Models\ActivityLog;
use App\Models\SalesOrder;
use App\Models\StockMovement;
use App\Models\UniformStock;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class ProcessSalesTransaction
{
    /**
     * @var array<int, string>
     */
    private const ALLOWED_SIZES = ['XS', 'S', 'M', 'L', 'XL', '2XL', '3XL', '4XL', '5XL'];

    /**
     * @param array<int, array<string, mixed>> $cartItems
     */
    public function handle(array $cartItems, int $cashierId, string $paymentType = 'cash'): SalesOrder
    {
        if (empty($cartItems)) {
            throw new RuntimeException('Cart is empty.');
        }

        return DB::transaction(function () use ($cartItems, $cashierId, $paymentType): SalesOrder {
            $salesOrder = SalesOrder::create([
                'cashier_id' => $cashierId,
                'total_amount' => 0,
                'payment_type' => $paymentType,
            ]);

            $totalAmount = 0.0;

            foreach ($cartItems as $item) {
                $uniformStockId = (int) ($item['uniform_stock_id'] ?? 0);
                $selectedSize = strtoupper(trim((string) ($item['size'] ?? '')));
                $requestedQty = (int) ($item['quantity'] ?? 0);
                $priceAtSale = (float) ($item['price_at_sale'] ?? $item['price'] ?? 0);

                if ($uniformStockId <= 0 || $requestedQty <= 0) {
                    throw new RuntimeException('Cart item is invalid.');
                }

                $stock = UniformStock::query()
                    ->lockForUpdate()
                    ->find($uniformStockId);

                if (! $stock) {
                    throw new RuntimeException("Stock item #{$uniformStockId} does not exist.");
                }

                $sizes = is_array($stock->sizes) ? $stock->sizes : [];
                $isBook = $stock->item_type === 'books' || $sizes === [];

                if ($isBook) {
                    // Books have no size — deduct from the flat quantity.
                    $available = (int) $stock->quantity;

                    if ($requestedQty > $available) {
                        throw new RuntimeException("Insufficient stock for {$stock->item_name}.");
                    }

                    $stock->quantity = $available - $requestedQty;
                    $stock->save();
                } else {
                    if (! in_array($selectedSize, self::ALLOWED_SIZES, true)) {
                        throw new RuntimeException('Cart item size is invalid.');
                    }

                    $availableInSize = (int) ($sizes[$selectedSize] ?? 0);

                    if ($requestedQty > $availableInSize) {
                        throw new RuntimeException("Insufficient stock for {$stock->item_name} ({$selectedSize}).");
                    }

                    $sizes[$selectedSize] = $availableInSize - $requestedQty;
                    $stock->sizes = $sizes;
                    $stock->quantity = array_sum(array_map(static fn ($qty): int => (int) $qty, $sizes));
                    $stock->save();
                }

                StockMovement::create([
                    'uniform_stock_id' => $stock->id,
                    'user_id' => $cashierId,
                    'type' => 'sale',
                    'size' => $isBook ? null : $selectedSize,
                    'quantity_change' => -$requestedQty,
                    'quantity_after' => $isBook
                        ? (int) $stock->quantity
                        : (int) ($stock->sizes[$selectedSize] ?? 0),
                    'note' => 'Sales order #' . $salesOrder->id,
                ]);

                $salesOrder->items()->create([
                    'uniform_stock_id' => $stock->id,
                    'quantity' => $requestedQty,
                    'price_at_sale' => $priceAtSale,
                ]);

                $totalAmount += $requestedQty * $priceAtSale;
            }

            $salesOrder->update([
                'total_amount' => $totalAmount,
            ]);

            ActivityLog::log(
                'stock_deducted_via_sale',
                'sales_order',
                (string) $salesOrder->id,
                'Stock deducted via cashier sale.',
                [
                    // TODO: Replace this placeholder detail payload as needed.
                    'cashier_id' => $cashierId,
                    'payment_type' => $paymentType,
                    'item_count' => count($cartItems),
                    'total_amount' => number_format($totalAmount, 2, '.', ''),
                ]
            );

            return $salesOrder->load('items.uniformStock', 'cashier');
        });
    }
}
