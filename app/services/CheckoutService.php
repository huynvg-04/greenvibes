<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Cart;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CheckoutService
{
    public function createOrderFromCart(int $userId, ?string $address = null): Order
    {
        return DB::transaction(function() use ($userId, $address) {
            $rows = Cart::with('product')->where('user_id', $userId)->where('is_selected', true)->get();
            if ($rows->isEmpty()) {
                throw ValidationException::withMessages(['cart' => 'Giỏ hàng trống']);
            }

            foreach ($rows as $row) {
                if (!$row->product || $row->product->quantity < $row->quantity) {
                    $name = $row->product ? $row->product->name : null;
                    throw ValidationException::withMessages(['stock' => 'Không đủ tồn cho: '.($name ?? '')]);
                }
            }

            $order = Order::create([
                'user_id' => $userId,
                'address' => $address,
                'total'   => 0,
                'status'  => 'pending',
            ]);

            $sum = 0;
            foreach ($rows as $row) {
                $p = $row->product->fresh();
                $final = $p->final_price;

                $aff = Product::where('id', $p->id)
                    ->where('quantity','>=',$row->quantity)
                    ->decrement('quantity', $row->quantity);

                if (!$aff) {
                    throw ValidationException::withMessages(['stock' => 'Hết hàng: '.$p->name]);
                }

                OrderItem::create([
                    'order_id'     => $order->id,
                    'product_id'   => $p->id,
                    'product_name' => $p->name,
                    'category_id'  => $p->category_id,
                    'quantity'     => $row->quantity,
                    'price'        => $final,
                    'subtotal'     => $final * $row->quantity,
                ]);

                $sum += $final * $row->quantity;
            }

            $order->update(['total' => $sum]);

        
            return $order;
        });
    }
}
