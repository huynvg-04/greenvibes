<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductVariant;
use App\Models\Order;
use App\Models\OrderReturn;
use Illuminate\Http\Request;
use App\services\InventoryService;
use App\Models\WarehouseTransaction;

class WarehouseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', WarehouseTransaction::class);

        $query = WarehouseTransaction::with(['variant.product', 'user', 'reference'])->latest();
    
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $query->whereHas('variant', function ($q) use ($keyword) {
                $q->where('sku', 'like', "%$keyword%")
                    ->orWhere('name', 'like', "%$keyword%")
                    ->orWhereHas('product', function ($sq) use ($keyword) {
                        $sq->where('name', 'like', "%$keyword%");
                    });
            });
        }

        if ($request->filled('source')) {
            if ($request->source == 'order') {
                $query->where('reference_type', Order::class);
            } elseif ($request->source == 'return') {
                $query->where('reference_type', OrderReturn::class);
            } elseif ($request->source == 'manual') {
                $query->whereNull('reference_type');
            }
        }

        if ($request->filled('month')) {
            $query->whereMonth('created_at', $request->month);
        }

        if ($request->filled('year')) {
            $query->whereYear('created_at', $request->year);
        }

        $years = WarehouseTransaction::selectRaw('YEAR(created_at) as year')
            ->distinct()->orderBy('year', 'desc')->pluck('year');
        if ($years->isEmpty()) $years = [date('Y')];

        $perPage = $request->input('per_page', 10);
        $allowedPerPage = [10, 20, 50, 100];

        if (!in_array($perPage, $allowedPerPage)) {
            $perPage = 10;
        }

        $transactions = $query->paginate($perPage)->appends($request->all());

        $variants = ProductVariant::with('product')->orderBy('stock', 'asc')->get();
 
        return view('admin.warehouse.index', compact('transactions', 'years', 'variants'));
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('create', WarehouseTransaction::class);
        // Load variants kèm product để hiển thị tên đầy đủ
        $variants = ProductVariant::with('product')->get();
        return view('admin.warehouse.create', compact('variants'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->authorize('create', WarehouseTransaction::class);

        $request->validate([
            'product_variant_id' => 'required|exists:product_variants,id',
            'type' => 'required|in:in,out',
            'stock' => 'required|integer|min:1',
            'description' => 'required|string|max:255',
        ], [
            'product_variant_id.required' => 'Vui lòng chọn sản phẩm.',
            'product_variant_id.exists' => 'Sản phẩm không tồn tại.',
            'type.required' => 'Vui lòng chọn loại thao tác.',
            'type.in' => 'Loại thao tác không hợp lệ.',
            'stock.required' => 'Vui lòng nhập số lượng.',
            'stock.integer' => 'Số lượng phải là số nguyên.',
            'stock.min' => 'Số lượng phải lớn hơn 0.',
            'description.required' => 'Vui lòng nhập mô tả.',
            'description.string' => 'Mô tả phải là chuỗi.',
            'description.max' => 'Mô tả không được quá 255 ký tự.',
        ]);

        try {
            InventoryService::log(
                $request->product_variant_id,
                $request->stock,
                $request->type,
                $request->description ?? 'Điều chỉnh kho thủ công',
                null 
            );

            return redirect()->route('admin.warehouse.index')->with('success', 'Điều chỉnh tồn kho thủ công');
        } catch (\Exception $e) {
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
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
