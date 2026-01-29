<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Notifications\OrderStatusUpdated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function __construct()
    {
        // Authorization for model-based actions is handled via policies in each method.
    }

    /**
     * Display a listing of the resource.   
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Order::class);

        $orderCounts = Order::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        $stats = [
            'pending'   => $orderCounts['pending'] ?? 0,
            'confirmed' => $orderCounts['confirmed'] ?? 0,
            'shipping'  => $orderCounts['shipping'] ?? 0,
            'completed' => $orderCounts['completed'] ?? 0,
            'cancelled' => $orderCounts['cancelled'] ?? 0,
        ];

        $years = range(date('Y'), date('Y') - 4);

        $query = Order::with('user');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($sub) use ($search) {
                    $sub->where('name', 'like', "%$search%")
                        ->orWhere('email', 'like', "%$search%");
                })
                    ->orWhere('code', 'like', "%$search%")
                    ->orWhere('shipping_address', 'like', "%$search%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('month')) {
            $query->whereMonth('created_at', $request->month);

            $year = $request->get('year', date('Y'));
            $query->whereYear('created_at', $year);
        } elseif ($request->filled('year')) {
            $query->whereYear('created_at', $request->year);
        }

        $sortField = $request->get('sort_field', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');

        $perPage = $request->input('per_page', 10);

        $allowedPerPage = [10, 20, 50, 100];
        if (!in_array($perPage, $allowedPerPage)) {
            $perPage = 10;
        }
        $orders = $query->orderBy($sortField, $sortDirection)
            ->paginate($perPage)
            ->appends($request->all());

        return view('admin.orders.index', compact('orders', 'stats', 'sortField', 'sortDirection', 'years'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Order $order)
    {

        $order->load(['user', 'items.product', 'items.variant']);
        // dd($order->toArray(), $order->created_at);
        $this->authorize('viewAny', $order);

        return view('admin.orders.show', compact('order'));
    }

    /**
     * Update the specified resource in storage.
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateStatus(Request $request, Order $order)
    {
        $this->authorize('update', $order);

        $order->update(['status' => $request->status]);

        if ($order->user) {
            $order->user->notify(new OrderStatusUpdated($order));
        }

        return back()->with('success', 'Cập nhật trạng thái đơn hàng thành công.');
    }

    public function print(Order $order)
    {
        $order->load(['items.product', 'items.variant', 'user']);

        return view('admin.orders.print', compact('order'));
    }
}
