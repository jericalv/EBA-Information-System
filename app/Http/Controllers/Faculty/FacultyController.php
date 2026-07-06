<?php

namespace App\Http\Controllers\Faculty;

use App\Actions\ProcessSalesTransaction;
use App\Http\Controllers\Controller;
use App\Mail\PartnershipApprovedMail;
use App\Mail\PartnershipStepApprovedMail;
use App\Models\ActivityLog;
use App\Models\ConcessionairePayment;
use App\Models\PartnershipApplication;
use App\Models\SalesOrder;
use App\Models\SalesOrderItem;
use App\Models\UniformStock;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class FacultyController extends Controller
{
    protected function getUnreadPayments(): Collection
    {
        $user = Auth::user();

        if (! $user || $user->role !== 'faculty') {
            return new Collection();
        }

        return ConcessionairePayment::query()
            ->with(['concessionaire:id,name,business_name'])
            ->when($user->payment_notifications_read_at, function ($query) use ($user) {
                $query->where('created_at', '>', $user->payment_notifications_read_at);
            })
            ->latest('created_at')
            ->take(10)
            ->get();
    }

    protected function facultyView(string $view, array $data = [])
    {
        $unreadPayments = $this->getUnreadPayments();

        return view($view, array_merge($data, [
            'unreadPayments' => $unreadPayments,
            'unreadCount' => $unreadPayments->count(),
        ]));
    }

    public function dashboard()
    {
        $authId = Auth::id();
        $now = now();
        $startMonth = $now->copy()->startOfMonth()->subMonths(5);

        $totalApplications = PartnershipApplication::count();
        $pendingCount = PartnershipApplication::where('status', 'pending')->count();
        $approvedCount = PartnershipApplication::where('status', 'approved')->count();
        $reviewedByMe = PartnershipApplication::where('reviewed_by', $authId)->count();

        $totalConcessionaires = User::query()
            ->where('role', 'concessionaire')
            ->where('is_approved', true)
            ->count();

        $monthlyApplicationsMap = PartnershipApplication::query()
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month_key, COUNT(*) as total")
            ->whereDate('created_at', '>=', $startMonth)
            ->groupBy('month_key')
            ->orderBy('month_key')
            ->pluck('total', 'month_key');

        $monthlyChartRows = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = $now->copy()->startOfMonth()->subMonths($i);
            $monthKey = $month->format('Y-m');

            $monthlyChartRows[] = [
                'label' => $month->format('M Y'),
                'applications' => (int) ($monthlyApplicationsMap[$monthKey] ?? 0),
            ];
        }

        $appMonthLabels = array_column($monthlyChartRows, 'label');
        $appMonthData = array_column($monthlyChartRows, 'applications');

        $statusCounts = PartnershipApplication::query()
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $statusLabels = ['pending', 'under_review', 'approved', 'rejected', 'registered'];
        $statusData = collect($statusLabels)
            ->map(fn ($status) => (int) $statusCounts->get($status, 0))
            ->values()
            ->toArray();
        $statusLabelsFormatted = ['Pending', 'Under Review', 'Approved', 'Rejected', 'Registered'];

        // ---------- Sales analytics (last 6 months) ----------
        $monthlyRevenueMap = SalesOrder::query()
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month_key, SUM(total_amount) as total")
            ->whereDate('created_at', '>=', $startMonth)
            ->groupBy('month_key')
            ->pluck('total', 'month_key');

        // Units sold per month, split by item type (books vs uniforms).
        $monthlyUnitsRows = SalesOrderItem::query()
            ->join('sales_orders', 'sales_order_items.sales_order_id', '=', 'sales_orders.id')
            ->join('uniform_stocks', 'sales_order_items.uniform_stock_id', '=', 'uniform_stocks.id')
            ->whereDate('sales_orders.created_at', '>=', $startMonth)
            ->selectRaw("DATE_FORMAT(sales_orders.created_at, '%Y-%m') as month_key, uniform_stocks.item_type as item_type, SUM(sales_order_items.quantity) as units")
            ->groupBy('month_key', 'item_type')
            ->get();

        // Index units by [month_key][bucket] where bucket is 'books' or 'uniforms'.
        $unitsByMonth = [];
        foreach ($monthlyUnitsRows as $row) {
            $bucket = $row->item_type === 'books' ? 'books' : 'uniforms';
            $unitsByMonth[$row->month_key][$bucket] = ($unitsByMonth[$row->month_key][$bucket] ?? 0) + (int) $row->units;
        }

        $salesMonthLabels = [];
        $revenueData = [];
        $unitsUniformsData = [];
        $unitsBooksData = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = $now->copy()->startOfMonth()->subMonths($i);
            $monthKey = $month->format('Y-m');

            $salesMonthLabels[] = $month->format('M Y');
            $revenueData[] = round((float) ($monthlyRevenueMap[$monthKey] ?? 0), 2);
            $unitsUniformsData[] = (int) ($unitsByMonth[$monthKey]['uniforms'] ?? 0);
            $unitsBooksData[] = (int) ($unitsByMonth[$monthKey]['books'] ?? 0);
        }

        // Top items by total quantity sold (all time).
        $topItemsRows = SalesOrderItem::query()
            ->join('uniform_stocks', 'sales_order_items.uniform_stock_id', '=', 'uniform_stocks.id')
            ->selectRaw('uniform_stocks.item_name as item_name, SUM(sales_order_items.quantity) as units')
            ->groupBy('uniform_stocks.id', 'uniform_stocks.item_name')
            ->orderByDesc('units')
            ->limit(8)
            ->get();

        $topItemLabels = $topItemsRows->pluck('item_name')->map(fn ($name) => (string) $name)->toArray();
        $topItemData = $topItemsRows->pluck('units')->map(fn ($units) => (int) $units)->toArray();

        return $this->facultyView('faculty.dashboard', compact(
            'totalApplications',
            'pendingCount',
            'approvedCount',
            'reviewedByMe',
            'totalConcessionaires',
            'appMonthLabels',
            'appMonthData',
            'statusData',
            'statusLabelsFormatted',
            'salesMonthLabels',
            'revenueData',
            'unitsUniformsData',
            'unitsBooksData',
            'topItemLabels',
            'topItemData'
        ));
    }

    public function transactionLogsIndex(Request $request)
    {
        $validated = $request->validate([
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date'],
            'export' => ['nullable', Rule::in(['csv'])],
        ]);

        $today = now()->toDateString();
        $startDate = $validated['start_date'] ?? $today;
        $endDate = $validated['end_date'] ?? $today;

        // Keep the range ordered so a reversed selection still returns results.
        if ($startDate > $endDate) {
            [$startDate, $endDate] = [$endDate, $startDate];
        }

        if (($validated['export'] ?? null) === 'csv') {
            $filename = 'transaction_logs_' . $startDate . '_to_' . $endDate . '.csv';

            return response()->streamDownload(function () use ($startDate, $endDate): void {
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
                    ->whereDate('created_at', '>=', $startDate)
                    ->whereDate('created_at', '<=', $endDate)
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

        $orders = SalesOrder::query()
            ->with(['items.uniformStock', 'cashier'])
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return $this->facultyView('faculty.transaction_logs', compact('orders', 'startDate', 'endDate'));
    }

    public function uniformCheckoutIndex()
    {
        $uniformStocks = UniformStock::query()
            ->where('is_visible', true)
            ->orderBy('item_name')
            ->get(['id', 'item_name', 'quantity']);

        return $this->facultyView('faculty.uniform_checkout', compact('uniformStocks'));
    }

    public function storeUniformCheckout(Request $request)
    {
        $validated = $request->validate([
            'payment_type' => ['required', Rule::in(['cash', 'gcash', 'card'])],
            'cart' => ['required', 'string'],
        ]);

        $cart = json_decode($validated['cart'], true);

        if (! is_array($cart)) {
            return back()
                ->withErrors(['cart' => 'Cart payload is invalid.'])
                ->withInput();
        }

        validator(['cart' => $cart], [
            'cart' => ['required', 'array', 'min:1'],
            'cart.*.uniform_stock_id' => ['required', 'integer', 'exists:uniform_stocks,id'],
            'cart.*.quantity' => ['required', 'integer', 'min:1'],
            'cart.*.price_at_sale' => ['required', 'numeric', 'min:0'],
        ])->validate();

        try {
            app(ProcessSalesTransaction::class)->handle(
                $cart,
                (int) Auth::id(),
                (string) $validated['payment_type']
            );
        } catch (\Throwable $e) {
            report($e);

            return back()
                ->with('error', $e->getMessage())
                ->withInput();
        }

        return redirect()
            ->route('staff.uniform-checkout')
            ->with('success', 'Sale completed and stock deducted successfully.');
    }

    public function stocksIndex()
    {
        $stocks = UniformStock::query()
            ->orderBy('item_name')
            ->get();

        return $this->facultyView('faculty.stocks', compact('stocks'));
    }

    public function storeStock(Request $request)
    {
        $validated = $request->validate([
            'item_name' => 'required|string|max:100|unique:uniform_stocks,item_name',
            'quantity' => 'required|integer|min:0|max:3000',
            'item_type' => ['nullable', Rule::in(['books', 'uniforms'])],
            'prices' => 'nullable|array',
            'prices.*' => 'nullable|numeric|min:0|max:999999.99',
            'book_price' => 'nullable|numeric|min:0|max:999999.99',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'is_visible' => 'nullable|boolean',
        ]);

        $itemType = $validated['item_type'] ?? null;
        $prices = null;
        $sizes = null;
        $unitPrice = null;
        $quantity = (int) $validated['quantity'];

        if ($itemType === 'uniforms') {
            $sizeKeys = ['XS', 'S', 'M', 'L', 'XL', '2XL', '3XL', '4XL', '5XL'];
            $prices = [];
            $sizes = [];
            $quantity = 0;

            foreach ($sizeKeys as $sizeKey) {
                $inputKey = strtolower($sizeKey);
                $priceValue = $request->input('price_' . $inputKey, $request->input('prices.' . $sizeKey, 0));
                $sizeQuantity = max(0, (int) $request->input('qty_' . $inputKey, 0));
                $prices[$sizeKey] = max(0, (float) $priceValue);
                $sizes[$sizeKey] = $sizeQuantity;
                $quantity += $sizeQuantity;
            }
        } elseif ($itemType === 'books') {
            $unitPrice = max(0, (float) ($validated['book_price'] ?? 0));
        }

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('stocks', 'public');
        }

        $stock = UniformStock::create([
            'item_name' => $validated['item_name'],
            'icon' => null,
            'image' => $imagePath,
            'item_type' => $itemType,
            'sizes' => $sizes,
            'prices' => $prices,
            'unit_price' => $unitPrice,
            'quantity' => $quantity,
            'is_visible' => (bool) ($validated['is_visible'] ?? false),
        ]);

        $actor = Auth::user();

        ActivityLog::log(
            'stock_added',
            'uniform_stock',
            (string) $stock->id,
            "Faculty added stock item {$stock->item_name}",
            [
                'stock_id' => $stock->id,
                'item_name' => $stock->item_name,
                'quantity' => $stock->quantity,
                'is_visible' => $stock->is_visible,
                'user_id' => $actor?->id,
                'user_name' => $actor?->name,
            ]
        );

        return back()->with('success', 'Stock item added successfully.');
    }

    public function updateStock(Request $request, UniformStock $stock)
    {
        $validated = $request->validate([
            'item_name' => 'required|string|max:100|unique:uniform_stocks,item_name,' . $stock->id,
            'quantity' => 'required|integer|min:0|max:3000',
            'item_type' => ['nullable', Rule::in(['books', 'uniforms'])],
            'prices' => 'nullable|array',
            'prices.*' => 'nullable|numeric|min:0|max:999999.99',
            'book_price' => 'nullable|numeric|min:0|max:999999.99',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'remove_image' => 'nullable|boolean',
        ]);

        $itemType = $validated['item_type'] ?? $stock->item_type;
        $prices = null;
        $sizes = null;
        $unitPrice = null;
        $newQuantity = (int) $validated['quantity'];

        if ($itemType === 'uniforms') {
            $sizeKeys = ['XS', 'S', 'M', 'L', 'XL', '2XL', '3XL', '4XL', '5XL'];
            $prices = [];
            $sizes = [];
            $newQuantity = 0;

            foreach ($sizeKeys as $sizeKey) {
                $inputKey = strtolower($sizeKey);
                $priceValue = $request->input('price_' . $inputKey, $request->input('prices.' . $sizeKey, 0));
                $sizeQuantity = max(0, (int) $request->input('qty_' . $inputKey, 0));
                $prices[$sizeKey] = max(0, (float) $priceValue);
                $sizes[$sizeKey] = $sizeQuantity;
                $newQuantity += $sizeQuantity;
            }
        } elseif ($itemType === 'books') {
            $unitPrice = max(0, (float) ($validated['book_price'] ?? 0));
        }

        $oldItemName = (string) $stock->item_name;
        $oldQuantity = (int) $stock->quantity;

        $imagePath = $stock->image;
        if ($request->hasFile('image')) {
            if ($imagePath) {
                Storage::disk('public')->delete($imagePath);
            }
            $imagePath = $request->file('image')->store('stocks', 'public');
        } elseif ($request->boolean('remove_image')) {
            if ($imagePath) {
                Storage::disk('public')->delete($imagePath);
            }
            $imagePath = null;
        }

        $stock->update([
            'item_name' => $validated['item_name'],
            'item_type' => $itemType,
            'sizes' => $sizes,
            'prices' => $prices,
            'unit_price' => $unitPrice,
            'quantity' => $newQuantity,
            'image' => $imagePath,
        ]);

        $actor = Auth::user();

        ActivityLog::log(
            'stock_updated',
            'uniform_stock',
            (string) $stock->id,
            "Faculty updated stock item {$stock->item_name}",
            [
                'stock_id' => $stock->id,
                'old_item_name' => $oldItemName,
                'new_item_name' => $stock->item_name,
                'old_quantity' => $oldQuantity,
                'new_quantity' => $stock->quantity,
                'user_id' => $actor?->id,
                'user_name' => $actor?->name,
            ]
        );

        return back()->with('success', "Stock item updated for {$stock->item_name}.");
    }

    public function destroyStock(UniformStock $stock)
    {
        $stockId = (int) $stock->id;
        $itemName = (string) $stock->item_name;
        $imagePath = $stock->image;

        $stock->salesOrderItems()->delete();

        if ($imagePath) {
            Storage::disk('public')->delete($imagePath);
        }

        $stock->delete();

        $actor = Auth::user();

        ActivityLog::log(
            'stock_deleted',
            'uniform_stock',
            (string) $stockId,
            "Faculty deleted stock item {$itemName}",
            [
                'stock_id' => $stockId,
                'item_name' => $itemName,
                'deleted_image' => $imagePath,
                'user_id' => $actor?->id,
                'user_name' => $actor?->name,
            ]
        );

        return back()->with('success', 'Item deleted successfully.');
    }

    public function toggleStockVisibility(UniformStock $stock)
    {
        $previousVisibility = (bool) $stock->is_visible;
        $stock->is_visible = ! $previousVisibility;
        $stock->save();

        $actor = Auth::user();

        ActivityLog::log(
            'stock_visibility_toggled',
            'uniform_stock',
            (string) $stock->id,
            "Faculty toggled visibility for {$stock->item_name}",
            [
                'stock_id' => $stock->id,
                'item_name' => $stock->item_name,
                'old_visibility' => $previousVisibility,
                'new_visibility' => (bool) $stock->is_visible,
                'user_id' => $actor?->id,
                'user_name' => $actor?->name,
            ]
        );

        return back()->with('success', $stock->is_visible
            ? "{$stock->item_name} is now visible on the products page."
            : "{$stock->item_name} is now hidden from the products page.");
    }

    public function partnershipsIndex(Request $request)
    {
        $query = PartnershipApplication::query()->with(['user:id,name,email', 'reviewer:id,name']);

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('business_name', 'like', "%{$search}%");
            });
        }

        $applications = $query->latest()->paginate(15)->withQueryString();

        return $this->facultyView('faculty.partnerships.index', compact('applications'));
    }

    public function partnershipsShow(int $id)
    {
        $application = PartnershipApplication::with(['user:id,name,email', 'reviewer:id,name'])->findOrFail($id);

        return $this->facultyView('faculty.partnerships.show', compact('application'));
    }

    public function submitRecommendation(Request $request, int $id)
    {
        $application = PartnershipApplication::findOrFail($id);

        if ($application->reviewed_by) {
            return back()->with('error', 'This application already has a faculty recommendation.');
        }

        $validated = $request->validate([
            'faculty_recommendation' => ['required', Rule::in(['recommend_approval', 'recommend_rejection'])],
            'faculty_notes' => ['nullable', 'string', 'max:3000'],
        ]);

        $application->update([
            'faculty_recommendation' => $validated['faculty_recommendation'],
            'faculty_notes' => $validated['faculty_notes'] ?? null,
            'reviewed_by' => Auth::id(),
        ]);

        ActivityLog::log(
            'faculty_recommendation_submitted',
            'partnership',
            (string) $application->id,
            "Faculty submitted recommendation for partnership application #{$application->id}",
            [
                'application_id' => $application->id,
                'faculty_recommendation' => $validated['faculty_recommendation'],
                'faculty_notes' => $validated['faculty_notes'] ?? null,
                'user_id' => Auth::id(),
            ]
        );

        return back()->with('success', 'Recommendation submitted successfully.');
    }

    public function uploadDocument(Request $request, int $id)
    {
        $application = PartnershipApplication::findOrFail($id);

        $validated = $request->validate([
            'document_type' => ['required', Rule::in(['moa', 'contract', 'letter_of_intent'])],
            'document' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
        ], [
            'document.max' => 'File must not exceed 10MB.',
        ]);

        $columnMap = [
            'moa' => 'moa_path',
            'contract' => 'contract_path',
            'letter_of_intent' => 'letter_of_intent_path',
        ];

        $documentType = $validated['document_type'];
        $column = $columnMap[$documentType];
        $existingPath = $application->{$column};

        if ($documentType === 'letter_of_intent' && ! $existingPath) {
            $existingPath = $application->letter_of_intent;
        }

        if ($existingPath) {
            Storage::disk('public')->delete($existingPath);
        }

        $file = $validated['document'];
        $extension = $file->getClientOriginalExtension();
        $filename = $documentType . '_' . now()->format('YmdHis') . '_' . $application->id . '.' . $extension;
        $path = $file->storeAs('partnership_letters/' . $application->id, $filename, 'public');

        $updatePayload = [$column => $path];
        if ($documentType === 'letter_of_intent') {
            $updatePayload['letter_of_intent'] = $path;
        }

        $application->update($updatePayload);

        ActivityLog::log(
            'faculty_document_uploaded',
            'partnership',
            (string) $application->id,
            "Faculty uploaded {$documentType} for partnership application #{$application->id}",
            [
                'application_id' => $application->id,
                'document_type' => $documentType,
                'document_path' => $path,
                'user_id' => Auth::id(),
            ]
        );

        return back()->with('success', ucfirst(str_replace('_', ' ', $documentType)) . ' uploaded successfully.');
    }

    public function viewPartnershipDocument(Request $request, PartnershipApplication $application, string $type)
    {
        $documentMap = [
            'letter_of_intent' => ['letter_of_intent_path', 'letter_of_intent'],
            'receipt' => ['receipt_path', null],
            'moa' => ['moa_path', null],
            'contract' => ['contract_path', null],
        ];

        if (! array_key_exists($type, $documentMap)) {
            abort(404);
        }

        [$primaryColumn, $fallbackColumn] = $documentMap[$type];
        $sourceColumns = [$type . '_paths', $primaryColumn];
        if ($fallbackColumn) {
            $sourceColumns[] = $fallbackColumn;
        }

        $candidates = $this->resolveDocumentPathCandidates($application, $type, $sourceColumns);

        // When a specific picture index is requested, serve exactly that file.
        $requestedIndex = $request->query('index');
        if ($requestedIndex !== null) {
            $target = $candidates[(int) $requestedIndex] ?? null;
            if ($target && Storage::disk('public')->exists($target)) {
                return response()->file(Storage::disk('public')->path($target));
            }
            abort(404, 'Document file missing from storage.');
        }

        foreach ($candidates as $resolvedPath) {
            if (! Storage::disk('public')->exists($resolvedPath)) {
                continue;
            }

            $updates = [];
            if ($application->{$primaryColumn} !== $resolvedPath) {
                $updates[$primaryColumn] = $resolvedPath;
            }

            if ($type === 'letter_of_intent' && $fallbackColumn && $application->{$fallbackColumn} !== $resolvedPath) {
                $updates[$fallbackColumn] = $resolvedPath;
            }

            if (! empty($updates)) {
                $application->forceFill($updates)->save();
            }

            return response()->file(Storage::disk('public')->path($resolvedPath));
        }

        abort(404, 'Document file missing from storage.');
    }

    private function resolveDocumentPathCandidates(PartnershipApplication $application, string $type, array $sourceColumns): array
    {
        $candidates = [];

        foreach ($sourceColumns as $column) {
            $rawPath = $application->{$column};
            if (! $rawPath) {
                continue;
            }

            $values = is_array($rawPath) ? $rawPath : [$rawPath];
            foreach ($values as $value) {
                if (! $value) {
                    continue;
                }
                $candidates = array_merge($candidates, $this->normalizeStoragePathCandidates((string) $value));
            }
        }

        // Fallback: discover files in likely folders if DB path became stale.
        $possibleFolders = array_values(array_unique(array_filter([
            $application->user_id,
            $application->id,
        ])));

        foreach ($possibleFolders as $folder) {
            $directory = 'partnership_letters/' . $folder;

            foreach (Storage::disk('public')->files($directory) as $file) {
                if ($this->documentFileMatchesType($file, $type)) {
                    $candidates[] = $file;
                }
            }
        }

        return array_values(array_unique(array_filter($candidates)));
    }

    private function normalizeStoragePathCandidates(string $path): array
    {
        $trimmed = ltrim($path, '/');
        $candidates = [$trimmed];

        if (str_starts_with($trimmed, 'public/')) {
            $candidates[] = substr($trimmed, 7);
        }

        if (str_starts_with($trimmed, 'storage/')) {
            $candidates[] = substr($trimmed, 8);
        }

        if (str_starts_with($trimmed, 'app/public/')) {
            $candidates[] = substr($trimmed, 11);
        }

        if (filter_var($path, FILTER_VALIDATE_URL)) {
            $parsedPath = (string) parse_url($path, PHP_URL_PATH);
            if ($parsedPath !== '') {
                $candidates = array_merge($candidates, $this->normalizeStoragePathCandidates($parsedPath));
            }
        }

        return array_values(array_unique(array_filter($candidates)));
    }

    private function documentFileMatchesType(string $path, string $type): bool
    {
        $filename = strtolower(pathinfo($path, PATHINFO_FILENAME));

        return match ($type) {
            'letter_of_intent' => str_starts_with($filename, 'letter_of_intent'),
            'receipt' => str_starts_with($filename, 'receipt'),
            'moa' => $filename === 'moa' || str_starts_with($filename, 'moa_'),
            'contract' => str_starts_with($filename, 'contract'),
            default => false,
        };
    }

    public function wizardApproveLOI(PartnershipApplication $application)
    {
        try {
            if ($application->wizard_status !== 'loi_submitted') {
                return response()->json(['success' => false, 'message' => 'Invalid state.']);
            }

            $application->update([
                'wizard_status' => 'form_pending',
            ]);

            ActivityLog::log(
                'wizard_loi_approved',
                'partnership_application',
                (string) $application->id,
                'Wizard LOI approved by faculty.'
            );

            if ($this->shouldSendPartnershipUpdateEmail($application)) {
                $this->sendMail($application->email, new PartnershipStepApprovedMail(
                    $application->fresh(),
                    'Step 1 of 4 — Letter of Intent',
                    'Your Letter of Intent has been approved',
                    'You may now proceed to fill out and submit your Application Form with your business details.',
                    'Step 2 — Application Form'
                ));
            }

            return response()->json([
                'success' => true,
                'message' => 'Letter of Intent approved. Concessionaire can now fill the application form.',
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function wizardRejectLOI(Request $request, PartnershipApplication $application)
    {
        try {
            if ($application->wizard_status !== 'loi_submitted') {
                return response()->json(['success' => false, 'message' => 'Invalid state.']);
            }

            $request->validate([
                'reason' => 'required|string|min:10|max:500',
            ]);

            $application->update([
                'wizard_status' => 'loi_rejected',
                'loi_rejection_reason' => $request->reason,
            ]);

            ActivityLog::log(
                'wizard_loi_rejected',
                'partnership_application',
                (string) $application->id,
                'Wizard LOI rejected by faculty.',
                [
                    'reason' => $request->reason,
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Letter of Intent rejected.',
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function wizardApproveForm(PartnershipApplication $application)
    {
        try {
            if ($application->wizard_status !== 'form_submitted') {
                return response()->json(['success' => false, 'message' => 'Invalid state.']);
            }

            $application->update([
                'wizard_status' => 'docs_in_progress',
            ]);

            ActivityLog::log(
                'wizard_form_approved',
                'partnership_application',
                (string) $application->id,
                'Wizard application form approved by faculty.'
            );

            if ($this->shouldSendPartnershipUpdateEmail($application)) {
                $this->sendMail($application->email, new PartnershipStepApprovedMail(
                    $application->fresh(),
                    'Step 2 of 4 — Application Form',
                    'Your Application Form has been approved',
                    'The EBA Office will now prepare and verify your office requirements (recommendation, notices, and MOA/Contract). You will be notified again once these are complete and you can proceed to payment.',
                    'Step 3 — Office Requirements'
                ));
            }

            return response()->json([
                'success' => true,
                'message' => 'Application form approved. Track physical documents next.',
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function wizardRejectForm(Request $request, PartnershipApplication $application)
    {
        try {
            if ($application->wizard_status !== 'form_submitted') {
                return response()->json(['success' => false, 'message' => 'Invalid state.']);
            }

            $request->validate([
                'reason' => 'required|string|min:10|max:500',
            ]);

            $application->update([
                'wizard_status' => 'form_rejected',
                'form_rejection_reason' => $request->reason,
            ]);

            ActivityLog::log(
                'wizard_form_rejected',
                'partnership_application',
                (string) $application->id,
                'Wizard application form rejected by faculty.',
                [
                    'reason' => $request->reason,
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Application form rejected.',
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function wizardTickDoc(Request $request, PartnershipApplication $application)
    {
        try {
            if (! in_array($application->wizard_status, ['docs_in_progress', 'receipt_pending'], true)) {
                return response()->json(['success' => false, 'message' => 'Invalid state.']);
            }

            $request->validate([
                'doc' => 'required|in:recommendation,notice_occupy,notice_termination,moa_contract',
                'checked' => 'required|boolean',
            ]);

            $columnMap = [
                'recommendation' => 'docs_recommendation_checked',
                'notice_occupy' => 'docs_notice_occupy_checked',
                'notice_termination' => 'docs_notice_termination_checked',
                'moa_contract' => 'docs_moa_contract_checked',
            ];

            $column = $columnMap[$request->doc];

            $application->update([
                $column => $request->boolean('checked'),
            ]);
            $application->refresh();

            $advancedToReceiptPending = false;
            if ($application->allPhysicalDocsChecked() && $application->wizard_status === 'docs_in_progress') {
                $application->update(['wizard_status' => 'receipt_pending']);
                $advancedToReceiptPending = true;
            } elseif (! $application->allPhysicalDocsChecked() && $application->wizard_status === 'receipt_pending') {
                $application->update(['wizard_status' => 'docs_in_progress']);
            }
            $application->refresh();

            if ($advancedToReceiptPending && $this->shouldSendPartnershipUpdateEmail($application)) {
                $this->sendMail($application->email, new PartnershipStepApprovedMail(
                    $application->fresh(),
                    'Step 3 of 4 — Office Requirements',
                    'Your office requirements are complete',
                    'All required documents have been received and verified by the EBA Office. You may now proceed to upload your official payment receipt to finish your application.',
                    'Step 4 — Payment Receipt'
                ));
            }

            ActivityLog::log(
                'wizard_doc_ticked',
                'partnership_application',
                (string) $application->id,
                'Wizard physical document checklist updated by faculty.',
                [
                    'doc' => $request->doc,
                    'checked' => $request->boolean('checked'),
                ]
            );

            return response()->json([
                'success' => true,
                'all_docs_checked' => $application->allPhysicalDocsChecked(),
                'wizard_status' => $application->wizard_status,
                'docs' => [
                    'recommendation' => $application->docs_recommendation_checked,
                    'notice_occupy' => $application->docs_notice_occupy_checked,
                    'notice_termination' => $application->docs_notice_termination_checked,
                    'moa_contract' => $application->docs_moa_contract_checked,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function wizardFinalApprove(PartnershipApplication $application)
    {
        try {
            if ($application->wizard_status !== 'receipt_submitted') {
                return response()->json(['success' => false, 'message' => 'Invalid state.']);
            }

            $application->update([
                'wizard_status' => 'final_approved',
                'status' => 'approved',
            ]);

            if ($application->user) {
                $application->user->update([
                    'is_approved' => true,
                    'is_active_concessionaire' => true,
                ]);
            }

            ActivityLog::log(
                'partnership_approved',
                'partnership',
                (string) $application->id,
                "Faculty approved partnership application #{$application->id} via wizard final approval",
                [
                    'application_id' => $application->id,
                    'flow' => 'wizard',
                    'user_id' => Auth::id(),
                ]
            );

            try {
                if ($application->user) {
                    Mail::to($application->user->email)->send(new PartnershipApprovedMail($application));
                }
            } catch (\Exception $mailEx) {
                Log::warning('PartnershipApprovedMail failed: ' . $mailEx->getMessage());
            }

            return response()->json([
                'success' => true,
                'message' => 'Application fully approved. Concessionaire can now access their dashboard.',
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    private function sendMail(string $recipient, Mailable $mailable): void
    {
        try {
            if (config('queue.default') !== 'sync') {
                Mail::to($recipient)->queue($mailable);
                return;
            }

            Mail::to($recipient)->send($mailable);
        } catch (\Exception $e) {
            Log::warning('Mail failed: ' . $e->getMessage(), [
                'controller' => self::class,
                'recipient' => $recipient,
                'mailable' => $mailable::class,
            ]);
        }
    }

    private function shouldSendPartnershipUpdateEmail(PartnershipApplication $application): bool
    {
        if (! $application->user_id) {
            return true;
        }

        $user = User::find($application->user_id);

        if (! $user) {
            return true;
        }

        return $user->getNotificationPreference('email_partnership_updates');
    }

    public function concessionairesIndex(Request $request)
    {
        $currentMonthStart = now()->startOfMonth();
        $currentMonthEnd = now()->endOfMonth();

        $concessionaires = User::query()
            ->where('role', 'concessionaire')
            ->where('is_approved', true)
            ->where('is_active_concessionaire', true)
            ->withCount([
                'concessionairePayments as current_month_payment_count' => function ($paymentQuery) use ($currentMonthStart, $currentMonthEnd) {
                    $paymentQuery->whereBetween('payment_date', [$currentMonthStart, $currentMonthEnd]);
                },
            ])
            ->withSum('concessionairePayments as total_paid', 'amount')
            ->withMax('concessionairePayments as last_payment_date', 'payment_date')
            ->orderBy('business_name')
            ->orderBy('name')
            ->get();

        $today = now()->day;
        $overdueCount = $concessionaires->filter(function (User $concessionaire) use ($today) {
            $monthlyFee = (float) ($concessionaire->monthly_fee ?? 0);
            $hasPaidThisMonth = (int) ($concessionaire->current_month_payment_count ?? 0) > 0;

            if ($monthlyFee <= 0 || $hasPaidThisMonth) {
                return false;
            }

            return $today >= 1 && $today < 25;
        })->count();

        return $this->facultyView('faculty.concessionaires.index', compact('concessionaires', 'overdueCount'));
    }

    public function concessionairesEdit(int $id)
    {
        $concessionaire = User::query()
            ->where('role', 'concessionaire')
            ->where('is_approved', true)
            ->findOrFail($id);

        return $this->facultyView('faculty.concessionaires.edit', compact('concessionaire'));
    }

    public function concessionairesUpdate(Request $request, int $id)
    {
        $concessionaire = User::query()
            ->where('role', 'concessionaire')
            ->where('is_approved', true)
            ->findOrFail($id);

        $validated = $request->validate([
            'business_name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'location' => ['nullable', 'string', 'max:255'],
        ]);

        $original = [
            'business_name' => $concessionaire->business_name,
            'description' => $concessionaire->description,
            'location' => $concessionaire->location,
        ];

        $concessionaire->update($validated);

        ActivityLog::log(
            'faculty_concessionaire_updated',
            'user',
            (string) $concessionaire->id,
            "Faculty updated concessionaire profile for {$concessionaire->name}",
            [
                'concessionaire_id' => $concessionaire->id,
                'before' => $original,
                'after' => [
                    'business_name' => $concessionaire->business_name,
                    'description' => $concessionaire->description,
                    'location' => $concessionaire->location,
                ],
                'user_id' => Auth::id(),
            ]
        );

        return redirect()->route('staff.concessionaires.index')->with('success', 'Concessionaire details updated successfully.');
    }

    public function updateMonthlyFee(Request $request, int $id)
    {
        $user = User::findOrFail($id);

        if ($user->role !== 'concessionaire') {
            return back()->with('error', 'Monthly fee can only be set for concessionaire accounts.');
        }

        $validated = $request->validate([
            'monthly_fee' => ['required', 'numeric', 'min:0', 'max:999999.99'],
        ]);

        $user->update([
            'monthly_fee' => $validated['monthly_fee'],
        ]);

        $businessName = $user->business_name ?: $user->name;

        return back()->with('success', "Monthly fee updated for {$businessName}.");
    }

    public function history()
    {
        $logs = ActivityLog::query()
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(20);

        return $this->facultyView('faculty.history', compact('logs'));
    }

    public function markPaymentsRead(Request $request)
    {
        $user = $request->user();

        if (! $user || $user->role !== 'faculty') {
            abort(403);
        }

        $user->forceFill([
            'payment_notifications_read_at' => now(),
        ])->save();

        if ($request->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return back();
    }
}
