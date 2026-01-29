<?php

namespace App\Http\Controllers;

use App\Models\Wishlist;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $wishlists = Wishlist::where('user_id', Auth::id())
            ->with('product')
            ->get();

        return view('user.wishlists.index', compact('wishlists'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($product_id)
    {
        Wishlist::firstOrCreate([
            'user_id' => Auth::id(),
            'product_id' => $product_id
        ]);

        return back()->with('success', 'Đã thêm vào yêu thích!');
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

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($product_id)
    {
        Wishlist::where('user_id', Auth::id())
            ->where('product_id', $product_id)
            ->delete();

        return back()->with('success', 'Đã xóa khỏi yêu thích!');
    }

    /**
     * Toggle wishlist status for a product.
     *
     * @param  int  $productId
     * @return \Illuminate\Http\Response
     */
    public function toggle($productId)
    {
        $user = Auth::user();

        if ($user->hasInWishlist($productId)) {
            $user->wishlist()->detach($productId);
            return back()->with('success', 'Đã bỏ khỏi danh sách yêu thích');
        }

        $user->wishlist()->attach($productId);
        return back()->with('success', 'Đã thêm vào danh sách yêu thích');
    }
}
