<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MembershipTier;

class MembershipTierController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->authorize('viewAny', MembershipTier::class);
        $tiers = MembershipTier::orderBy('rank_priority', 'asc')->get();
        return view('admin.membership-tiers.index', compact('tiers'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->authorize('create', MembershipTier::class);
        return view('admin.membership-tiers.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->authorize('create', MembershipTier::class);
        
        $data = $request->validate([
            'name'          => 'required|unique:membership_tiers',
            'discount'      => 'nullable|numeric|min:0|max:100',
            'usage_limit'   => 'nullable|integer|min:0',
            'usage_period'  => 'nullable|in:week,month,year,lifetime',
            
            'rank_priority' => 'required|integer|unique:membership_tiers',
            'min_spent'     => 'nullable|numeric|min:0',
            'min_orders'    => 'nullable|integer|min:0',
            
            'color_hex'     => ['required', 'regex:/^#([a-f0-9]{6}|[a-f0-9]{3})$/i'],
            
            'validity_days' => 'nullable|integer|min:0'
        ]);

        MembershipTier::create($data);
        
        return redirect()->route('admin.membership-tiers.index')
            ->with('success', 'Thêm hạng thành viên thành công.');
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
    public function edit(MembershipTier $membershipTier)
    {
        $this->authorize('update', $membershipTier);
        return view('admin.membership-tiers.edit', compact('membershipTier'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, MembershipTier $membershipTier)
    {
        $this->authorize('update', $membershipTier);
        
        $data = $request->validate([
            'name'          => 'required|unique:membership_tiers,name,' . $membershipTier->id,
            'discount'      => 'nullable|numeric|min:0|max:100',
            'usage_limit'   => 'nullable|integer|min:0',
            'usage_period'  => 'nullable|in:week,month,year,lifetime',
            'rank_priority' => 'required|integer|unique:membership_tiers,rank_priority,' . $membershipTier->id,
            'min_spent'     => 'nullable|numeric|min:0',
            'min_orders'    => 'nullable|integer|min:0',
            
            'color_hex'     => ['required', 'regex:/^#([a-f0-9]{6}|[a-f0-9]{3})$/i'],
            
            'validity_days' => 'nullable|integer|min:0'
        ]);

        $membershipTier->update($data);
        
        return redirect()->route('admin.membership-tiers.index')
            ->with('success', 'Cập nhật hạng thành viên.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(MembershipTier $membershipTier)
    {
        $this->authorize('delete', $membershipTier);
        $membershipTier->delete();
        return redirect()->route('admin.membership-tiers.index')
            ->with('success', 'Xóa hạng thành viên.');
    }
}