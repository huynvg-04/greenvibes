<?php

namespace App\Observers;

use App\Models\Product;
use App\Jobs\GenerateProductEmbedding;
use Illuminate\Support\Facades\Log;


class ProductObserver
{
    /**
     * Handle the Product "created" event.
     *
     * @param  \App\Models\Product  $product
     * @return void
     */
    public function created(Product $product)
    {
        GenerateProductEmbedding::dispatch($product)->delay(now()->addSeconds(2));
    }

    /**
     * Handle the Product "updated" event.
     *
     * @param  \App\Models\Product  $product
     * @return void
     */
    public function updated(Product $product)
    {
        Log::info('Observer đã bắt được sự kiện update cho ID: ' . $product->id);

        $infoChanged = $product->isDirty(['name', 'description', 'category_id', 'status', 'sold_count']);

        $variantChanged = $product->isDirty('updated_at');

        if ($infoChanged || $variantChanged) {
            Log::info('Dữ liệu quan trọng đã thay đổi, đang cập nhật Vector...');
            GenerateProductEmbedding::dispatch($product);
        } else {
            Log::info('Không có thay đổi cần thiết để cập nhật Vector.');
        }
    }

    /**
     * Handle the Product "deleted" event.
     *
     * @param  \App\Models\Product  $product
     * @return void
     */
    public function deleted(Product $product)
    {
        //
    }

    /**
     * Handle the Product "restored" event.
     *
     * @param  \App\Models\Product  $product
     * @return void
     */
    public function restored(Product $product)
    {
        //
    }

    /**
     * Handle the Product "force deleted" event.
     *
     * @param  \App\Models\Product  $product
     * @return void
     */
    public function forceDeleted(Product $product)
    {
        //
    }
}
