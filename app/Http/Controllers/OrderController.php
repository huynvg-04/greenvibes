<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Cart;
use App\Models\Review;
use App\Models\Product;
use App\Models\OrderReturn;
use App\Models\OrderReturnItem;
use Illuminate\Support\Facades\Notification;
use App\Services\InventoryService;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;
use App\Notifications\NewReturnRequestNotification;

class OrderController extends Controller
{
    /**
     * Display a listing of the user's orders with filtering and searching.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->authorize('viewCustomer', Order::class);

        $user = Auth::user();

        $profile = $user->customerProfile;
        if ($profile) {
            $profile->load('tier');
        }

        $currentTier = $profile ? $profile->tier : null;

        $currentRank = $currentTier ? ($currentTier->rank_priority ?? 0) : 0;

        $nextTier = \App\Models\MembershipTier::where('rank_priority', '>', $currentRank)
            ->orderBy('rank_priority', 'asc')
            ->first();

        $query = Order::with('items')->where('user_id', Auth::id());

        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $query->where(function ($q) use ($keyword) {
                $q->where('code', 'LIKE', "%{$keyword}%")
                    ->orWhereHas('items', function ($sub) use ($keyword) {
                        $sub->whereHas('product', function ($p) use ($keyword) {
                            $p->where('name', 'LIKE', "%{$keyword}%");
                        });
                    });
            });
        }

        if ($request->filled('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }

        if ($request->filled('price_min')) {
            $query->where('total_amount', '>=', (float) $request->price_min);
        }
        if ($request->filled('price_max')) {
            $query->where('total_amount', '<=', (float) $request->price_max);
        }

        $orders = $query->latest()->paginate(10)->appends($request->query());

        return view('user.orders.index', compact('orders', 'profile', 'currentTier', 'nextTier'));
    }

    /**
     * Show the form for creating a new order.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }


    public function store(Request $request)
    {
        //
    }

    /**
     * Cancel the specified order.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function cancel($id)
    {
        $order = Order::with(['items.product', 'items.variant'])->findOrFail($id);

        $this->authorize('cancel', $order);

        if ($order->status === 'cancelled') {
            return back()->with('error', 'Đơn hàng này đã được hủy trước đó.');
        }

        if ($order->status !== 'pending' && $order->status !== 'waiting_payment') {
            return back()->with('error', 'Không thể hủy đơn hàng đang giao hoặc đã hoàn thành.');
        }

        DB::beginTransaction();
        try {
            $order->status = 'cancelled';
            $order->save();

            foreach ($order->items as $item) {
                if ($item->product_variant_id) {
                    InventoryService::log(
                        $item->product_variant_id,
                        $item->quantity,
                        'in',
                        "Khách hủy đơn hàng #{$order->code}",
                        $order
                    );
                } else {
                    if ($item->product) {
                        $item->product->increment('quantity', $item->quantity);
                    }
                }
                if ($item->product) {
                    $item->product->decrement('sold_count', $item->quantity);
                }

                if ($item->product_variant_id) {
                    DB::table('product_variants')
                        ->where('id', $item->product_variant_id)
                        ->decrement('sold_count', $item->quantity);
                }
            }



            DB::commit();
            return back()->with('success', 'Hủy đơn hàng thành công.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Lỗi hệ thống: ' . $e->getMessage());
        }
    }

    public function complete(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        if (!in_array($order->status, ['shipping', 'waiting_payment'])) {
            return back()->with('error', 'Trạng thái đơn hàng không hợp lệ để xác nhận.');
        }

        $order->status = 'completed';

        $order->save();

        return back()->with('success', 'Cảm ơn bạn đã mua hàng! Đơn hàng đã hoàn tất.');
    }

    public function returnForm(Order $order)
    {
        if ($order->user_id !== Auth::id())
            abort(403);

        if ($order->status !== 'completed') {
            return back()->with('error', 'Đơn hàng chưa hoàn thành, không thể yêu cầu hoàn hàng.');
        }

        if ($order->returns()->where('status', 'pending')->exists()) {
            return back()->with('error', 'Đơn hàng này đang có yêu cầu hoàn hàng chờ xử lý.');
        }

        return view('user.orders.return', compact('order'));
    }

    /**
     * Xử lý lưu phiếu hoàn hàng
     */
    public function storeReturn(Request $request, Order $order)
    {

        if ($order->user_id !== Auth::id())
            abort(403);

        $request->validate([
            'reason' => 'required|string|max:255',
            'description' => 'nullable|string',
            'items' => 'required|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:10240'
        ], [
            'items.required' => 'Vui lòng chọn ít nhất một sản phẩm để trả lại.',
            'reason.required' => 'Vui lòng chọn lý do hoàn hàng.',
        ]);

        DB::beginTransaction();
        try {
            $imagePaths = [];
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $file) {
                    $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                    $path = 'returns/' . $filename;

                    try {
                        $image = Image::read($file);
                        $image->scale(width: 1000);
                        $encodedImage = $image->toJpeg(80);
                        Storage::disk('public')->put($path, $encodedImage);
                        $imagePaths[] = $path;
                    } catch (\Exception $e) {

                        $imagePaths[] = $file->store('returns', 'public');
                    }
                }
            }

            $orderReturn = \App\Models\OrderReturn::create([
                'order_id' => $order->id,
                'user_id' => Auth::id(),
                'reason' => $request->reason,
                'description' => $request->description,
                'images' => $imagePaths,
                'status' => 'pending',
                'refund_amount' => 0
            ]);

            $totalRefund = 0;
            $quantities = $request->input('quantities', []);

            foreach ($request->items as $itemId => $val) {
                $qtyToReturn = isset($quantities[$itemId]) ? (int) $quantities[$itemId] : 1;

                if ($qtyToReturn > 0) {
                    $orderItem = $order->items->find($itemId);
                    if ($qtyToReturn > $orderItem->quantity) {
                        $qtyToReturn = $orderItem->quantity;
                    }

                    \App\Models\OrderReturnItem::create([
                        'order_return_id' => $orderReturn->id,
                        'order_item_id' => $itemId,
                        'quantity' => $qtyToReturn
                    ]);

                    $totalRefund += $orderItem->price * $qtyToReturn;
                }
            }

            $orderReturn->update(['refund_amount' => $totalRefund]);

            $recipients = User::role(['manager', 'staff'])->get();

            if ($recipients->count() > 0) {
                Notification::send($recipients, new NewReturnRequestNotification($orderReturn));
            }

            DB::commit();
            return redirect()->route('user.orders.index')->with('success', 'Yêu cầu hoàn hàng đã được gửi thành công.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }
}
