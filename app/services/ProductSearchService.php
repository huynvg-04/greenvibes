<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Facades\DB;

class ProductSearchService
{
    /**
     * Tìm sản phẩm theo name/description (không phân biệt hoa/thường).
     * - Có thể lọc theo category_id và khoảng giá trên final_price = COALESCE(promotion_price, price).
     * - Có fallback bỏ dần điều kiện để luôn trả về kết quả.
     */
    public function recommend(?string $q, ?int $categoryId, ?float $minPrice, ?float $maxPrice, int $limit = 6)
    {
        $finalExpr = DB::raw('COALESCE(promotion_price, price)');

        // Chuẩn hoá keyword đơn giản
        $keywords = [];
        if ($q !== null) {
            $q = trim(mb_strtolower($q, 'UTF-8'));
            if ($q !== '') {
                $keywords = array_values(array_filter(preg_split('/\s+/', $q)));
            }
        }

        // Base query: chỉ dùng cột có thật
        $base = Product::query()
            ->with(['category:id,name']) // không eager cột lạ
            ->select(['id','name','description','image','price','promotion_price','quantity','category_id','promotion_id'])
            ->selectRaw('COALESCE(promotion_price, price) as final_price')
            ->when(!empty($keywords), function ($q) use ($keywords) {
                foreach ($keywords as $word) {
                    $like = '%'.$word.'%';
                    $q->where(function ($w) use ($like) {
                        $w->whereRaw('LOWER(name) LIKE ?', [$like])
                          ->orWhereRaw('LOWER(IFNULL(description,"")) LIKE ?', [$like]);
                    });
                }
            });

        // Helper tạo query để fallback
        $make = function (bool $useCategory, bool $usePrice) use ($base, $categoryId, $minPrice, $maxPrice, $finalExpr, $limit) {
            $q = (clone $base);
            if ($useCategory && $categoryId !== null) {
                $q->where('category_id', $categoryId);
            }
            if ($usePrice) {
                if ($minPrice !== null) $q->whereRaw('COALESCE(promotion_price, price) >= ?', [$minPrice]);
                if ($maxPrice !== null) $q->whereRaw('COALESCE(promotion_price, price) <= ?', [$maxPrice]);
            }
            return $q->orderBy($finalExpr)->limit($limit);
        };

        // Thử lần lượt để luôn có dữ liệu
        $items = $make(true,  true)->get();                                // 1) category + price
        if ($items->isEmpty() && ($minPrice !== null || $maxPrice !== null)) {
            $items = $make(true,  false)->get();                           // 2) category only
        }
        if ($items->isEmpty() && $categoryId !== null) {
            $items = $make(false, true)->get();                            // 3) no category + price
        }
        if ($items->isEmpty()) {
            $items = $make(false, false)->get();                           // 4) no category, no price
        }

        return $items;
    }

    /** Lấy chi tiết sản phẩm theo ID (chỉ cột có thật) */
    public function detailById(int $id): ?Product
    {
        return Product::query()
            ->with(['category:id,name'])
            ->select(['id','name','description','image','price','promotion_price','quantity','category_id','promotion_id'])
            ->selectRaw('COALESCE(promotion_price, price) as final_price')
            ->find($id);
    }
}
