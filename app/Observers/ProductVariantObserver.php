<?php

namespace App\Observers;

use App\Models\ProductVariant;

class ProductVariantObserver
{
    /**
     * Handle the ProductVariant "created" event.
     *
     * @param  \App\Models\ProductVariant  $productVariant
     * @return void
     */
    public function created(ProductVariant $productVariant)
    {
        //
    }

    /**
     * Handle the ProductVariant "updated" event.
     *
     * @param  \App\Models\ProductVariant  $productVariant
     * @return void
     */
    public function updated(ProductVariant $productVariant)
    {
        //
    }

    /**
     * Handle the ProductVariant "deleted" event.
     *
     * @param  \App\Models\ProductVariant  $productVariant
     * @return void
     */
    public function deleted(ProductVariant $productVariant)
    {
        //
    }

    /**
     * Handle the ProductVariant "restored" event.
     *
     * @param  \App\Models\ProductVariant  $productVariant
     * @return void
     */
    public function restored(ProductVariant $productVariant)
    {
        //
    }

    /**
     * Handle the ProductVariant "force deleted" event.
     *
     * @param  \App\Models\ProductVariant  $productVariant
     * @return void
     */
    public function forceDeleted(ProductVariant $productVariant)
    {
        //
    }

    public function saving(ProductVariant $variant)
    {
        if ($variant->isDirty('list_price') || !$variant->exists) {

            $product = $variant->product;

            if ($product) {
                $discountPercent = $product->discount_percent ?? 0;

                if ($discountPercent > 0 && $variant->list_price > 0) {
                
                    $discountedPrice = $variant->list_price * ((100 - $discountPercent) / 100);

                    $variant->sale_price = ceil($discountedPrice / 1000) * 1000;
                } else {
                    $variant->sale_price = $variant->list_price;
                }
            }
        }
    }
}
