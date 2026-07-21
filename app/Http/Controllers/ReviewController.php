<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OrderItem;
use App\Models\Review;
use App\Models\ReviewLike;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Services\ImageUploadService;

class ReviewController extends Controller
{
    /**
     * Store a newly created review in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $itemId
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $itemId)
    {
        $this->authorize('create', Review::class);

        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:500',
            'images' => 'nullable|array|max:5',
            'images.*' => 'image|mimes:jpeg,png,jpg,webp|max:5120',
        ], [
            'images.max' => 'Bạn chỉ được tải lên tối đa 5 ảnh.',
            'images.*.max' => 'Dung lượng mỗi ảnh không được quá 5MB.',
            'images.*.image' => 'File tải lên phải là hình ảnh.',
        ]);

        $item = OrderItem::with('order')->findOrFail($itemId);

        if ($item->order->user_id !== Auth::id()) {
            return response()->json(['error' => 'Bạn không có quyền đánh giá đơn hàng này'], 403);
        }

        if ($item->order->status != 'completed') {
            return response()->json(['error' => 'Chỉ đơn hàng đã hoàn thành mới được đánh giá'], 403);
        }

        if (Review::where('order_item_id', $item->id)->exists()) {
            return response()->json(['error' => 'Sản phẩm này đã được đánh giá rồi'], 403);
        }

       try {
            $imagePaths = [];
            if ($request->hasFile('images')) {
                /** @var ImageUploadService $imageService */
                $imageService = app(ImageUploadService::class);
                $imagePaths = $imageService->uploadMultiple(
                    $request->file('images'),
                    'reviews',
                    800,
                    80,
                    'jpeg',
                    'review_'
                );
            }

            $review = Review::create([
                'user_id' => Auth::id(),
                'order_item_id' => $item->id,
                'rating' => $request->rating,
                'comment' => $request->comment,
                'images' => $imagePaths, 
                'likes_count' => 0
            ]);

            session()->flash('success', 'Đánh giá thành công!');

            return response()->json([
                'success' => true,
                'message' => 'Đánh giá thành công!',
                'review' => $review
            ]);

        } catch (\Exception $e) {
            Log::error("Review Store Error: " . $e->getMessage());
            return response()->json(['error' => 'Lỗi server: ' . $e->getMessage()], 500);
        }
    }

    public function toggleLike($reviewId)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Vui lòng đăng nhập để thích đánh giá.'], 401);
        }

        $review = Review::findOrFail($reviewId);
        $userId = Auth::id();

        DB::beginTransaction();
        try {
            $existingLike = ReviewLike::where('review_id', $reviewId)
                ->where('user_id', $userId)
                ->first();

            if ($existingLike) {

                $existingLike->delete();
                $review->decrement('likes_count');
                $isLiked = false;
                $message = 'Đã bỏ thích';
            } else {
                ReviewLike::create([
                    'review_id' => $reviewId,
                    'user_id' => $userId
                ]);
                $review->increment('likes_count');
                $isLiked = true;
                $message = 'Đã thích';
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'likes_count' => $review->likes_count,
                'is_liked' => $isLiked,
                'message' => $message
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Lỗi xử lý'], 500);
        }
    }
}
