<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Auth;
use App\Models\Promotion;


class CartController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $userId = Auth::id();
        Cart::where('user_id', $userId)->update(['is_selected' => false]);

        $cart = Cart::with(['product', 'variant.attributeValues.attribute'])
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('user.cart.index', compact('cart'));
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'product_variant_id' => 'nullable|exists:product_variants,id'
        ]);

        $product = Product::findOrFail($request->product_id);
        $variantId = $request->product_variant_id;
        $quantityToAdd = (int) $request->quantity;

        $stockAvailable = 0;

        if ($variantId) {

            $variant = ProductVariant::find($variantId);
            $stockAvailable = $variant->stock;

        } else {
            $stockAvailable = $product->quantity;
        }

        $cartItem = Cart::where('user_id', Auth::id())
            ->where('product_id', $product->id)
            ->where('product_variant_id', $variantId)
            ->first();

        if ($cartItem) {

            $newQuantity = $cartItem->quantity + $quantityToAdd;

            if ($newQuantity > $stockAvailable) {
                return response()->json([
                    'success' => false,
                    'message' => 'Số lượng vượt quá tồn kho hiện có!'
                ], 400);
            }

            $cartItem->quantity = $newQuantity;
            $cartItem->save();
        } else {

            if ($quantityToAdd > $stockAvailable) {
                return response()->json([
                    'success' => false,
                    'message' => 'Số lượng vượt quá tồn kho hiện có!'
                ], 400);
            }

            Cart::create([
                'user_id' => Auth::id(),
                'product_id' => $product->id,
                'product_variant_id' => $variantId,
                'quantity' => $quantityToAdd,
            ]);
        }

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Đã thêm vào giỏ hàng!']);
        }

        return redirect()->route('user.cart.index')->with('success', 'Sản phẩm đã được thêm vào giỏ hàng!');
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $userId = Auth::id();
        $productId = $request->product_id;

        $variantId = $request->product_variant_id ? $request->product_variant_id : null;
        $qty = (int) $request->quantity;

        $cartItem = Cart::where('user_id', $userId)
            ->where('product_id', $productId)
            ->where('product_variant_id', $variantId)
            ->first();

        if (!$cartItem) {
            return response()->json(['success' => false, 'message' => 'Sản phẩm không tìm thấy trong giỏ.']);
        }

        $stockAvailable = 0;
        if ($cartItem->variant) {
            $stockAvailable = $cartItem->variant->stock;
        } else {
            $stockAvailable = $cartItem->product->quantity;
        }

        $message = 'Cập nhật thành công.';

        if ($qty < 1) $qty = 1;
        if ($qty > $stockAvailable) {
            $qty = $stockAvailable;
            $message = 'Số lượng đã được điều chỉnh về mức tối đa tồn kho (' . $stockAvailable . ').';
        }

        $cartItem->quantity = $qty;
        $cartItem->save();

        $unitPrice = $cartItem->variant ? $cartItem->variant->list_price : $cartItem->product->price;
        if ($cartItem->product->promotion_price) {
            $unitPrice = $cartItem->product->promotion_price;
        }

        $itemSubtotal = $unitPrice * $qty;

        return response()->json([
            'success' => true,
            'message' => $message,
            'quantity' => $qty,
            'item_subtotal_text' => number_format($itemSubtotal, 0, ',', '.') . '₫',
        ]);
    }

    /*  * Remove the specified resource from storage.
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */
    public function remove(Request $request)
    {
        Cart::where('user_id', Auth::id())
            ->where('product_id', $request->product_id)
            ->where('product_variant_id', $request->product_variant_id)
            ->delete();

        return response()->json(['success' => true]);
    }


    /**
     * Cập nhật trạng thái chọn mua (is_selected)
     */
    public function updateSelection(Request $request)
    {
        $userId = Auth::id();
        $isSelected = $request->is_selected ? 1 : 0;

        if ($request->has('select_all')) {
            Cart::where('user_id', $userId)->update(['is_selected' => $isSelected]);
        } else {
            Cart::where('user_id', $userId)
                ->where('product_id', $request->product_id)
                ->where('product_variant_id', $request->product_variant_id)
                ->update(['is_selected' => $isSelected]);
        }

        return response()->json(['success' => true]);
    }

    // public function applyPromotionsAjax(Request $request)
    // {
    //     $cart = session()->get('cart', []);

    //     foreach ($cart as $id => &$item) {
    //         $product = Product::find($id);
    //         if ($product && $product->promotion) {
    //             $item['price'] = $product->final_price;
    //         } else {
    //             $item['price'] = $product->price;
    //         }

    //         $item['total'] = $item['price'] * $item['quantity'];
    //     }

    //     session()->put('cart', $cart);

    //     $cartTotal = collect($cart)->sum('total');

    //     return response()->json([
    //         'cart' => $cart,
    //         'cartTotal' => number_format($cartTotal, 0, ',', '.') . ' đ'
    //     ]);
    // }
}
