<?php

use App\Actions\ProcessSalesTransaction;
use App\Models\UniformStock;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('cashier.layout')] #[Title('Uniform Checkout')] class extends Component {
    public string $paymentType = 'cash';
    public array $uniformStocks = [];

    public ?string $successMessage = null;
    public ?string $errorMessage = null;

    public function mount(): void
    {
        $this->uniformStocks = $this->loadStocks();
    }

    /**
     * Load the visible stock rows for the cashier UI.
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

    public function processCheckout(array $cart): bool
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
                'cart.*.price_at_sale' => ['required', 'numeric', 'min:0'],
            ],
            [
                'paymentType.required' => 'Please choose a payment type.',
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
                'cart.*.price_at_sale.min' => 'Unit price cannot be negative.',
            ],
            [
                'paymentType' => 'payment type',
                'cart.*.uniform_stock_id' => 'stock item',
                'cart.*.size' => 'size',
                'cart.*.price_at_sale' => 'unit price',
            ]
        )->validate();

        try {
            app(ProcessSalesTransaction::class)->handle(
                $cart,
                (int) Auth::id(),
                $this->paymentType
            );

            $this->successMessage = 'Sale completed and stock deducted successfully.';

            $this->uniformStocks = $this->loadStocks();

            return true;
        } catch (\Throwable $e) {
            report($e);
            $this->errorMessage = $e->getMessage();

            return false;
        }
    }
}; ?>

<div class="w-full overflow-x-hidden">

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

    <div class="mb-4 w-full overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm" >
        <div class="border-b border-slate-100 bg-slate-50/60 px-6 py-4">
            <h2 class="text-base font-bold text-slate-800">Payment Details</h2>
        </div>
        <div class="px-6 pt-5 pb-7">

            <div class="w-full max-w-sm">
                <select id="paymentType" wire:model="paymentType" class="h-11 w-full rounded-md border border-slate-300 bg-white px-3 text-sm text-slate-700 shadow-sm focus:border-[#1a3c2e] focus:outline-none focus:ring-2 focus:ring-[#1a3c2e]/10">
                    <option value="cash">Cash</option>
                    <option value="gcash">GCash</option>
                    <option value="card">Card</option>
                </select>
                <br>
                <br>
                
            </div>
            @error('paymentType')
                <span class="mt-1.5 block text-sm text-red-600">{{ $message }}</span>
            @enderror
        </div>
    </div>

    <div class="w-full overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm"
         x-data="{
             stockItems: [],
             sizeOrder: ['XS', 'S', 'M', 'L', 'XL', '2XL', '3XL', '4XL', '5XL'],
             cart: [{ uniform_stock_id: '', selected_size: '', quantity: 1, unit_price: 0, max_available_stock: 0 }],
             loading: false,
             showSuccessModal: false,
             lastCompletedTotal: 0,
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
                if (this.cart.length >= this.maxCartItems) {
                    alert('Maximum of 10 items per sale only.');
                    return;
                }

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
                     // Books have no size — use the flat quantity and the price set by faculty.
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
             fmt(val) {
                 const n = parseFloat(val || 0).toFixed(2)
                 const parts = n.split('.')
                 parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',')
                 return '₱' + parts.join('.')
             },
             closeSuccessModal() {
                 this.showSuccessModal = false
             },
             async submit() {
                 if (this.hasOverstock || this.loading) return
                 this.loading = true
                 try {
                     const completedTotal = this.orderTotal
                     const cartData = this.cart.map(item => ({
                         uniform_stock_id: parseInt(item.uniform_stock_id),
                         size: this.isBook(item.uniform_stock_id) ? null : item.selected_size,
                         quantity: parseInt(item.quantity),
                         price_at_sale: parseFloat(item.unit_price),
                     }))
                     const isSuccessful = await $wire.processCheckout(cartData)
                     if (!isSuccessful) return

                     this.lastCompletedTotal = completedTotal
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
        <div class="flex items-center justify-between border-b border-slate-100 bg-slate-50/60 px-6 py-4">
            <h2 class="text-base font-bold text-slate-800">Cart Items</h2>
        <button
            type="button"
             x-on:click="addItem()"
            :disabled="cart.length >= maxCartItems"
            :class="cart.length >= maxCartItems ? 'opacity-50 cursor-not-allowed' : ''"
            class="inline-flex items-center rounded-md border border-slate-300 bg-white px-3 py-1.5 text-xs font-semibold text-slate-600 transition hover:bg-slate-50"
        >
        <span x-text="cart.length >= maxCartItems ? 'Maximum 10 Items' : '+ Add Item'"></span>
        </button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-full">
                <thead class="bg-slate-50/90">
                    <tr>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold uppercase tracking-[0.08em] text-slate-500">Stock Item</th>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold uppercase tracking-[0.08em] text-slate-500">Size</th>
                        <th class="px-5 py-3.5 text-center text-xs font-semibold uppercase tracking-[0.08em] text-slate-500">Available</th>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold uppercase tracking-[0.08em] text-slate-500">Quantity</th>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold uppercase tracking-[0.08em] text-slate-500">Unit Price (₱)</th>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold uppercase tracking-[0.08em] text-slate-500">Subtotal</th>
                        <th class="px-5 py-3.5"></th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="(item, index) in cart" :key="index">
                        <tr class="border-t border-slate-100 align-top">
                            <td class="min-w-[200px] px-5 py-4">
                                <select
                                    x-model="item.uniform_stock_id"
                                    x-on:change="onStockChange(index)"
                                    class="h-10 w-full rounded-md border border-slate-300 bg-white px-3 text-sm text-slate-700 focus:border-[#1a3c2e] focus:outline-none focus:ring-2 focus:ring-[#1a3c2e]/10"
                                >
                                    <option value="">Select item</option>
                                    <template x-for="stock in stockItems" :key="stock.id">
                                        <option :value="stock.id" x-text="stock.item_name"></option>
                                    </template>
                                </select>
                            </td>
                            <td class="min-w-[210px] px-5 py-4">
                                <select
                                    x-show="!isBook(item.uniform_stock_id)"
                                    x-model="item.selected_size"
                                    x-on:change="onSizeChange(index)"
                                    :disabled="!item.uniform_stock_id"
                                    class="h-10 w-full rounded-md border border-slate-300 bg-white px-3 text-sm text-slate-700 focus:border-[#1a3c2e] focus:outline-none focus:ring-2 focus:ring-[#1a3c2e]/10 disabled:cursor-not-allowed disabled:bg-slate-100"
                                >
                                    <option value="">Select size</option>
                                    <template x-for="option in getSizeOptions(item.uniform_stock_id)" :key="option.size">
                                        <option :value="option.size" x-text="`${option.size} — ${option.qty} available`"></option>
                                    </template>
                                </select>
                                <span x-show="isBook(item.uniform_stock_id)" class="text-sm font-medium text-slate-400">No size</span>
                            </td>
                            <td class="px-5 py-4 text-center text-sm font-semibold text-slate-600" x-text="availableStock(item)"></td>
                            <td class="min-w-[160px] px-5 py-4">
                                <input
                                    type="number"
                                    min="1"
                                    :max="item.max_available_stock || null"
                                    x-model.number="item.quantity"
                                    :class="item.uniform_stock_id && item.quantity > item.max_available_stock
                                        ? 'h-10 w-full rounded-md border-2 border-red-400 px-3 text-sm text-slate-700 focus:border-red-500 focus:outline-none focus:ring-2 focus:ring-red-100'
                                        : 'h-10 w-full rounded-md border border-slate-300 px-3 text-sm text-slate-700 focus:border-[#1a3c2e] focus:outline-none focus:ring-2 focus:ring-[#1a3c2e]/10'"
                                >
                            </td>
                            <td class="min-w-[190px] px-5 py-4">
                                <input
                                    type="number"
                                    min="0"
                                    step="0.01"
                                    x-model.number="item.unit_price"
                                    class="h-10 w-full rounded-md border border-slate-300 px-3 text-sm text-slate-700 focus:border-[#1a3c2e] focus:outline-none focus:ring-2 focus:ring-[#1a3c2e]/10"
                                >
                            </td>
                            <td class="whitespace-nowrap px-5 py-4 text-base font-extrabold text-[#1a3c2e]" x-text="fmt(subtotal(item))"></td>
                            <td class="px-5 py-4 text-right">
                                <button
                                    type="button"
                                    x-show="cart.length > 1"
                                    x-on:click="removeItem(index)"
                                    class="inline-flex items-center rounded-md border border-red-200 bg-white px-2.5 py-1.5 text-xs font-semibold text-red-500 transition hover:bg-red-50"
                                >
                                    Remove
                                </button>
                            </td>
                        </tr>
                    </template>
                </tbody>
                <tfoot>
                    <tr class="bg-slate-50/80">
                        <td colspan="5" class="px-5 py-4 text-right text-sm font-semibold text-slate-600">Order Total</td>
                        <td class="whitespace-nowrap px-5 py-4 text-2xl font-extrabold text-[#1a3c2e]" x-text="fmt(orderTotal)"></td>
                        <td class="px-5 py-4"></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="flex justify-end border-t border-slate-100 px-6 py-4">
            <button
                type="button"
                x-on:click="submit()"
                :disabled="hasOverstock || loading"
                :class="(hasOverstock || loading) ? 'opacity-60 cursor-not-allowed' : ''"
                class="inline-flex items-center rounded-md bg-[#1a3c2e] px-6 py-3 text-sm font-semibold text-white transition hover:bg-[#214837]"
            >
                <span x-text="loading ? 'Processing…' : 'Complete Sale'"></span>
            </button>
        </div>

        <template x-teleport="body" wire:ignore>
        <div
            x-show="showSuccessModal"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 p-4 backdrop-blur-sm"
            role="dialog"
            aria-modal="true"
            aria-labelledby="checkout-success-title"
            x-on:click.self="closeSuccessModal()"
        >
            <div
                x-show="showSuccessModal"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="w-full max-w-md transform overflow-hidden rounded-2xl bg-white p-8 text-left align-middle shadow-2xl transition-all"
            >
                <div class="mx-auto mb-6 flex h-16 w-16 items-center justify-center rounded-full bg-green-100">
                    <svg class="h-10 w-10 text-green-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                </div>

                <div class="text-center">
                    <h3 id="checkout-success-title" class="text-xl font-bold text-slate-900">Sale Completed!</h3>
                    <p class="mt-2 text-base text-slate-500">The transaction was finalized and stock has been successfully deducted.</p>
                </div>

                <div class="mt-6 rounded-xl border border-slate-100 bg-slate-50 p-5">
                    <div class="flex items-center justify-between">
                        <span class="text-base font-medium text-slate-500">Recorded Total</span>
                        <span class="text-2xl font-extrabold text-[#1a3c2e]" x-text="fmt(lastCompletedTotal)"></span>
                    </div>
                </div>

                <div class="mt-6">
                    <button
                        type="button"
                        x-on:click="closeSuccessModal()"
                        class="inline-flex w-full justify-center rounded-lg bg-[#1a3c2e] px-4 py-3 text-base font-semibold text-white shadow-sm transition hover:bg-[#214837] focus:outline-none focus:ring-2 focus:ring-[#1a3c2e] focus:ring-offset-2"
                    >
                        Close & Continue
                    </button>
                </div>
            </div>
        </div>
        </template>
    </div>
</div>