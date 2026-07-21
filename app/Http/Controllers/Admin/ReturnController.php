<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OrderReturn;
use App\Services\InventoryService;
use App\Notifications\ReturnStatusUpdated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ReturnController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', OrderReturn::class);

        $returnCounts = OrderReturn::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        $stats = [
            'total'    => OrderReturn::count(),
            'pending'  => $returnCounts['pending'] ?? 0,
            'approved' => $returnCounts['approved'] ?? 0,
            'rejected' => $returnCounts['rejected'] ?? 0,
        ];

        $years = OrderReturn::selectRaw('YEAR(created_at) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        if ($years->isEmpty()) {
            $years = [date('Y')];
        }

        $query = OrderReturn::with(['order', 'user'])->latest();

        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $query->where(function ($q) use ($keyword) {
                $q->whereHas('order', function ($sub) use ($keyword) {
                    $sub->where('code', 'like', "%{$keyword}%");
                })
                    ->orWhereHas('user', function ($sub) use ($keyword) {
                        $sub->where('name', 'like', "%{$keyword}%")
                            ->orWhere('email', 'like', "%{$keyword}%");
                    })
                    ->orWhere('id', $keyword);
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('month')) {
            $query->whereMonth('created_at', $request->month);
        }

        if ($request->filled('year')) {
            $query->whereYear('created_at', $request->year);
        }

        
        $perPage = $request->input('per_page', 10);

        $allowedPerPage = [10, 20, 50, 100];
        if (!in_array($perPage, $allowedPerPage)) {
            $perPage = 10;
        }
        
        $returns = $query->paginate($perPage)->appends($request->all());

        return view('admin.returns.index', compact('returns', 'stats', 'years'));
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(OrderReturn $return)
    {
        $return->load(['items.orderItem.product', 'items.orderItem.variant.attributeValues', 'order', 'user']);


        $this->authorize('view', $return);

        return view('admin.returns.show', compact('return'));
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
    public function update(Request $request, OrderReturn $return)
    {
        $this->authorize('update', $return);

        $request->validate([
            'action' => 'required|in:approve,reject',
            'admin_note' => 'nullable|string|max:500'
        ]);

        if ($return->status !== 'pending') {
            return back()->with('error', 'Yêu cầu này đã được xử lý trước đó.');
        }

        DB::beginTransaction();
        try {
            if ($request->action === 'approve') {
                $this->handleApprove($return, $request->admin_note);
            } elseif ($request->action === 'reject') {
                $this->handleReject($return, $request->admin_note);
            }

            if ($return->user) {
                $return->user->notify(new ReturnStatusUpdated($return));
            }

            DB::commit();
            return back()->with('success', 'Đã xử lý yêu cầu hoàn hàng.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }


    private function handleApprove(OrderReturn $return, $note)
    {
        $return->update([
            'status' => 'approved',
            'admin_note' => $note,
            'processed_by' => Auth::id()
        ]);

        foreach ($return->items as $returnItem) {
            $orderItem = $returnItem->orderItem;

            if ($orderItem->product_variant_id) {
                InventoryService::log(
                    $orderItem->product_variant_id,
                    $returnItem->quantity,
                    'in',
                    "Hoàn hàng đơn #{$return->order->code} (Phiếu hoàn #{$return->id})",
                    $return
                );
            } else {
                if ($orderItem->product) {
                    $orderItem->product->increment('quantity', $returnItem->quantity);
                }
            }

            if ($orderItem->product) {
                $orderItem->product->decrement('sold_count', $returnItem->quantity);
            }

            if ($orderItem->product_variant_id) {
                DB::table('product_variants')
                    ->where('id', $orderItem->product_variant_id)
                    ->decrement('sold_count', $returnItem->quantity);
            }
        }
    }


    private function handleReject(OrderReturn $return, $note)
    {
        $return->update([
            'status' => 'rejected',
            'admin_note' => $note,
            'processed_by' => Auth::id()
        ]);
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
