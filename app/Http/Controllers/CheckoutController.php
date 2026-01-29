<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Coupon;
use App\Models\PaymentMethod;
use App\Models\ShippingRate;
use App\Models\User;
use App\Models\TierUsageLog;
use App\Notifications\NewOrderNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Mail;
use App\Mail\InvoiceMail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;

use App\Services\InventoryService;

class CheckoutController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $cartItems = Cart::where('user_id', Auth::id())
            ->where('is_selected', true)
            ->with(['product', 'variant.attributeValues.attribute'])
            ->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('user.cart.index')->with('error', 'Vui lòng chọn sản phẩm để thanh toán.');
        }

        foreach ($cartItems as $item) {
            if ($item->variant) {
                $currentStock = $item->variant->stock;
                $productName = $item->product->name . ' (' . $item->variant->sku . ')';
            } else {
                $currentStock = $item->product->quantity;
                $productName = $item->product->name;
            }

            if ($item->quantity > $currentStock) {
                return redirect()->route('user.cart.index')->with('error', "Sản phẩm \"$productName\" hiện không đủ hàng (Chỉ còn $currentStock). Vui lòng cập nhật lại giỏ hàng.");
            }

            if ($item->product->status == 0) {
                return redirect()->route('user.cart.index')->with('error', "Sản phẩm \"$productName\" đã ngừng kinh doanh.");
            }
        }

        $calculations = $this->calculateOrderTotal($cartItems);

        $paymentMethods = PaymentMethod::where('is_active', true)->orderBy('sort_order')->get();
        $shippingRates = ShippingRate::where('is_active', true)->orderBy('fee', 'asc')->get();

        return view('user.orders.checkout', array_merge([
            'cartItems' => $cartItems,
            'user' => Auth::user(),
            'paymentMethods' => $paymentMethods,
            'shippingRates' => $shippingRates
        ], $calculations));
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

    public function applyCoupon(Request $request)
    {
        $coupon = Coupon::where('code', $request->code)->where('is_active', true)->first();

        if (!$coupon || !$coupon->isValid()) {
            return back()->with('error', 'Mã giảm giá không hợp lệ hoặc đã hết hạn.');
        }

        $user = Auth::user();
        $cartItems = Cart::where('user_id', $user->id)->where('is_selected', true)->get();

        $subtotal = $cartItems->sum(function ($item) {
            $price = $item->variant ? $item->variant->list_price : $item->product->price;
            if ($item->product->promotion_price) $price = $item->product->promotion_price;
            return $price * $item->quantity;
        });

        if ($subtotal < $coupon->min_order_value) {
            return back()->with('error', 'Đơn hàng chưa đạt giá trị tối thiểu ' . number_format($coupon->min_order_value) . 'đ để dùng mã này.');
        }

        session()->put('coupon_code', $coupon->code);
        return back()->with('success', 'Đã áp dụng mã giảm giá');
    }


    public function removeCoupon()
    {
        session()->forget('coupon_code');
        return back()->with('success', 'Đã bỏ áp dụng mã giảm giá.');
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


    private function calculateOrderTotal($cartItems)
    {
        $user = Auth::user();
        $subtotal = 0;

        foreach ($cartItems as $item) {
            $basePrice = $item->variant ? $item->variant->list_price : $item->product->price;
            if ($item->product->promotion_price) {
                $basePrice = $item->product->promotion_price;
            }
            $subtotal += $basePrice * $item->quantity;
        }

        $couponDiscount = 0;
        $couponCode = session('coupon_code');
        $coupon = null;
        if ($couponCode) {
            $coupon = Coupon::where('code', $couponCode)->where('is_active', true)->first();

            if ($coupon) {
                // 1. Ép kiểu số để so sánh chính xác
                $minOrder = (float)$coupon->min_order_value;

                // 2. Kiểm tra điều kiện
                // Tạm thời bỏ qua isValid() để test xem có phải lỗi do nó không
                // Nếu muốn dùng isValid(), hãy chắc chắn nó không chặn sai
                if ($subtotal < $minOrder) {
                    // Hủy nếu không đủ tiền
                    $coupon = null;
                } else {
                    // 3. Tính toán (Ép kiểu float cho value)
                    $val = (float)$coupon->value;
                    $type = strtolower($coupon->type); // fixed / percent

                    if ($type == 'fixed') {
                        $couponDiscount = $val;
                    } elseif ($type == 'percent') {
                        $calc = $subtotal * ($val / 100);
                        $maxDiscount = (float)$coupon->max_discount_value;

                        if ($maxDiscount > 0) {
                            $couponDiscount = min($calc, $maxDiscount);
                        } else {
                            $couponDiscount = $calc;
                        }
                    }
                }
            }
        }

        $tierDiscount = 0;
        $activeTier = null;

        if ($user->customerProfile && $user->customerProfile->tier) {
            $tier = $user->customerProfile->tier;

            if ($tier->discount > 0) {
                $canUseTierDiscount = true;

                if ($tier->usage_limit > 0) {
                    $query = TierUsageLog::where('user_id', $user->id)
                        ->where('membership_tier_id', $tier->id);

                    if ($tier->usage_period === 'month') {
                        $query->whereMonth('created_at', Carbon::now()->month)
                            ->whereYear('created_at', Carbon::now()->year);
                    } elseif ($tier->usage_period === 'year') {
                        $query->whereYear('created_at', Carbon::now()->year);
                    }

                    $usedCount = $query->count();

                    if ($usedCount >= $tier->usage_limit) {
                        $canUseTierDiscount = false;
                    }
                }

                if ($canUseTierDiscount) {
                    $activeTier = $tier;
                    $tierDiscount = $subtotal * ($tier->discount / 100);
                }
            }
        }

        $totalDiscount = $couponDiscount + $tierDiscount;

        $total = max($subtotal - $totalDiscount, 0);

        return [
            'subtotal' => $subtotal,
            'couponDiscount' => $couponDiscount,
            'tierDiscount' => $tierDiscount,
            'discount' => $totalDiscount,
            'total' => $total,
            'coupon' => $coupon,
            'couponCode' => $coupon ? $coupon->code : null,
            'activeTier' => $activeTier
        ];
    }

    public function process(Request $request)
    {
        $request->validate([
            'fullname' => 'required',
            'phone' => 'required',
            'address' => 'required',
            'payment_method' => 'required|exists:payment_methods,code',
            'shipping_rate_id' => 'required|exists:shipping_rates,id',
        ], [
            'fullname.required' => 'Vui lòng nhập họ và tên người nhận.',
            'phone.required' => 'Vui lòng nhập số điện thoại.',
            'address.required' => 'Vui lòng nhập địa chỉ nhận hàng.',
            'payment_method.required' => 'Vui lòng chọn phương thức thanh toán.',
            'shipping_rate_id.required' => 'Vui lòng chọn hình thức vận chuyển.',
        ]);

        if ($request->filled('applied_coupon')) {
            $codeFromInput = $request->input('applied_coupon');

            session()->put('coupon_code', $codeFromInput);
            session()->save(); 
        }

        $user = Auth::user();
        $cartItems = Cart::where('user_id', $user->id)
            ->where('is_selected', true)
            ->with(['product', 'variant'])
            ->get();

        if ($cartItems->isEmpty()) return redirect()->route('user.cart.index');


        // $debugCalcs = $this->calculateOrderTotal($cartItems);
        // dd([
        //     'Session Code' => session('coupon_code'),
        //     'Input Code' => $request->input('applied_coupon'),
        //     'Subtotal' => $debugCalcs['subtotal'],
        //     'Coupon Discount' => $debugCalcs['couponDiscount'],
        // ]);

        DB::beginTransaction();
        try {
            $calcs = $this->calculateOrderTotal($cartItems);

            $shippingRate = ShippingRate::findOrFail($request->shipping_rate_id);
            $shippingFee = $shippingRate->fee;

            if ($shippingRate->min_order_value > 0 && $calcs['subtotal'] >= $shippingRate->min_order_value) {
                $shippingFee = 0;
            }

            $finalTotal = $calcs['total'] + $shippingFee;

            $order = Order::create([
                'user_id' => $user->id,
                'code' => 'ORD-' . strtoupper(Str::random(8)),
                'total_amount' => $finalTotal,

                'discount_amount' => $calcs['discount'],
                'coupon_code' => $calcs['couponCode'],
                'coupon_discount' => $calcs['couponDiscount'],
                'tier_discount' => $calcs['tierDiscount'],

                'shipping_fee' => $shippingFee,
                'shipping_method' => $shippingRate->name,
                'shipping_address' => $request->address,
                'phone' => $request->phone,
                'note' => $request->note,
                'payment_method' => $request->payment_method,
                'status' => 'pending',
                'order_date' => now(),
            ]);

            if ($calcs['coupon']) {
                $calcs['coupon']->increment('used_count');
            }

            if ($calcs['tierDiscount'] > 0 && $calcs['activeTier']) {
                TierUsageLog::create([
                    'user_id' => $user->id,
                    'order_id' => $order->id,
                    'membership_tier_id' => $calcs['activeTier']->id,
                    'discount_amount' => $calcs['tierDiscount']
                ]);
            }

            foreach ($cartItems as $item) {
                if ($item->product_variant_id) {
                    $lockedVariant = \App\Models\ProductVariant::where('id', $item->product_variant_id)
                        ->lockForUpdate()->first();
                    $stock = $lockedVariant->stock ?? $lockedVariant->quantity ?? 0;

                    if (!$lockedVariant || $stock < $item->quantity) {
                        throw new \Exception("Sản phẩm " . $item->product->name . " (Biến thể) vừa hết hàng.");
                    }
                } else {
                    $lockedProduct = \App\Models\Product::where('id', $item->product_id)
                        ->lockForUpdate()->first();
                    if (!$lockedProduct || $lockedProduct->quantity < $item->quantity) {
                        throw new \Exception("Sản phẩm " . $lockedProduct->name . " vừa hết hàng.");
                    }
                }

                $price = $item->variant ? $item->variant->list_price : $item->product->price;
                if ($item->product->promotion_price) $price = $item->product->promotion_price;

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'product_variant_id' => $item->product_variant_id,
                    'quantity' => $item->quantity,
                    'price' => $price,
                ]);

                if ($item->product) $item->product->increment('sold_count', $item->quantity);
                if ($item->product_variant_id && $item->variant) $item->variant->increment('sold_count', $item->quantity);

                if ($item->product_variant_id) {
                    InventoryService::log($item->product_variant_id, $item->quantity, 'out', "Đơn hàng #{$order->code}", $order);
                } else {
                    $item->product->decrement('quantity', $item->quantity);
                }
            }

            Cart::where('user_id', $user->id)->where('is_selected', true)->delete();

            // Xóa session coupon sau khi đặt hàng thành công
            session()->forget('coupon_code');

            DB::commit();

            // Gửi thông báo Admin
            try {
                $admins = User::role(['manager', 'staff'])->get();
                if ($admins->count() > 0) {
                    Notification::send($admins, new NewOrderNotification($order));
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Lỗi thông báo: ' . $e->getMessage());
            }

            // Xử lý thanh toán và Redirect
            if ($request->payment_method == 'cod') {
                try {
                    if ($user->email) {
                        Mail::to($user->email)->send(new InvoiceMail($order));
                    }
                } catch (\Exception $e) {
                    Log::error('Lỗi mail COD: ' . $e->getMessage());
                }
                return redirect()->route('user.checkout.success', $order->code);
            } elseif ($request->payment_method == 'vnpay') {
                return redirect()->route('payment.create', ['orderId' => $order->id]);
            }

            return redirect()->route('user.checkout.success', $order->code);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Lỗi đặt hàng: ' . $e->getMessage());
        }
    }

    public function success($code)
    {
        $order = Order::where('code', $code)->where('user_id', Auth::id())->firstOrFail();
        return view('user.orders.success', compact('order'));
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
