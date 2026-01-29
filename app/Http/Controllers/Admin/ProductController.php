<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use App\Http\Requests\ProductRequest;
use Illuminate\Support\Facades\Storage;
use App\Models\Promotion;
use App\Models\Tag;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function __construct() {}

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Product::class);

        $categories = Category::where('type', 'product')->get();
        $tags = Tag::all();

        $query = Product::withCount(['variants', 'images'])->with('category');

        if ($request->filled('keyword')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->keyword . '%')
                    ->orWhere('sku', 'like', '%' . $request->keyword . '%');
            });
        }

        if ($request->filled('status')) {
            if ($request->status == '1') {
                $query->where('status', true);
            } elseif ($request->status == '0') {
                $query->where('status', false);
            }
        }

        if ($request->filled('sort')) {
            switch ($request->sort) {
                case 'name_asc':
                    $query->orderBy('name', 'asc');
                    break;

                case 'name_desc':
                    $query->orderBy('name', 'desc');
                    break;

                case 'created_asc':
                    $query->orderBy('created_at', 'asc');
                    break;

                case 'price_asc':

                    $query->orderBy(
                        ProductVariant::select('sale_price')
                            ->whereColumn('product_id', 'products.id')
                            ->orderBy('sale_price', 'desc')
                            ->limit(1),
                        'asc'
                    );
                    break;

                case 'price_desc':
                    $query->orderBy(
                        ProductVariant::select('sale_price')
                            ->whereColumn('product_id', 'products.id')
                            ->orderBy('sale_price', 'desc')
                            ->limit(1),
                        'desc'
                    );
                    break;

                case 'discount_desc':
                    $query->orderBy(
                        ProductVariant::select('discount_percent')
                            ->whereColumn('product_id', 'products.id')
                            ->orderBy('discount_percent', 'desc')
                            ->limit(1),
                        'desc'
                    );
                    break;
                case 'created_desc':
                default:
                    $query->orderBy('created_at', 'desc');
                    break;
            }
        } else {
            $query->orderBy('id', 'desc');
        }

        $perPage = $request->input('per_page', 10);

        $allowedPerPage = [10, 20, 50, 100];
        if (!in_array($perPage, $allowedPerPage)) {
            $perPage = 10;
        }
        $products = $query->paginate($perPage)->appends($request->all());

        return view('admin.products.index', compact('products', 'categories', 'tags'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('create', Product::class);
        $categories = Category::all();
        $tags = Tag::all();

        return view('admin.products.create', compact('categories', 'tags'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ProductRequest $request)
    {
        $this->authorize('create', Product::class);

        $data = $request->all();

        $data['views'] = 0;
        $data['status'] = $request->has('status') ? 1 : 0;

        $data['discount_percent'] = $request->input('discount_percent', 0);

        $product = Product::create($data);

        $rawTags = $request->input('tags');
        $tagNames = [];

        if ($rawTags) {
            $decoded = json_decode($rawTags, true);

            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $tagNames = array_column($decoded, 'value');
            } else {
                $tagNames = is_array($rawTags) ? $rawTags : [$rawTags];
            }
        }

        $this->syncTags($product, $tagNames);

        return redirect()->route('admin.products.index')->with('success', 'Thêm mới sản phẩm.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        $this->authorize('view', $product);

        $categories = Category::where('type', 'product')->get();
        $tags = Tag::all();
        return view('admin.products.show', compact('product', 'categories', 'tags'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product)
    {
        $this->authorize('update', $product);
        $categories = Category::all();
        $tags = Tag::all();
        return view('admin.products.edit', compact('product', 'categories', 'tags'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ProductRequest $request, Product $product)
    {
        $this->authorize('update', $product);

        $data = $request->all();
        $data['status'] = $request->input('status', 0);
        $data['discount_percent'] = $request->input('discount_percent', 0);

        $product->update($data);

        $rawTags = $request->input('tags');
        $tagNames = [];

        if ($rawTags) {
            $decoded = json_decode($rawTags, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $tagNames = array_column($decoded, 'value');
            } else {
                $tagNames = is_array($rawTags) ? $rawTags : [$rawTags];
            }
        }

        $this->syncTags($product, $tagNames);

        return back()->with('success', 'Cập nhật sản phẩm.');
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        $this->authorize('delete', $product);

        foreach ($product->images as $galleryImage) {
            if (Storage::disk('public')->exists($galleryImage->image_url)) {
                Storage::disk('public')->delete($galleryImage->image_url);
            }
        }

        if ($product->image && Storage::disk('public')->exists($product->image)) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success', 'Xóa sản phẩm và toàn bộ hình ảnh liên quan.');
    }

    private function syncTags($product, $tagsInput)
    {
        if (!empty($tagsInput)) {
            $tagNames = is_string($tagsInput) ? explode(',', $tagsInput) : $tagsInput;
            $tagIds = [];

            foreach ($tagNames as $tagName) {
                $name = trim($tagName);
                if ($name === '') continue;

                $tag = Tag::firstOrCreate(
                    ['name' => $name],
                    ['slug' => Str::slug($name) . '-' . time()]
                );

                $tagIds[] = $tag->id;
            }

            $product->tags()->sync($tagIds);
        } else {
            $product->tags()->detach();
        }
    }
}
