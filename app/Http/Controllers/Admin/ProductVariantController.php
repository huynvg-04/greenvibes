<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Attribute;
use App\Http\Requests\StoreProductVariantRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductVariantController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', ProductVariant::class);

        $attributes = Attribute::with('values')->get();
        $query = Product::with('variants.attributeValues.attribute')->latest();

        // 1. LỌC THEO TỪ KHÓA (Giữ nguyên code cũ)
        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                    ->orWhere('sku', 'like', "%{$keyword}%")
                    ->orWhereHas('variants', function ($qVariant) use ($keyword) {
                        $qVariant->where('sku', 'like', "%{$keyword}%")
                            ->orWhere('name', 'like', "%{$keyword}%");
                    });
            });
        }

        // 2. [MỚI] LỌC THEO TRẠNG THÁI BIẾN THỂ
        if ($request->filled('variant_status')) {
            if ($request->variant_status == 'has_variants') {
                // Lấy SP ĐÃ CÓ biến thể
                $query->has('variants');
            } elseif ($request->variant_status == 'no_variants') {
                // Lấy SP CHƯA CÓ biến thể
                $query->doesntHave('variants');
            }
        }

        $perPage = $request->input('per_page', 10);
        $allowedPerPage = [10, 20, 50, 100];
        if (!in_array($perPage, $allowedPerPage)) {
            $perPage = 10;
        }

        $products = $query->paginate($perPage)->appends($request->all());

        return view('admin.product_variants.index', compact('products', 'attributes'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Product $product)
    {
        $this->authorize('create', ProductVariant::class);

        $attributes = Attribute::with('values')->get();

        return view('admin.product_variants.create', compact('product', 'attributes'));
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreProductVariantRequest $request, Product $product)
    {
        $this->authorize('create', ProductVariant::class);

        $validated = $request->validated();

        try {
            DB::beginTransaction();

            $variant = $product->variants()->create([
                'sku'           => $validated['sku'],
                'name'          => $request->name ?? ($product->name . ' - ' . $validated['sku']),
                'stock'         => 0,
                'standard_cost' => $validated['standard_cost'],
                'list_price'    => $validated['list_price'],
            ]);

            if (!empty($validated['attributes'])) {
                $syncData = array_filter($validated['attributes'], function ($value) {
                    return !is_null($value) && $value !== '';
                });

                if (!empty($syncData)) {
                    $variant->attributeValues()->sync($syncData);
                }
            }

            DB::commit();

            return back()->with('success', 'Thêm mới phân loại sản phẩm.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(ProductVariant $productVariant)
    {
        $this->authorize('update', $productVariant);

        $productVariant->load('attributeValues', 'product');

        $product = $productVariant->product;
        $attributes = Attribute::with('values')->get();
        $currentAttributeValues = $productVariant->attributeValues->pluck('id')->toArray();

        return view('admin.product_variants.edit', [
            'variant' => $productVariant,
            'product' => $product,
            'attributes' => $attributes,
            'currentAttributeValues' => $currentAttributeValues
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ProductVariant $productVariant)
    {
        $this->authorize('update', $productVariant);

        $request->validate([
            'sku' => 'required|string|unique:product_variants,sku,' . $productVariant->id,
            'standard_cost' => 'required|numeric|min:1',
            'list_price' => 'required|numeric|gte:standard_cost',
            'attributes' => 'required|array',
        ], [
            'sku.unique' => 'SKU đã tồn tại.',
            'sku.required' => 'Vui lòng nhập SKU.',
            'sku.string' => 'SKU phải là chuỗi.',
            'standard_cost.min' => 'Giá nhập phải lớn hơn 0.',
            'standard_cost.required' => 'Vui lòng nhập giá nhập.',
            'list_price.required' => 'Vui lòng nhập giá niêm yết.',
            'list_price.gte' => 'Giá niêm yết phải lớn hơn hoặc bằng giá nhập.',
        ]);

        try {
            DB::beginTransaction();

            $productVariant->update([
                'sku' => $request->sku,
                'standard_cost' => $request->standard_cost,
                'list_price' => $request->list_price,
            ]);

            if ($request->has('attributes')) {
                $newAttributeValues = array_filter($request->input('attributes', []));
                $productVariant->attributeValues()->sync($newAttributeValues);
            }

            DB::commit();

            return back()->with('success', 'Cập nhật phân loại sản phẩm.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(ProductVariant $productVariant)
    {
        $this->authorize('delete', $productVariant);

        $productVariant->delete();

        return back()->with('success', 'Xóa phân loại sản phẩm.');
    }
}
