<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductImageRequest;
use App\Models\ProductImage;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Services\ImageUploadService;

class ProductImageController extends Controller
{
    public function __construct(protected ImageUploadService $imageService) {}
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', ProductImage::class);

        $totalImages = ProductImage::count();
        $productsWithImages = Product::has('images')->count();
        $primaryImagesCount = ProductImage::where('is_primary', 1)->count();
        $secondaryImagesCount = $totalImages - $primaryImagesCount;

        $categories = Category::where('type', 'product')->get();

        $query = Product::with(['images' => function ($q) {
            $q->orderBy('is_primary', 'desc')->orderBy('created_at', 'desc');
        }]);

        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                    ->orWhere('sku', 'like', "%{$keyword}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('has_images')) {
            if ($request->has_images == 'yes') {
                $query->has('images');
            } elseif ($request->has_images == 'no') {
                $query->doesntHave('images');
            }
        }

        
        $perPage = $request->input('per_page', 10);

        $allowedPerPage = [10, 20, 50, 100];
        if (!in_array($perPage, $allowedPerPage)) {
            $perPage = 10;
        }
        
        $products = $query->paginate($perPage)->appends($request->all());

        return view('admin.product_images.index', compact(
            'products',
            'categories',
            'totalImages',
            'productsWithImages',
            'primaryImagesCount',
            'secondaryImagesCount'
        ));
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Product $product)
    {
        $this->authorize('create', ProductImage::class);
        return view('admin.product_images.create', compact('product'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ProductImageRequest $request, Product $product)
    {
        $this->authorize('create', ProductImage::class);
        $maxImages = 10;
        $currentImagesCount = $product->images()->count(); 
        $newImagesCount = $request->hasFile('images') ? count($request->file('images')) : 0; 

        if (($currentImagesCount + $newImagesCount) > $maxImages) {
            $remaining = max(0, $maxImages - $currentImagesCount);

            $message = $remaining > 0
                ? "Sản phẩm đã có $currentImagesCount ảnh. Bạn chỉ được tải thêm tối đa $remaining ảnh nữa."
                : "Sản phẩm đã đạt giới hạn $maxImages ảnh. Vui lòng xóa bớt ảnh cũ trước khi thêm mới.";

            return redirect()->back()
                ->withErrors(['images' => $message]) 
                ->withInput(); 
        }
        if ($request->hasFile('images')) {

            foreach ($request->file('images') as $index => $file) {

                $fullPath = $this->imageService->upload($file, 'product_images', 1000);

                $isPrimary = false;

                if ($product->images()->count() === 0 && $index === 0) {
                    $isPrimary = true;
                } elseif ($request->is_primary && $index === 0) {
                    $product->images()->update(['is_primary' => false]);
                    $isPrimary = true;
                }

                $product->images()->create([
                    'image_url' => $fullPath,
                    'is_primary' => $isPrimary,
                ]);
            }

            $count = count($request->file('images'));
            return redirect()->route('admin.product_images.index', ['keyword' => $product->sku])
                ->with('success', "Đã tải lên $count ảnh.");
        }

        return redirect()->back()->with('error', 'Vui lòng chọn ít nhất một ảnh.');
    }

    /**
     *  Display the specified resource.
     *
     * @param  int  $productId
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        $this->authorize('viewAny', ProductImage::class);
        $product->load('images');

        return view('admin.product_images.show', compact('product'));
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product, ProductImage $productImage)
    {
        $this->authorize('update', $productImage);

        if ($productImage->product_id !== $product->id) {
            abort(404);
        }

        return view('admin.product_images.edit', [
            'product' => $product,
            'image' => $productImage
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ProductImageRequest $request, Product $product, ProductImage $productImage)
    {
        $this->authorize('update', $productImage);

        if ($request->hasFile('image')) {
            $productImage->image_url = $this->imageService->replace(
                $request->file('image'),
                $productImage->image_url,
                'product_images'
            );
        }

        if ($request->has('is_primary')) {

            ProductImage::where('product_id', $product->id)
                ->where('id', '!=', $productImage->id)
                ->update(['is_primary' => false]);

            $productImage->is_primary = true;
        } else {
            $productImage->is_primary = false;
        }

        $productImage->save();

        return redirect()->route('admin.product_images.index', $product)
            ->with('success', 'Cập nhật ảnh sản phẩm.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product, ProductImage $productImage)
    {
        $this->authorize('delete', $productImage);

        $isPrimary = $productImage->is_primary;

        $this->imageService->delete($productImage->image_url);

        $productImage->delete();

        if ($isPrimary) {
            $nextPrimaryImage = ProductImage::where('product_id', $product->id)
                ->orderBy('id', 'asc')
                ->first();

            if ($nextPrimaryImage) {
                $nextPrimaryImage->is_primary = 1;
                $nextPrimaryImage->save();
            }
        }

        return redirect()->route('admin.product_images.index',  ['keyword' => $product->sku])
            ->with('success', 'Xóa ảnh sản phẩm.');
    }


    /**
     * Remove the selected resources from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $productId
     * @return \Illuminate\Http\Response
     */
    public function destroySelected(Request $request, Product $product)
    {
        $this->authorize('delete', ProductImage::class);

        $request->validate([
            'ids' => 'required|array',
        ]);

        $images = ProductImage::whereIn('id', $request->ids)
            ->where('product_id', $product->id)
            ->get();

        foreach ($images as $image) {
            $this->imageService->delete($image->image_url);
            $image->delete();
        }

        return redirect()->route('admin.product_images.index',  ['keyword' => $product->sku])
            ->with('success', 'Xóa các ảnh đã chọn.');
    }


    /**
     * Remove all images of a product from storage.
     * @param  int  $productId
     * @return \Illuminate\Http\Response
     */
    public function destroyAll(Product $product)
    {
        $this->authorize('delete', ProductImage::class);

        foreach ($product->images as $image) {
            $this->imageService->delete($image->image_url);
            $image->delete();
        }

        return back()->with('success', ['keyword' => $product->sku], 'Xóa tất cả ảnh của sản phẩm.');
    }
}
