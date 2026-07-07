<?php

namespace App\Http\Controllers\Concessionaire;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\ConcessionaireMedia;
use App\Models\ConcessionairePayment;
use App\Models\ConcessionaireReview;
use App\Models\PartnershipApplication;
use App\Models\Product;
use App\Models\ProductReview;
use App\Models\User;
use App\Services\WordFilterService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ConcessionaireController extends Controller
{
    /**
     * Show the concessionaire dashboard.
     */
    public function dashboard()
    {
        $user = Auth::user();
        if (! $user instanceof User) {
            abort(403);
        }

        return view('concessionaire.dashboard', $this->buildDashboardViewData($user, 'overview'));
    }

    /**
     * Show payment history for the authenticated concessionaire.
     */
    public function paymentsIndex()
    {
        $user = Auth::user();
        if (! $user instanceof User) {
            abort(403);
        }

        $paymentsQuery = ConcessionairePayment::query()
            ->where('concessionaire_id', $user->id)
            ->with('recordedBy:id,name')
            ->latest('payment_date')
            ->latest('id');

        $payments = $paymentsQuery->paginate(15)->withQueryString();

        $totalPaid = (clone $paymentsQuery)->sum('amount');
        $lastPaymentDate = (clone $paymentsQuery)->value('payment_date');
        $lastPaymentDate = $lastPaymentDate ? Carbon::parse($lastPaymentDate) : null;
        $latestApplication = $user->latestPartnershipApplication;

        return view('concessionaire.payments', compact(
            'user',
            'payments',
            'totalPaid',
            'lastPaymentDate',
            'latestApplication'
        ));
    }

    public function downloadReceipt(Request $request, ConcessionairePayment $payment)
    {
        $user = Auth::user();
        if (! $user instanceof User) {
            abort(403);
        }

        if ((int) $payment->concessionaire_id !== (int) $user->id) {
            abort(403);
        }

        $payment->loadMissing(['concessionaire', 'recordedBy', 'partnershipApplication']);

        $receiptYear = $payment->payment_date?->format('Y') ?? now()->format('Y');
        $receiptNumber = sprintf('RCP-%s-%05d', $receiptYear, $payment->id);
        $logoPath = public_path('images/eba-logo.png');
        $logoDataUri = file_exists($logoPath)
            ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath))
            : null;

        $pdf = Pdf::loadView('pdf.payment-receipt', [
            'payment' => $payment,
            'receiptNumber' => $receiptNumber,
            'generatedAt' => now(),
            'logoDataUri' => $logoDataUri,
        ])->setPaper('a4');

        $filename = sprintf('invoice-%s.pdf', $receiptNumber);

        return $pdf->download($filename);
    }

    /**
     * Show edit profile form.
     */
    public function editProfile()
    {
        $user = Auth::user();
        if (! $user instanceof User) {
            abort(403);
        }

        return view('concessionaire.edit-profile', compact('user'));
    }

    /**
     * Update profile.
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        if (! $user instanceof User) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'business_name' => 'sometimes|nullable|string|max:255',
            'location' => 'sometimes|nullable|string|max:255',
            'description' => 'sometimes|nullable|string|max:1000',
        ]);

        foreach ($validated as $key => $value) {
            $user->$key = $value;
        }
        $user->save();

        return back()->with('success', 'Profile updated successfully.');
    }

    /**
     * Show reviews tab.
     */
    public function reviews(Request $request)
    {
        $user = Auth::user();
        if (! $user instanceof User) {
            abort(403);
        }

        $minRating = trim((string) $request->query('min_rating', ''));
        if (! in_array($minRating, ['', '1', '2', '3', '4', '5'], true)) {
            $minRating = '';
        }

        $type = trim((string) $request->query('type', ''));
        if (! in_array($type, ['', 'store', 'product'], true)) {
            $type = '';
        }

        $search = trim((string) $request->query('search', ''));
        $storeTargetName = $user->business_name ?: $user->name;

        $storeReviews = ConcessionaireReview::query()
            ->with('user:id,name')
            ->where('concessionaire_id', $user->id)
            ->get()
            ->map(function (ConcessionaireReview $review) use ($storeTargetName) {
                return [
                    'type' => 'store',
                    'type_label' => 'Store Review',
                    'reviewer_name' => $review->user?->name ?? 'Anonymous',
                    'rating' => (int) $review->rating,
                    'comment' => (string) ($review->comment ?? ''),
                    'created_at' => $review->created_at,
                    'target' => $storeTargetName,
                ];
            });

        $productReviews = ProductReview::query()
            ->with(['user:id,name', 'product:id,name,concessionaire_id'])
            ->whereHas('product', function ($query) use ($user) {
                $query->where('concessionaire_id', $user->id);
            })
            ->get()
            ->map(function (ProductReview $review) {
                return [
                    'type' => 'product',
                    'type_label' => 'Product Review',
                    'reviewer_name' => $review->user?->name ?? 'Anonymous',
                    'rating' => (int) $review->rating,
                    'comment' => (string) ($review->comment ?? ''),
                    'created_at' => $review->created_at,
                    'target' => $review->product?->name ?? 'Unknown Product',
                ];
            });

        $allReviews = $storeReviews
            ->concat($productReviews)
            ->values();

        $totalReviews = $allReviews->count();
        $averageRating = $totalReviews > 0
            ? round((float) $allReviews->avg('rating'), 1)
            : null;
        $fiveStarCount = $allReviews->where('rating', 5)->count();
        $fiveStarShare = $totalReviews > 0
            ? (int) round(($fiveStarCount / $totalReviews) * 100)
            : 0;

        $filteredReviews = $allReviews;

        if ($type !== '') {
            $filteredReviews = $filteredReviews->where('type', $type);
        }

        if ($minRating !== '') {
            $filteredReviews = $filteredReviews->where('rating', (int) $minRating);
        }

        if ($search !== '') {
            $searchTerm = mb_strtolower($search);
            $filteredReviews = $filteredReviews->filter(function (array $review) use ($searchTerm) {
                $comment = mb_strtolower(trim((string) ($review['comment'] ?? '')));

                return str_contains($comment, $searchTerm);
            });
        }

        $filteredReviews = $filteredReviews
            ->sortByDesc(function (array $review) {
                $createdAt = $review['created_at'] ?? null;

                return $createdAt ? $createdAt->getTimestamp() : 0;
            })
            ->values();

        $perPage = 12;
        $currentPage = max(1, (int) $request->query('page', 1));

        $reviews = new LengthAwarePaginator(
            $filteredReviews->forPage($currentPage, $perPage)->values(),
            $filteredReviews->count(),
            $perPage,
            $currentPage,
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );

        $stats = [
            'rating' => $averageRating ?? 0.0,
            'review_count' => $totalReviews,
            'location' => $user->location ?? 'Location not set',
        ];

        return view('concessionaire.reviews', compact(
            'user',
            'reviews',
            'type',
            'minRating',
            'search',
            'totalReviews',
            'averageRating',
            'fiveStarShare',
            'stats'
        ));
    }

    /**
     * Show products tab.
     */
    public function products()
    {
        $user = Auth::user();
        if (! $user instanceof User) {
            abort(403);
        }

        $search = trim((string) request('search', ''));
        $category = trim((string) request('category', ''));
        $sort = trim((string) request('sort', 'newest'));

        $productsQuery = Product::where('concessionaire_id', $user->id)
            ->notDeleted();

        if ($search !== '') {
            $productsQuery->where(function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%')
                    ->orWhere('description', 'like', '%' . $search . '%');
            });
        }

        if ($category !== '') {
            $productsQuery->where('category', $category);
        }

        switch ($sort) {
            case 'oldest':
                $productsQuery->orderBy('created_at', 'asc');
                break;
            case 'price_low':
                $productsQuery->orderBy('price', 'asc');
                break;
            case 'price_high':
                $productsQuery->orderBy('price', 'desc');
                break;
            default:
                $sort = 'newest';
                $productsQuery->orderBy('created_at', 'desc');
                break;
        }

        $products = $productsQuery
            ->paginate(12)
            ->withQueryString();

        $categories = Product::where('concessionaire_id', $user->id)
            ->notDeleted()
            ->distinct()
            ->pluck('category')
            ->filter()
            ->sort()
            ->values();

        return view('concessionaire.products', compact('user', 'products', 'search', 'category', 'sort', 'categories'));
    }

    /**
     * Store a newly created product.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        if (! $user instanceof User) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'category' => 'required|string|max:100',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'is_available' => 'nullable|boolean',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products/' . $user->id, 'public');
            $imagePath = $this->normalizeStoredImagePath($imagePath);
        }

        $product = Product::create([
            'concessionaire_id' => $user->id,
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'price' => $validated['price'],
            'category' => $validated['category'],
            'image' => $imagePath,
            'is_available' => array_key_exists('is_available', $validated)
                ? (bool) $validated['is_available']
                : true,
        ]);

        ActivityLog::log(
            'product_created',
            'product',
            (string) $product->id,
            "Created product {$product->name}",
            [
                'product_id' => $product->id,
                'concessionaire_id' => $user->id,
            ]
        );

        return redirect()->route('concessionaire.products')->with('success', 'Product added successfully.');
    }

    /**
     * Show edit form for a product.
     */
    public function edit(Product $product)
    {
        $this->authorizeProductOwnership($product);

        return view('concessionaire.edit-product', compact('product'));
    }

    /**
     * Update an existing product.
     */
    public function update(Request $request, Product $product)
    {
        $this->authorizeProductOwnership($product);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'category' => 'required|string|max:100',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'is_available' => 'nullable|boolean',
        ]);

        $imagePath = $product->image;
        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('public')->delete($this->normalizeStoredImagePath($product->image));
            }
            $imagePath = $request->file('image')->store('products/' . $product->concessionaire_id, 'public');
            $imagePath = $this->normalizeStoredImagePath($imagePath);
        }

        $product->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'price' => $validated['price'],
            'category' => $validated['category'],
            'image' => $imagePath,
            'is_available' => array_key_exists('is_available', $validated)
                ? (bool) $validated['is_available']
                : $product->is_available,
        ]);

        ActivityLog::log(
            'product_updated',
            'product',
            (string) $product->id,
            "Updated product {$product->name}",
            [
                'product_id' => $product->id,
                'concessionaire_id' => $product->concessionaire_id,
            ]
        );

        return redirect()->route('concessionaire.products')->with('success', 'Product updated successfully.');
    }

    /**
     * Delete a product.
     */
    public function destroy(Product $product)
    {
        $this->authorizeProductOwnership($product);

        if ($product->image) {
            Storage::disk('public')->delete($this->normalizeStoredImagePath($product->image));
        }

        $productId = $product->id;
        $productName = $product->name;
        $ownerId = $product->concessionaire_id;
        if (method_exists($product, 'forceDelete')) {
            $product->forceDelete();
        } else {
            $product->delete();
        }

        ActivityLog::log(
            'product_deleted',
            'product',
            (string) $productId,
            "Deleted product {$productName}",
            [
                'product_id' => $productId,
                'concessionaire_id' => $ownerId,
            ]
        );

        return redirect()->route('concessionaire.products')->with('success', 'Product deleted successfully.');
    }

    /**
     * Toggle product availability.
     */
    public function toggleAvailability(Product $product)
    {
        $this->authorizeProductOwnership($product);

        $product->is_available = ! $product->is_available;
        $product->save();

        ActivityLog::log(
            'product_availability_toggled',
            'product',
            (string) $product->id,
            "Toggled product availability for {$product->name}",
            [
                'product_id' => $product->id,
                'is_available' => $product->is_available,
            ]
        );

        return back()->with('success', 'Product availability updated.');
    }

    /**
     * Public list of active concessionaires.
     */
    public function publicIndex()
    {
        $concessionaires = User::query()
            ->select('users.*')
            ->where('role', 'concessionaire')
            ->where('is_active_concessionaire', true)
            ->where('is_approved', true)
            ->with([
                'concessionaireReviews:id,concessionaire_id,rating',
                'products' => function ($query) {
                    $query->available()->notDeleted()
                        ->whereNotNull('image')
                        ->latest()
                        ->select('id', 'concessionaire_id', 'name', 'price', 'image');
                },
            ])
            ->withCount('concessionaireReviews')
            ->withAvg('concessionaireReviews', 'rating')
            ->addSelect([
                'product_reviews_count' => ProductReview::query()
                    ->selectRaw('COUNT(product_reviews.id)')
                    ->join('products', 'products.id', '=', 'product_reviews.product_id')
                    ->whereColumn('products.concessionaire_id', 'users.id'),
                'product_reviews_avg_rating' => ProductReview::query()
                    ->selectRaw('AVG(product_reviews.rating)')
                    ->join('products', 'products.id', '=', 'product_reviews.product_id')
                    ->whereColumn('products.concessionaire_id', 'users.id'),
            ])
            ->withCount([
                'products as products_count' => function ($query) {
                    $query->available()->notDeleted();
                },
            ])
            ->latest()
            ->limit(48)
            ->get();

        // Unified display rating (store reviews preferred, else product reviews)
        // so each card shows a consistent rating chip.
        $concessionaires->each(function ($c) {
            $storeCount = (int) ($c->concessionaire_reviews_count ?? 0);
            $prodCount = (int) ($c->product_reviews_count ?? 0);
            $c->display_review_count = $storeCount > 0 ? $storeCount : $prodCount;
            $c->display_rating = $storeCount > 0
                ? (float) ($c->concessionaire_reviews_avg_rating ?? 0)
                : ($prodCount > 0 ? (float) ($c->product_reviews_avg_rating ?? 0) : 0.0);
        });

        return view('concessionaires.index', compact('concessionaires'));
    }

    /**
     * Public concessionaire profile and products.
     */
    public function publicShow(User $user)
    {
        if ($user->role !== 'concessionaire' || ! $user->is_approved) {
            abort(404);
        }

        $publicDescription = $this->resolvePublicDescription($user);

        $isConcessionaireActive = (bool) $user->is_active_concessionaire;

        $carouselImages = $user->carouselMedia()->get()
            ->map(fn (ConcessionaireMedia $media) => asset('storage/' . $media->path))
            ->values();

        if ($carouselImages->isEmpty() && $user->carousel_image) {
            $carouselImages = collect([asset('storage/' . $user->carousel_image)]);
        }

        $products = Product::where('concessionaire_id', $user->id)
            ->notDeleted()
            ->available()
            ->withCount('reviews')
            ->withAvg('reviews', 'rating')
            ->latest()
            ->paginate(12)
            ->withQueryString();

        $productRatingSummary = ProductReview::query()
            ->selectRaw('AVG(product_reviews.rating) as average_rating, COUNT(product_reviews.id) as review_count')
            ->join('products', 'products.id', '=', 'product_reviews.product_id')
            ->where('products.concessionaire_id', $user->id)
            ->first();

        $concessionaireReviews = ConcessionaireReview::query()
            ->with('user:id,name')
            ->where('concessionaire_id', $user->id)
            ->latest()
            ->get();

        $concessionaireAvgRating = round((float) ($concessionaireReviews->avg('rating') ?? 0), 1);
        $concessionaireReviewCount = $concessionaireReviews->count();
        $concessionaireRatingDistribution = [
            5 => $concessionaireReviews->where('rating', 5)->count(),
            4 => $concessionaireReviews->where('rating', 4)->count(),
            3 => $concessionaireReviews->where('rating', 3)->count(),
            2 => $concessionaireReviews->where('rating', 2)->count(),
            1 => $concessionaireReviews->where('rating', 1)->count(),
        ];

        $productAvgRating = round((float) ($productRatingSummary?->average_rating ?? 0), 1);
        $productReviewCount = (int) ($productRatingSummary?->review_count ?? 0);

        $productReviews = ProductReview::query()
            ->with(['user:id,name', 'product:id,name,concessionaire_id'])
            ->whereHas('product', function ($query) use ($user) {
                $query->where('concessionaire_id', $user->id);
            })
            ->latest()
            ->get();

        $userConcessionaireReview = null;
        if (Auth::check()) {
            $userConcessionaireReview = ConcessionaireReview::query()
                ->where('user_id', Auth::id())
                ->where('concessionaire_id', $user->id)
                ->first();
        }

        return view('concessionaires.show', compact(
            'user',
            'publicDescription',
            'carouselImages',
            'products',
            'isConcessionaireActive',
            'concessionaireReviews',
            'concessionaireAvgRating',
            'concessionaireReviewCount',
            'concessionaireRatingDistribution',
            'productAvgRating',
            'productReviewCount',
            'productReviews',
            'userConcessionaireReview'
        ));
    }

    /**
     * Store a concessionaire-level review.
     */
    public function storeReview(Request $request, User $user)
    {
        if (in_array(Auth::user()?->role, ['admin', 'cashier'], true)) {
            return back()->with('error', 'This account type cannot leave reviews.');
        }

        if ($user->role !== 'concessionaire') {
            abort(404);
        }

        if (Auth::user()?->role === 'concessionaire') {
            abort(403);
        }

        if ((int) $user->id === (int) Auth::id()) {
            return back()->with('error', 'You cannot review your own store.');
        }

        if (! $user->is_active_concessionaire) {
            return back()->with('error', 'This concessionaire is currently inactive.');
        }

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        $filter = app(WordFilterService::class);
        if ($filter->containsBadWord($validated['comment'] ?? '') ||
            $filter->containsBadWord($validated['title'] ?? '')) {
            return back()
                ->withErrors(['comment' => 'Your review contains inappropriate language. Please revise and resubmit.'])
                ->withInput();
        }

        $existing = ConcessionaireReview::query()
            ->where('user_id', Auth::id())
            ->where('concessionaire_id', $user->id)
            ->first();

        if ($existing) {
            return back()->with('error', 'You have already reviewed this concessionaire.');
        }

        $review = ConcessionaireReview::create([
            'user_id' => Auth::id(),
            'concessionaire_id' => $user->id,
            'rating' => $validated['rating'],
            'comment' => $validated['comment'] ?? null,
        ]);

        ActivityLog::log(
            'concessionaire_review_created',
            'concessionaire_review',
            (string) $review->id,
            'Submitted store review for ' . ($user->business_name ?: $user->name),
            [
                'concessionaire_id' => $user->id,
                'rating' => $review->rating,
            ]
        );

        return back()->with('success', 'Your review has been submitted.');
    }

    /**
     * Update concessionaire-level review.
     */
    public function updateReview(Request $request, User $user)
    {
        if (in_array(Auth::user()?->role, ['admin', 'cashier'], true)) {
            return back()->with('error', 'This account type cannot leave reviews.');
        }

        if ($user->role !== 'concessionaire') {
            abort(404);
        }

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        $filter = app(WordFilterService::class);
        if ($filter->containsBadWord($validated['comment'] ?? '') ||
            $filter->containsBadWord($validated['title'] ?? '')) {
            return back()
                ->withErrors(['comment' => 'Your review contains inappropriate language. Please revise and resubmit.'])
                ->withInput();
        }

        $review = ConcessionaireReview::query()
            ->where('user_id', Auth::id())
            ->where('concessionaire_id', $user->id)
            ->firstOrFail();

        $review->update([
            'rating' => $validated['rating'],
            'comment' => $validated['comment'] ?? null,
        ]);

        ActivityLog::log(
            'concessionaire_review_updated',
            'concessionaire_review',
            (string) $review->id,
            'Updated store review for ' . ($user->business_name ?: $user->name),
            [
                'concessionaire_id' => $user->id,
                'rating' => $review->rating,
            ]
        );

        return back()->with('success', 'Your review has been updated.');
    }

    /**
     * Delete concessionaire-level review.
     */
    public function deleteReview(Request $request, User $user)
    {
        if (in_array(Auth::user()?->role, ['admin', 'cashier'], true)) {
            return back()->with('error', 'This account type cannot leave reviews.');
        }

        if ($user->role !== 'concessionaire') {
            abort(404);
        }

        $review = ConcessionaireReview::query()
            ->where('user_id', Auth::id())
            ->where('concessionaire_id', $user->id)
            ->firstOrFail();

        $reviewId = $review->id;
        $review->delete();

        ActivityLog::log(
            'concessionaire_review_deleted',
            'concessionaire_review',
            (string) $reviewId,
            'Deleted store review for ' . ($user->business_name ?: $user->name),
            [
                'concessionaire_id' => $user->id,
            ]
        );

        return back()->with('success', 'Your review has been deleted.');
    }

    /**
     * Show photos tab.
     */
    public function photos()
    {
        $user = Auth::user();
        if (! $user instanceof User) {
            abort(403);
        }

        // Placeholder - connect to actual photos model later
        $photos = collect([]);

        return view('concessionaire.photos', compact('user', 'photos'));
    }

    /**
     * Maximum number of carousel banner images a concessionaire may keep.
     */
    private const CAROUSEL_IMAGE_LIMIT = 8;

    /**
     * Upload one or more carousel banner images for the concessionaire's public page.
     */
    public function updateCarouselImage(Request $request)
    {
        $user = Auth::user();
        if (! $user instanceof User) {
            abort(403);
        }

        $request->validate([
            'carousel_images' => 'required|array|min:1',
            'carousel_images.*' => 'image|mimes:jpg,jpeg,png,webp|max:4096',
        ], [
            'carousel_images.required' => 'Please choose at least one image to upload.',
            'carousel_images.*.image' => 'Each file must be an image.',
            'carousel_images.*.max' => 'Each image must be 4 MB or smaller.',
        ]);

        $existingCount = $user->carouselMedia()->count();
        $remaining = self::CAROUSEL_IMAGE_LIMIT - $existingCount;

        if ($remaining <= 0) {
            return back()->with('error', 'You already have the maximum of ' . self::CAROUSEL_IMAGE_LIMIT . ' banner images. Remove one before adding more.');
        }

        $files = array_slice($request->file('carousel_images'), 0, $remaining);
        $added = 0;

        foreach ($files as $file) {
            $path = $file->store('carousel/' . $user->id, 'public');
            $user->carouselMedia()->create([
                'path' => $this->normalizeStoredImagePath($path),
                'sort_order' => $existingCount + $added,
            ]);
            $added++;
        }

        $this->syncPrimaryCarouselImage($user);

        $skipped = count($request->file('carousel_images')) - $added;
        $message = $added . ' image' . ($added === 1 ? '' : 's') . ' added to your banner.';
        if ($skipped > 0) {
            $message .= ' ' . $skipped . ' skipped (limit of ' . self::CAROUSEL_IMAGE_LIMIT . ' reached).';
        }

        return back()->with('success', $message);
    }

    /**
     * Delete a single carousel banner image from the concessionaire's gallery.
     */
    public function deleteCarouselImage(Request $request)
    {
        $user = Auth::user();
        if (! $user instanceof User) {
            abort(403);
        }

        $request->validate([
            'media_id' => 'required|integer',
        ]);

        $media = $user->carouselMedia()->find($request->input('media_id'));

        if ($media) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($media->path);
            $media->delete();
        }

        $this->syncPrimaryCarouselImage($user);

        return back()->with('success', 'Banner image removed.');
    }

    /**
     * Keep the legacy carousel_image column pointed at the first banner image so
     * concessionaire card thumbnails (index / landing page) keep working.
     */
    private function syncPrimaryCarouselImage(User $user): void
    {
        $first = $user->carouselMedia()->first();
        $user->carousel_image = $first?->path;
        $user->save();
    }

    /**
     * Show Media tab (carousel banner image and store description).
     */
    public function media()
    {
        $user = Auth::user();
        if (! $user instanceof User) {
            abort(403);
        }

        $user->load('carouselMedia');

        return view('concessionaire.media', compact('user'));
    }

    /**
     * Build shared data for concessionaire overview and reviews tabs.
     * Ensures snapshot metrics and customer reviews come from the same model.
     */
    private function buildDashboardViewData(User $user, string $activeTab): array
    {
        $latestContractApplication = PartnershipApplication::query()
            ->where('user_id', $user->id)
            ->whereNotNull('contract_period_end')
            ->latest('contract_period_end')
            ->first();

        $contractPeriodEnd = $latestContractApplication?->contract_period_end;
        $hasContractPeriod = ! is_null($contractPeriodEnd);
        $isExpired = $hasContractPeriod
            ? Carbon::parse($contractPeriodEnd)->endOfDay()->isPast()
            : false;

        $isLegacyAccountWithoutContract = ! $hasContractPeriod;
        $isConcessionaireActive = (bool) $user->is_active_concessionaire || $isLegacyAccountWithoutContract;
        $showInactiveNotice = ! $isConcessionaireActive && $hasContractPeriod && $isExpired;

        $unreadNotifications = $user->unreadNotifications()->latest()->get();

        $customerReviews = ConcessionaireReview::query()
            ->with('user:id,name')
            ->where('concessionaire_id', $user->id)
            ->latest()
            ->get();

        $reviewCount = $customerReviews->count();
        $averageRating = $reviewCount > 0
            ? round((float) $customerReviews->avg('rating'), 1)
            : 0.0;

        $ratingBreakdown = [
            5 => $customerReviews->where('rating', 5)->count(),
            4 => $customerReviews->where('rating', 4)->count(),
            3 => $customerReviews->where('rating', 3)->count(),
            2 => $customerReviews->where('rating', 2)->count(),
            1 => $customerReviews->where('rating', 1)->count(),
        ];

        $productsBaseQuery = Product::query()
            ->where('concessionaire_id', $user->id)
            ->notDeleted();

        $totalProducts = (clone $productsBaseQuery)->count();
        $availableProducts = (clone $productsBaseQuery)->where('is_available', true)->count();
        $unavailableProducts = max(0, $totalProducts - $availableProducts);
        $recentProducts = (clone $productsBaseQuery)
            ->latest()
            ->take(4)
            ->get();

        $recentCustomerReviews = $customerReviews->take(4);

        $productReviewCount = ProductReview::query()
            ->whereHas('product', function ($query) use ($user) {
                $query->where('concessionaire_id', $user->id);
            })
            ->count();

        $monthlyPaymentsTotal = (float) ConcessionairePayment::query()
            ->where('concessionaire_id', $user->id)
            ->whereBetween('payment_date', [now()->startOfMonth(), now()->endOfMonth()])
            ->sum('amount');

        $trendStart = now()->startOfMonth()->subMonths(5);
        $trendEnd = now()->endOfMonth();

        $productReviewTrends = ProductReview::query()
            ->selectRaw('YEAR(product_reviews.created_at) as year_num, MONTH(product_reviews.created_at) as month_num, COUNT(product_reviews.id) as review_count')
            ->join('products', 'products.id', '=', 'product_reviews.product_id')
            ->where('products.concessionaire_id', $user->id)
            ->whereBetween('product_reviews.created_at', [$trendStart, $trendEnd])
            ->groupBy('year_num', 'month_num')
            ->get()
            ->keyBy(fn ($row) => sprintf('%04d-%02d', (int) $row->year_num, (int) $row->month_num));

        $storeReviewTrends = ConcessionaireReview::query()
            ->selectRaw('YEAR(created_at) as year_num, MONTH(created_at) as month_num, COUNT(id) as review_count, AVG(rating) as avg_rating')
            ->where('concessionaire_id', $user->id)
            ->whereBetween('created_at', [$trendStart, $trendEnd])
            ->groupBy('year_num', 'month_num')
            ->get()
            ->keyBy(fn ($row) => sprintf('%04d-%02d', (int) $row->year_num, (int) $row->month_num));

        $reviewTrendData = collect(range(0, 5))
            ->map(function (int $offset) use ($trendStart, $productReviewTrends, $storeReviewTrends) {
                $month = $trendStart->copy()->addMonths($offset);
                $monthKey = $month->format('Y-m');

                $productRow = $productReviewTrends->get($monthKey);
                $storeRow = $storeReviewTrends->get($monthKey);

                return [
                    'month' => $month->format('M Y'),
                    'product_reviews' => (int) ($productRow->review_count ?? 0),
                    'store_reviews' => (int) ($storeRow->review_count ?? 0),
                    'avg_rating' => round((float) ($storeRow->avg_rating ?? 0), 1),
                ];
            })
            ->values()
            ->all();

        // Sparkline trend data for the stat cards (monthly, same 6-month window)
        $sparklineMonths = collect(range(0, 5))
            ->map(fn (int $offset) => $trendStart->copy()->addMonths($offset));

        $ratingSparkline = $sparklineMonths
            ->map(function (CarbonInterface $month) use ($customerReviews) {
                $reviewsToDate = $customerReviews->filter(
                    fn ($review) => $review->created_at && $review->created_at->lte($month->copy()->endOfMonth())
                );

                return $reviewsToDate->isEmpty() ? 0 : round((float) $reviewsToDate->avg('rating'), 2);
            })
            ->values()
            ->all();

        $reviewsSparkline = collect($reviewTrendData)
            ->map(fn (array $row) => $row['store_reviews'] + $row['product_reviews'])
            ->values()
            ->all();

        $productCreatedDates = (clone $productsBaseQuery)->pluck('created_at');
        $productsSparkline = $sparklineMonths
            ->map(function (CarbonInterface $month) use ($productCreatedDates) {
                $endOfMonth = $month->copy()->endOfMonth();

                return $productCreatedDates
                    ->filter(fn ($date) => $date && Carbon::parse($date)->lte($endOfMonth))
                    ->count();
            })
            ->values()
            ->all();

        $paymentRows = ConcessionairePayment::query()
            ->where('concessionaire_id', $user->id)
            ->whereBetween('payment_date', [$trendStart, $trendEnd])
            ->get(['payment_date', 'amount']);

        $paymentsSparkline = $sparklineMonths
            ->map(function (CarbonInterface $month) use ($paymentRows) {
                $monthKey = $month->format('Y-m');

                return (float) $paymentRows
                    ->filter(fn ($payment) => $payment->payment_date && Carbon::parse($payment->payment_date)->format('Y-m') === $monthKey)
                    ->sum('amount');
            })
            ->values()
            ->all();

        $statSparklines = [
            'rating' => $ratingSparkline,
            'reviews' => $reviewsSparkline,
            'products' => $productsSparkline,
            'payments' => $paymentsSparkline,
        ];

        $products = (clone $productsBaseQuery)->get(['category']);
        $productCategoryData = [
            'food' => $products->where('category', 'food')->count(),
            'beverage' => $products->where('category', 'beverage')->count(),
            'snack' => $products->where('category', 'snack')->count(),
        ];

        $stats = [
            'rating' => $averageRating,
            'review_count' => $reviewCount,
            'location' => $user->location ?? 'Location not set',
        ];

        return [
            'user' => $user,
            'stats' => $stats,
            'unreadNotifications' => $unreadNotifications,
            'showInactiveNotice' => $showInactiveNotice,
            'contractPeriodEnd' => $contractPeriodEnd,
            'isConcessionaireActive' => $isConcessionaireActive,
            'activeTab' => $activeTab,
            'customerReviews' => $customerReviews,
            'recentCustomerReviews' => $recentCustomerReviews,
            'ratingBreakdown' => $ratingBreakdown,
            'totalProducts' => $totalProducts,
            'availableProducts' => $availableProducts,
            'unavailableProducts' => $unavailableProducts,
            'recentProducts' => $recentProducts,
            'productReviewCount' => $productReviewCount,
            'monthlyPaymentsTotal' => $monthlyPaymentsTotal,
            'reviewTrendData' => $reviewTrendData,
            'productCategoryData' => $productCategoryData,
            'statSparklines' => $statSparklines,
        ];
    }

    private function resolvePublicDescription(User $user): string
    {
        $description = trim((string) ($user->description ?? ''));

        if ($description === '') {
            return 'No description provided';
        }

        $normalizedDescription = strtolower($description);

        // Prevent internal registration/system-generated text from leaking publicly.
        if (str_contains($normalizedDescription, 'pending concessionaire registration')) {
            return 'No description provided';
        }

        return $description;
    }

    private function authorizeProductOwnership(Product $product): void
    {
        if ((int) $product->concessionaire_id !== (int) Auth::id()) {
            abort(403);
        }
    }

    /**
     * Keep product image paths relative to the public disk root.
     */
    private function normalizeStoredImagePath(?string $path): ?string
    {
        if (! $path) {
            return $path;
        }

        return ltrim(preg_replace('#^(public/|storage/)#', '', $path), '/');
    }
}
