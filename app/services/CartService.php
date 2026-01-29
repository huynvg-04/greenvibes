<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CartService
{
    public function countForUser(int $userId): int
    {
        return Cart::where('user_id', $userId)->sum('quantity');
    }

    public function listForUser(int $userId)
    {
        return Cart::with('product:id,name,image,price,promotion_id,promotion_price')
            ->where('user_id', $userId)
            ->get();
    }

    public function summaryForAi(int $userId): array
    {
        $rows = $this->listForUser($userId);
        $items = [];
        $total = 0;
        foreach ($rows as $r) {
            $p = $r->product;
            $final = $p ? ($p->final_price ?? 0) : 0;
            $items[] = [
                'product_id' => $r->product_id,
                'name' => $p ? $p->name : null,
                'qty' => $r->quantity,
                'final_price' => $final,
                'subtotal' => $final * $r->quantity,
                'selected' => (bool)$r->is_selected,
            ];
            if ($r->is_selected) $total += $final * $r->quantity;
        }
        return ['items' => $items, 'estimated_total' => $total];
    }

    public function add(int $userId, int $productId, int $qty = 1): Cart
    {
        $product = Product::findOrFail($productId);
        if ($product->quantity < $qty) {
            throw ValidationException::withMessages(['quantity' => 'Sản phẩm không đủ tồn kho']);
        }

        return DB::transaction(function () use ($userId, $productId, $qty, $product) {
            $row = Cart::where('user_id', $userId)->where('product_id', $productId)->first();
            if ($row) {
                $newQty = $row->quantity + $qty;
                if ($newQty > $product->quantity) {
                    throw ValidationException::withMessages(['quantity' => 'Vượt quá tồn kho']);
                }
                $row->update(['quantity' => $newQty]);
                return $row;
            }
            return Cart::create([
                'user_id'    => $userId,
                'product_id' => $productId,
                'quantity'   => $qty,
                'is_selected'=> true,
            ]);
        });
    }

    public function updateQuantity(int $userId, int $productId, int $qty): Cart
    {
        $cart = Cart::where('user_id', $userId)->where('product_id', $productId)->firstOrFail();
        if ($qty < 1) {
            $cart->delete();
            return $cart;
        }
        $product = Product::findOrFail($productId);
        if ($qty > $product->quantity) {
            throw ValidationException::withMessages(['quantity' => 'Vượt quá tồn kho']);
        }
        $cart->update(['quantity' => $qty]);
        return $cart;
    }

    public function select(int $userId, int $productId, bool $selected = true): void
    {
        Cart::where('user_id', $userId)->where('product_id', $productId)->update(['is_selected' => $selected]);
    }

    public function clear(int $userId): void
    {
        Cart::where('user_id', $userId)->delete();
    }
}
