<?php

use App\Actions\ProcessSalesTransaction;
use App\Models\UniformStock;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Component;

new class extends Component {
    /** Uniform and book sales are collected in cash only, so the type is fixed. */
    public string $paymentType = 'cash';
    public array $uniformStocks = [];

    public ?string $successMessage = null;
    public ?string $errorMessage = null;

    public function mount(): void
    {
        $this->uniformStocks = $this->loadStocks();
    }

    /**
     * Load the visible stock rows for the checkout UI.
     *
     * Keep the selected columns here only — both the initial mount and the
     * post-sale refresh must read the exact same shape, otherwise the Alpine
     * front-end loses fields (e.g. books' quantity/unit_price) after a sale.
     *
     * @return array<int, array<string, mixed>>
     */
    private function loadStocks(): array
    {
        return UniformStock::query()
            ->where('is_visible', true)
            ->orderBy('item_name')
            ->get(['id', 'item_name', 'item_type', 'prices', 'sizes', 'unit_price', 'quantity'])
            ->toArray();
    }

    /**
     * Returns the new sales order id so the front-end can offer the
     * booklet receipt print, or null when the sale failed.
     */
    public function processCheckout(array $cart): ?int
    {
        $this->successMessage = null;
        $this->errorMessage = null;

        validator(
            ['cart' => $cart, 'paymentType' => $this->paymentType],
            [
                'paymentType' => ['required', 'string', 'max:50'],
                'cart' => ['required', 'array', 'min:1', 'max:10'],
                'cart.max' => 'A sale may contain a maximum of 10 items only.',
                'cart.*.uniform_stock_id' => ['required', 'integer', 'exists:uniform_stocks,id'],
                'cart.*.size' => ['nullable', 'string', Rule::in(['XS', 'S', 'M', 'L', 'XL', '2XL', '3XL', '4XL', '5XL'])],
                'cart.*.quantity' => ['required', 'integer', 'min:1'],
                'cart.*.price_at_sale' => ['required', 'numeric', 'min:0.01'],
            ],
            [
                'cart.required' => 'Add at least one item to the cart before completing the sale.',
                'cart.min' => 'Add at least one item to the cart before completing the sale.',
                'cart.*.uniform_stock_id.required' => 'Please select a stock item for each cart row.',
                'cart.*.uniform_stock_id.integer' => 'Please select a valid stock item for each cart row.',
                'cart.*.uniform_stock_id.exists' => 'One of the selected stock items is invalid. Please reselect and try again.',
                'cart.*.size.required' => 'Please select a size for each selected item.',
                'cart.*.size.in' => 'Please select a valid size for each selected item.',
                'cart.*.quantity.required' => 'Please enter quantity for each selected item.',
                'cart.*.quantity.integer' => 'Quantity must be a whole number.',
                'cart.*.quantity.min' => 'Quantity must be at least 1.',
                'cart.*.price_at_sale.required' => 'Please enter unit price for each selected item.',
                'cart.*.price_at_sale.numeric' => 'Unit price must be a valid number.',
                'cart.*.price_at_sale.min' => 'Unit price must be greater than zero.',
            ],
            [
                'cart.*.uniform_stock_id' => 'stock item',
                'cart.*.size' => 'size',
                'cart.*.price_at_sale' => 'unit price',
            ]
        )->validate();

        try {
            $order = app(ProcessSalesTransaction::class)->handle(
                $cart,
                (int) Auth::id(),
                $this->paymentType
            );

            $this->successMessage = 'Sale completed and stock deducted successfully.';

            $this->uniformStocks = $this->loadStocks();

            return $order->id;
        } catch (\Throwable $e) {
            report($e);
            $this->errorMessage = $e->getMessage();

            return null;
        }
    }
}; ?>

