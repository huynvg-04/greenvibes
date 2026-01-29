<?php

namespace App\View\Components;

use App\Models\Product;
use Illuminate\View\Component;

class ProductCard extends Component
{
    public $product;
    public $image;
    public $discountPercent;
    public $priceHtml;
    public $rating;
    public $reviewCount;
    public $soldCount;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(Product $product)
    {
        $this->product = $product;
        $this->prepareData();
    }



    protected function prepareData()
    {
        if ($this->product->primary_image) {
            $this->image = asset('storage/' . $this->product->primary_image);
        } elseif ($this->product->images->first()) {
       
            $this->image = asset('storage/' . $this->product->images->first()->image_url);
        } else {
            $this->image = null; 
        }

        $hasVariants = $this->product->variants->isNotEmpty();
        $minPrice = $hasVariants ? $this->product->variants->min('list_price') : $this->product->price;

        $this->discountPercent = 0;

        if ($this->product->promotion_price && $this->product->promotion_price < $minPrice) {

            $this->discountPercent = round((($minPrice - $this->product->promotion_price) / $minPrice) * 100);

            $this->priceHtml = sprintf(
                '<span class="price-original" aria-label="Giá gốc">%s₫</span>
                 <span class="price-current" aria-label="Giá khuyến mãi">%s₫</span>',
                number_format($minPrice),
                number_format($this->product->promotion_price)
            );
        } else {
            $prefix = $hasVariants ? '<span style="font-size: 0.9em; font-weight: normal;">Từ</span> ' : '';
            $this->priceHtml = sprintf(
                '<span class="price-regular" aria-label="Giá bán">%s%s₫</span>',
                $prefix,
                number_format($minPrice)
            );
        }

        $this->rating = round($this->product->reviews->avg('rating'), 1);
        $this->reviewCount = $this->product->reviews->count();

        $this->soldCount = $this->product->completed_order_items_sum_quantity ?? 0;
    }


    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.product-card');
    }
}
