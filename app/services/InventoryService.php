<?php

namespace App\Services;

use App\Models\ProductVariant;
use App\Models\WarehouseTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Exception;

class InventoryService
{
    /**
     * Ghi nhận giao dịch kho (Nhập/Xuất/Điều chỉnh)
     *
     * @param int $variantId ID của phân loại sản phẩm
     * @param int $quantity Số lượng thay đổi (Luôn nhập số dương)
     * @param string $type Loại giao dịch: 'in' (Nhập) hoặc 'out' (Xuất)
     * @param string $description Ghi chú chi tiết (VD: "Bán đơn hàng #123")
     * @param mixed $reference Model liên quan (VD: $order, $importNote) - Có thể null
     * * @return WarehouseTransaction
     * @throws Exception
     */
    public static function log($variantId, int $quantity, string $type, string $description = '', $reference = null)
    {
        if ($quantity <= 0) {
            throw new Exception("Số lượng giao dịch kho phải lớn hơn 0.");
        }

        if (!in_array($type, ['in', 'out'])) {
            throw new Exception("Loại giao dịch không hợp lệ (chỉ chấp nhận 'in' hoặc 'out').");
        }

        

        return DB::transaction(function () use ($variantId, $quantity, $type, $description, $reference) {

            $variant = ProductVariant::lockForUpdate()->find($variantId);

            if (!$variant) {
                throw new Exception("Phân loại sản phẩm (ID: $variantId) không tồn tại.");
            }
            $currentStock = $variant->stock;

            if ($type === 'in') {
                $variant->stock = $currentStock + $quantity;
            } else {    
                if ($currentStock < $quantity) {
                    throw new Exception("Kho không đủ hàng để xuất! (Tồn: {$currentStock}, Cần: {$quantity}) - SKU: {$variant->sku}");
                }
                $variant->stock = $currentStock - $quantity;
            }

            $variant->save();

            $transaction = WarehouseTransaction::create([
                'product_variant_id' => $variantId,
                'type' => $type,
                'quantity' => $quantity,
                'balance_after' => $variant->stock, 
                'user_id' => Auth::id(),
                'description' => $description,
                'reference_type' => $reference ? get_class($reference) : null,
                'reference_id' => $reference ? $reference->id : null,
            ]);

            return $transaction;
        });
    }
}