<div>

    @if ($errorMessage)
        <div class="alert alert-error">{{ $errorMessage }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-error">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <div class="card"
         x-data="{
             stockItems: [],
             sizeOrder: ['XS', 'S', 'M', 'L', 'XL', '2XL', '3XL', '4XL', '5XL'],
             cart: [{ uniform_stock_id: '', selected_size: '', quantity: 1, unit_price: 0, max_available_stock: 0 }],
             loading: false,
             showSuccessModal: false,
             lastCompletedTotal: 0,
             lastOrderId: null,
             receiptAddress: '',
             receiptBase: '{{ request()->routeIs('admin.*') ? url('admin/uniform-checkout/receipt') : url('staff/uniform-checkout/receipt') }}',
             maxCartItems: 10,
             init() {
                 this.stockItems = $wire.uniformStocks
                 $wire.$watch('uniformStocks', (value) => { this.stockItems = value })
             },
             normalizeQty(value) {
                 const qty = parseInt(value ?? 0, 10)
                 return Number.isNaN(qty) ? 0 : qty
             },
             normalizePrice(value) {
                 const price = parseFloat(value ?? 0)
                 return Number.isNaN(price) ? 0 : price
             },
             getStock(stockId) {
                 return this.stockItems.find(s => s.id == stockId)
             },
             getSizeOptions(stockId) {
                 const stock = this.getStock(stockId)
                 if (!stock || !stock.sizes) return []

                 return this.sizeOrder
                     .map(size => ({ size, qty: this.normalizeQty(stock.sizes[size]) }))
                     .filter(option => option.qty > 0)
             },
             isBook(stockId) {
                 const stock = this.getStock(stockId)
                 return !!stock && (stock.item_type === 'books' || !stock.sizes)
             },
             addItem() {
                if (this.cart.length >= this.maxCartItems) return;

                 this.cart.push({
                    uniform_stock_id: '',
                    selected_size: '',
                    quantity: 1,
                    unit_price: 0,
                    max_available_stock: 0
                });
            },
             removeItem(index) {
                 if (this.cart.length > 1) this.cart.splice(index, 1)
             },
             onStockChange(index) {
                 const row = this.cart[index]
                 const stock = this.getStock(row.uniform_stock_id)

                 row.selected_size = ''
                 row.quantity = 1

                 if (stock && (stock.item_type === 'books' || !stock.sizes)) {
                     // Books have no size — use the flat quantity and the price set by staff.
                     row.unit_price = this.normalizePrice(stock.unit_price)
                     row.max_available_stock = this.normalizeQty(stock.quantity)
                 } else {
                     row.unit_price = 0
                     row.max_available_stock = 0
                 }
             },
             onSizeChange(index) {
                 const row = this.cart[index]
                 const stock = this.getStock(row.uniform_stock_id)

                 if (!stock || !row.selected_size) {
                     row.max_available_stock = 0
                     row.unit_price = 0
                     row.quantity = 1
                     return
                 }

                 const sizeQty = this.normalizeQty(stock.sizes?.[row.selected_size])
                 row.max_available_stock = sizeQty
                 row.unit_price = this.normalizePrice(stock.prices?.[row.selected_size])

                 if (row.quantity > sizeQty) {
                     row.quantity = sizeQty > 0 ? sizeQty : 1
                 }
             },
             availableStock(item) {
                 if (!item.uniform_stock_id) return '—'
                 if (this.isBook(item.uniform_stock_id)) return item.max_available_stock
                 if (!item.selected_size) return '—'
                 return item.max_available_stock
             },
             subtotal(item) {
                 return item.quantity * item.unit_price
             },
             get orderTotal() {
                 return this.cart.reduce((sum, item) => sum + (item.quantity * item.unit_price), 0)
             },
             get hasOverstock() {
                 return this.cart.some(item => item.uniform_stock_id && item.quantity > item.max_available_stock)
             },
             get canCompleteSale() {
                 if (this.hasOverstock || this.loading) return false
                 if (this.orderTotal <= 0) return false

                 // Every row must be a complete, priced line — no empty rows and
                 // no zero-subtotal (free) items may be recorded as a sale.
                 return this.cart.every(item => {
                     if (!item.uniform_stock_id) return false
                     if (!this.isBook(item.uniform_stock_id) && !item.selected_size) return false
                     return this.normalizeQty(item.quantity) >= 1 && this.normalizePrice(item.unit_price) > 0
                 })
             },
             fmt(val) {
                 const n = parseFloat(val || 0).toFixed(2)
                 const parts = n.split('.')
                 parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',')
                 return '₱' + parts.join('.')
             },
             closeSuccessModal() {
                 this.showSuccessModal = false
             },
             printReceipt() {
                 if (!this.lastOrderId) return
                 const address = this.receiptAddress.trim()
                 const query = address ? '?address=' + encodeURIComponent(address) : ''
                 window.open(this.receiptBase + '/' + this.lastOrderId + query, '_blank')
             },
             async submit() {
                 if (!this.canCompleteSale) return
                 this.loading = true
                 try {
                     const completedTotal = this.orderTotal
                     const cartData = this.cart.map(item => ({
                         uniform_stock_id: parseInt(item.uniform_stock_id),
                         size: this.isBook(item.uniform_stock_id) ? null : item.selected_size,
                         quantity: parseInt(item.quantity),
                         price_at_sale: parseFloat(item.unit_price),
                     }))
                     const orderId = await $wire.processCheckout(cartData)
                     if (!orderId) return

                     this.lastCompletedTotal = completedTotal
                     this.lastOrderId = orderId
                     this.receiptAddress = ''
                     this.cart = [{ uniform_stock_id: '', selected_size: '', quantity: 1, unit_price: 0, max_available_stock: 0 }]
                     this.showSuccessModal = true
                 } catch (e) {
                     this.showSuccessModal = false
                 } finally {
                     this.loading = false
                 }
             }
         }"
         x-on:keydown.escape.window="closeSuccessModal()">
        <div class="card-header co-card-header">
            <div>
                <h3 class="panel-title">Cart Items</h3>
                <p class="panel-sub">Cash sale &middot; maximum of 10 items per transaction.</p>
            </div>
            <div class="co-header-actions">
                <span class="co-cash-badge">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                        <rect x="2" y="6" width="20" height="12" rx="2"/>
                        <circle cx="12" cy="12" r="2.5"/>
                    </svg>
                    Cash payment
                </span>
                <button
                    type="button"
                    x-on:click="addItem()"
                    :disabled="cart.length >= maxCartItems"
                    class="btn btn-outline btn-sm"
                >
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14M5 12h14"/>
                    </svg>
                    <span x-text="cart.length >= maxCartItems ? 'Maximum 10 items' : 'Add Item'"></span>
                </button>
            </div>
        </div>

        <div class="card-body">
            <table class="co-table">
                <thead>
                    <tr>
                        <th>Stock Item</th>
                        <th>Size</th>
                        <th style="text-align:center;">Available</th>
                        <th>Quantity</th>
                        <th>Unit Price (&#8369;)</th>
                        <th>Subtotal</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="(item, index) in cart" :key="index">
                        <tr>
                            <td class="co-col-item">
                                <select
                                    x-model="item.uniform_stock_id"
                                    x-on:change="onStockChange(index)"
                                    class="filter-select co-control"
                                >
                                    <option value="">Select item</option>
                                    <template x-for="stock in stockItems" :key="stock.id">
                                        <option :value="stock.id" x-text="stock.item_name"></option>
                                    </template>
                                </select>
                            </td>
                            <td class="co-col-size">
                                <select
                                    x-show="!isBook(item.uniform_stock_id)"
                                    x-model="item.selected_size"
                                    x-on:change="onSizeChange(index)"
                                    :disabled="!item.uniform_stock_id"
                                    class="filter-select co-control"
                                >
                                    <option value="">Select size</option>
                                    <template x-for="option in getSizeOptions(item.uniform_stock_id)" :key="option.size">
                                        <option :value="option.size" x-text="`${option.size} — ${option.qty} available`"></option>
                                    </template>
                                </select>
                                <span x-show="isBook(item.uniform_stock_id)" class="table-dim" x-cloak>No size</span>
                            </td>
                            <td style="text-align:center;">
                                <span class="table-num" x-text="availableStock(item)"></span>
                            </td>
                            <td class="co-col-qty">
                                <input
                                    type="number"
                                    min="1"
                                    :max="item.max_available_stock || null"
                                    x-model.number="item.quantity"
                                    class="co-control"
                                    :class="item.uniform_stock_id && item.quantity > item.max_available_stock ? 'is-invalid' : ''"
                                >
                            </td>
                            <td class="co-col-price">
                                <input
                                    type="number"
                                    min="0"
                                    step="0.01"
                                    x-model.number="item.unit_price"
                                    class="co-control"
                                >
                            </td>
                            <td><span class="table-num is-pine co-subtotal" x-text="fmt(subtotal(item))"></span></td>
                            <td style="text-align:right;">
                                <button
                                    type="button"
                                    x-show="cart.length > 1"
                                    x-on:click="removeItem(index)"
                                    class="btn btn-red btn-sm"
                                    x-cloak
                                >
                                    Remove
                                </button>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        <div class="co-footer">
            <div>
                <div class="co-total-label">Order Total</div>
                <div class="co-total-sub" x-text="cart.length + (cart.length === 1 ? ' line item' : ' line items')"></div>
            </div>
            <div class="co-footer-right">
                <span class="co-total" x-text="fmt(orderTotal)"></span>
                <button
                    type="button"
                    x-on:click="submit()"
                    :disabled="!canCompleteSale"
                    class="btn btn-green"
                >
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span x-text="loading ? 'Processing…' : 'Complete Sale'"></span>
                </button>
            </div>
        </div>

        <template x-teleport="body" wire:ignore>
            <div
                x-show="showSuccessModal"
                x-transition.opacity.duration.200ms
                x-cloak
                class="co-modal-backdrop"
                role="dialog"
                aria-modal="true"
                aria-labelledby="checkout-success-title"
                x-on:click.self="closeSuccessModal()"
            >
                <div class="co-modal" x-show="showSuccessModal" x-transition.scale.origin.center.duration.200ms>
                    <div class="co-modal-head" id="checkout-success-title">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                        </svg>
                        Sale Completed
                    </div>
                    <div class="co-modal-body">
                        <p>The transaction was finalized and stock has been deducted. The sale is listed under Transaction Logs.</p>
                        <div class="co-modal-total">
                            <span>Recorded total</span>
                            <strong x-text="fmt(lastCompletedTotal)"></strong>
                        </div>
                        <div class="co-modal-receipt">
                            <label for="co-receipt-address">Address on receipt (optional)</label>
                            <input
                                id="co-receipt-address"
                                type="text"
                                maxlength="45"
                                x-model="receiptAddress"
                                class="co-control"
                                placeholder="e.g. Bacoor, Cavite"
                            >
                        </div>
                    </div>
                    <div class="co-modal-actions">
                        <button type="button" class="btn btn-outline" x-on:click="printReceipt()">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="width:14px;height:14px;">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 9V4h12v5M6 18H4a2 2 0 01-2-2v-5a2 2 0 012-2h16a2 2 0 012 2v5a2 2 0 01-2 2h-2M6 14h12v6H6z"/>
                            </svg>
                            Print Receipt
                        </button>
                        <button type="button" class="btn btn-green" x-on:click="closeSuccessModal()">Close &amp; Continue</button>
                    </div>
                </div>
            </div>
        </template>
    </div>
</div>
