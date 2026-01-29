<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Coupon::class);

        $totalCoupons = Coupon::count();

        $activeCondition = function ($q) {
            $q->where(function ($sub) {
                $sub->whereNull('end_date')->orWhere('end_date', '>=', now());
            })->where(function ($sub) {
                $sub->whereNull('usage_limit')->orWhereColumn('used_count', '<', 'usage_limit');
            });
        };

        $expiredCondition = function ($q) {
            $q->where(function ($sub) {
                $sub->where('end_date', '<', now())
                    ->orWhereColumn('used_count', '>=', 'usage_limit');
            });
        };

        $activeCount = Coupon::where($activeCondition)->count();
        $expiredCount = Coupon::where($expiredCondition)->count();

        $query = Coupon::query()->latest('id');

        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $query->where(function ($q) use ($keyword) {
                $q->where('code', 'like', "%{$keyword}%")
                  ->orWhere('name', 'like', "%{$keyword}%");
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            if ($request->status == 'active') {
                $query->where($activeCondition);
            } elseif ($request->status == 'expired') {
                $query->where($expiredCondition);
            }
        }

        $perPage = $request->input('per_page', 10);

        $allowedPerPage = [10, 20, 50, 100];
        if (!in_array($perPage, $allowedPerPage)) {
            $perPage = 10;
        }
    
        $coupons = $query->paginate($perPage)->appends($request->all());

        return view('admin.coupons.index', compact('coupons', 'activeCount', 'expiredCount','totalCoupons'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('create', Coupon::class);

        return view('admin.coupons.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->authorize('create', Coupon::class);


        $request->validate([
            'code' => 'required|string|unique:coupons,code|max:50',
            'type' => 'required|in:fixed,percent',
            'value' => 'required|numeric|min:0',
            'min_order_value' => 'nullable|numeric|min:0',
            'max_discount_value' => 'nullable|numeric|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $data = $request->all();
        $data['code'] = strtoupper($request->code);
        $data['is_active'] = $request->has('is_active');

        $data['min_order_value'] = $request->min_order_value ?? 0;
        $data['max_discount_value'] = $request->max_discount_value ?? null;

        $data['scope'] = 'global';

        Coupon::create($data);

        return redirect()->route('admin.coupons.index')->with('success', 'Thêm mới mã giảm giá.');
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
    public function edit(Coupon $coupon)
    {
        $this->authorize('update', $coupon);

        return view('admin.coupons.edit', compact('coupon'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Coupon $coupon)
    {
        $this->authorize('update', $coupon);

        $request->validate([
            'code' => 'required|string|max:50|unique:coupons,code,' . $coupon->id,
            'type' => 'required|in:fixed,percent',
            'value' => 'required|numeric|min:0',
            'min_order_value' => 'nullable|numeric|min:0',
            'max_discount_value' => 'nullable|numeric|min:0',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $data = $request->all();
        $data['code'] = strtoupper($request->code);
        $data['is_active'] = $request->has('is_active');
        $data['min_order_value'] = $request->min_order_value ?? 0;
        $data['max_discount_value'] = $request->max_discount_value ?? null;
        $data['scope'] = 'global';

        $coupon->update($data);


        return redirect()->route('admin.coupons.index')->with('success', 'Cập nhật mã giảm giá.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Coupon $coupon)
    {
        $this->authorize('delete', $coupon);
        $coupon->delete();
        return redirect()->route('admin.coupons.index')->with('success', 'Xóa mã giảm giá.');
    }
}
