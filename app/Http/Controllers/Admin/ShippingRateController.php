<?php

namespace App\Http\Controllers\Admin;

use App\Models\ShippingRate;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ShippingRateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->authorize('viewAny', ShippingRate::class);
        $rates = ShippingRate::all();
        return view('admin.settings.shipping.index', compact('rates'));
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
     public function create()
    {
        $this->authorize('create', ShippingRate::class);
        return view('admin.settings.shipping.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
   public function store(Request $request)
    {
        $this->authorize('create', ShippingRate::class);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'fee' => 'required|numeric|min:0',
            'min_order_value' => 'nullable|numeric|min:0',
            'estimated_days' => 'nullable|integer|min:1',
        ]);

        $data = $request->all();
        $data['is_active'] = $request->has('is_active');
        
        ShippingRate::create($data);

        return redirect()->route('admin.settings.shipping.index')->with('success', 'Thêm mới phí vận chuyển.');
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
      public function edit(ShippingRate $shipping)
    {
        $this->authorize('update', $shipping);
        return view('admin.settings.shipping.edit', compact('shipping'));
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
      public function update(Request $request, ShippingRate $shipping)
    {
        $this->authorize('update', $shipping);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'fee' => 'required|numeric|min:0',
        ]);

        $data = $request->all();
        $data['is_active'] = $request->has('is_active');
        
        $shipping->update($data);

        return redirect()->route('admin.settings.shipping.index')->with('success', 'Cập nhật phí vận chuyển.');
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
      public function destroy(ShippingRate $shipping)
    {
        $this->authorize('delete', $shipping);
        $shipping->delete();
        return back()->with('success', 'Xóa phí vận chuyển.');
    }
}
