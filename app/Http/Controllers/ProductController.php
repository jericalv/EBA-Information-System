<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductReview;
use App\Models\UniformStock;
use App\Models\User;
use App\Services\WordFilterService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    /**
     * Display listing of products from all concessionaires.
     */
    public function index(Request $request)
    {
        $query = Product::with(['concessionaire', 'reviews'])
            ->notDeleted()
            ->available()
            ->whereHas('concessionaire', function ($q) {
                $q->where('role', 'concessionaire')
                ->where('is_active_concessionaire', true)
                ->where('is_approved', true);
            })
            ->withCount('reviews')
            ->withAvg('reviews', 'rating');

        // Search filter
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('concessionaire', function ($q) use ($search) {
                      $q->where('business_name', 'like', "%{$search}%");
                  });
            });
        }

        // Category filter
        if ($category = $request->input('category')) {
            $query->category($category);
        }

        // Concessionaire filter
        if ($concessionaireId = $request->input('concessionaire_id')) {
            $query->where('concessionaire_id', $concessionaireId);
        }

        // Sort options
        $sort = $request->input('sort', 'newest');
        switch ($sort) {
            case 'price_low':
                $query->orderBy('price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('price', 'desc');
                break;
            case 'rating':
                $query->orderByDesc('reviews_avg_rating');
                break;
            case 'popular':
                $query->orderByDesc('reviews_count');
                break;
            default:
                $query->latest();
        }

        $products = $query->paginate(12)->withQueryString();

        $categories = Product::notDeleted()
            ->available()
            ->whereHas('concessionaire', function ($q) {
                $q->where('role', 'concessionaire')
                ->where('is_active_concessionaire', true)
                ->where('is_approved', true);
            })
            ->distinct()
            ->pluck('category')
            ->filter()
            ->values();

        $concessionaires = User::query()
            ->where('role', 'concessionaire')
            ->where('is_active_concessionaire', true)
            ->where('is_approved', true)
            ->withCount([
                'products as products_count' => function ($q) {
                    $q->available()->notDeleted();
                },
            ])
            ->orderByRaw('COALESCE(business_name, name) asc')
            ->get();

        $visibleStocks = UniformStock::query()
            ->where('is_visible', true)
            ->orderBy('item_name')
            ->get();

        return view('products.index', compact('products', 'categories', 'concessionaires', 'visibleStocks'));
    }

    /**
     * Display a single product with reviews.
     */
    public function show(Product $product)
    {
        $product->load('concessionaire');

        $reviews = ProductReview::with('user')
            ->where('product_id', $product->id)
            ->latest()
            ->paginate(10);

        // Check if current user has already reviewed
        $userReview = null;
        if (Auth::check()) {
            $userReview = ProductReview::where('product_id', $product->id)
                ->where('user_id', Auth::id())
                ->first();
        }

        // Get review statistics
        $totalReviews = ProductReview::where('product_id', $product->id)->count();
        $reviewStats = [
            'total' => $totalReviews,
            'average' => $product->average_rating,
            'distribution' => [
                5 => ProductReview::where('product_id', $product->id)->where('rating', 5)->count(),
                4 => ProductReview::where('product_id', $product->id)->where('rating', 4)->count(),
                3 => ProductReview::where('product_id', $product->id)->where('rating', 3)->count(),
                2 => ProductReview::where('product_id', $product->id)->where('rating', 2)->count(),
                1 => ProductReview::where('product_id', $product->id)->where('rating', 1)->count(),
            ],
        ];

        return view('products.show', compact('product', 'reviews', 'userReview', 'reviewStats'));
    }

    /**
     * Store a new review.
     */
    public function storeReview(Request $request, Product $product)
    {
        if (in_array(Auth::user()?->role, ['admin', 'cashier'], true)) {
            return back()->with('error', 'This account type cannot leave reviews.');
        }

        if (Auth::user()?->role === 'concessionaire') {
            abort(403);
        }

        if ((int) $product->concessionaire_id === (int) Auth::id()) {
            return back()->with('error', 'You cannot review your own products.');
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

        // Check if user already reviewed
        $existingReview = ProductReview::where('user_id', Auth::id())
            ->where('product_id', $product->id)
            ->first();

        if ($existingReview) {
            return back()->with('error', 'You have already reviewed this product.');
        }

        ProductReview::create([
            'user_id' => Auth::id(),
            'product_id' => $product->id,
            'rating' => $validated['rating'],
            'comment' => $validated['comment'] ?? null,
        ]);

        return back()->with('success', 'Your review has been submitted!');
    }

    /**
     * Update an existing review.
     */
    public function updateReview(Request $request, Product $product)
    {
        if (in_array(Auth::user()?->role, ['admin', 'cashier'], true)) {
            return back()->with('error', 'This account type cannot leave reviews.');
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

        $review = ProductReview::where('user_id', Auth::id())
            ->where('product_id', $product->id)
            ->firstOrFail();

        $review->update([
            'rating' => $validated['rating'],
            'comment' => $validated['comment'] ?? null,
        ]);

        return back()->with('success', 'Your review has been updated!');
    }

    /**
     * Delete a review.
     */
    public function deleteReview(Product $product)
    {
        if (in_array(Auth::user()?->role, ['admin', 'cashier'], true)) {
            return back()->with('error', 'This account type cannot leave reviews.');
        }

        $review = ProductReview::where('user_id', Auth::id())
            ->where('product_id', $product->id)
            ->firstOrFail();

        $review->delete();

        return back()->with('success', 'Your review has been deleted.');
    }
}
