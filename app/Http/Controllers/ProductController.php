<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Banner;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Product::where('status', 1)
            ->with(['category', 'images', 'variants']);

        if ($request->has('category')) {
            $categorySlug = $request->category;
            $query->whereHas('category', function ($q) use ($categorySlug) {
                $q->where('slug', $categorySlug);
            });
        }

        if ($request->has('tag')) {
            $tagSlug = $request->tag;
            $query->whereHas('tags', function ($q) use ($tagSlug) {
                $q->where('slug', $tagSlug);
            });
        }

        if ($request->filled('min_price')) {
            $query->whereHas('variants', function ($q) use ($request) {
                $q->where('sale_price', '>=', $request->min_price);
            });
        }
        if ($request->filled('max_price')) {
            $query->whereHas('variants', function ($q) use ($request) {
                $q->where('sale_price', '<=', $request->max_price);
            });
        }

        $sort = $request->sort ?? 'default';

        $minPriceSubquery = \App\Models\ProductVariant::select('sale_price')
            ->whereColumn('product_id', 'products.id')
            ->orderBy('sale_price', 'asc')
            ->limit(1);

        switch ($sort) {
            case 'popularity':
                $query->orderBy('views', 'desc');
                break;
            case 'rating':
                $query->withCount([
                    'reviews as average_rating' => function ($q) {
                        $q->select(DB::raw('coalesce(avg(rating),0)'));
                    }
                ])->orderByDesc('average_rating');
                break;
            case 'date':
            case 'newest':
                $query->orderBy('created_at', 'desc');
                break;
            case 'price_asc':
                $query->orderBy($minPriceSubquery, 'asc');
                break;
            case 'price_desc':
                $query->orderBy($minPriceSubquery, 'desc');
                break;
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        $products = $query->paginate(12)->appends($request->all());

        $categories = Category::where('type', 'product')
            ->withCount([
                'products' => function ($q) {
                    $q->where('status', 1);
                }
            ])
            ->get();

        $tags = \App\Models\Tag::has('products')->take(10)->get();

        return view('products.index', compact('products', 'categories', 'tags'));
    }

    /**
     * Display the home page with products and filters.
     *
     * @return \Illuminate\Http\Response
     */
    public function home(Request $request)
    {
        $query = Product::with(['images' => fn($q) => $q->orderByDesc('is_primary')]);
        $categoryName = 'Khám phá sản phẩm';

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
            $category = Category::find($request->category_id);
            if ($category)
                $categoryName = $category->name;
        }

        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        switch ($request->input('sort')) {
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'discount':
                $query->orderByRaw('(price - promotion_price) DESC');
                break;
            case 'rating':
                $query->withAvg('reviews', 'rating')->orderByDesc('reviews_avg_rating');
                break;
            default:
                $query->inRandomOrder();
                break;
        }

        $products = $query->get();


        $products->map(function ($product) {
            $primaryImage = $product->images->firstWhere('is_primary', true);
            $product->primary_image = $primaryImage ? $primaryImage->image_url : null;
            return $product;
        });

        $categories = Category::all();
        $banners = Banner::where('status', true)->get();

        $newProducts = Product::with(['images' => fn($q) => $q->orderByDesc('is_primary')])
            ->orderBy('created_at', 'desc')->take(8)->get();
        $newProducts->map(function ($p) {
            $primary = $p->images->firstWhere('is_primary', true);
            $p->primary_image = $primary ? $primary->image_url : null;
            return $p;
        });
        // Sản phẩm bán chạy
        $bestSellerProducts = Product::withSum('completedOrderItems', 'quantity')
            ->with(['images' => fn($q) => $q->orderByDesc('is_primary')])
            ->orderByDesc('completed_order_items_sum_quantity')
            ->take(8)
            ->get();
        $bestSellerProducts->map(function ($p) {
            $primary = $p->images->firstWhere('is_primary', true);
            $p->primary_image = $primary ? $primary->image_url : null;
            return $p;
        });


        return view('home', compact(
            'products',
            'categories',
            'banners',
            'categoryName',
            'newProducts',
            'bestSellerProducts'
        ));
    }

    // public function show(Product $product)
    // {
    //     $reviews = $product->orderItems->flatMap(fn($item) => $item->reviews);
    //     if (request('star')) {
    //         $star = (int) request('star');
    //         $reviews = $reviews->filter(fn($r) => $r->rating == $star);
    //     }
    //     $averageRating = $reviews->avg('rating') ?? 0;
    //     return view('products.show', compact('product', 'reviews', 'averageRating'));
    // }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @param  \Illuminate\Http\Request  $request
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product, Request $request)
    {
        $product->load([
            'images' => function ($query) {
                $query->orderByDesc('is_primary');
            }
        ]);
        $images = $product->images;

        $averageRating = $product->reviews()->avg('rating');

        $counts = $product->reviews()
            ->selectRaw('rating, COUNT(*) as total')
            ->groupBy('rating')
            ->pluck('total', 'rating')
            ->toArray();

        $reviewsQuery = $product->reviews()->with('orderItem.order.user');

        if ($request->filled('star')) {
            $reviewsQuery->where('rating', $request->star);
        }

        $reviews = $reviewsQuery->latest()->paginate(5);

        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('status', 1)
            ->inRandomOrder()
            ->take(4)
            ->get();

        return view('products.show', compact('product', 'reviews', 'averageRating', 'counts', 'images', 'relatedProducts'));
    }


    /**
     * Search for products by name.
     *
     * @param  \Illuminate\Http\Request  $request
     *
     */
    public function search(Request $request)
    {
        $query = $request->input('query');

        if (empty($query)) {
            return redirect()->back()->with('error', 'Vui lòng nhập từ khóa tìm kiếm.');
        }

        $products = \App\Models\Product::where('name', 'like', "%{$query}%")->get();
        return view('products.search', compact('products', 'query'));
    }

    /**
     * Provide product suggestions for autocomplete.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function suggestions(Request $request)
    {
        $query = $request->input('query');

        if (!$query) {
            return response()->json([]);
        }

        $products = Product::where('name', 'LIKE', "%{$query}%")
            // ->where('status', 1) 
            ->with(['primaryImage', 'images'])
            ->select('id', 'name', 'slug')
            ->take(7)
            ->get();

        return response()->json($products);
    }
}
