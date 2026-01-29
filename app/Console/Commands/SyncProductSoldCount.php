<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\DB;

class SyncProductSoldCount extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'product:sync-sold';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tính toán và cập nhật lại số lượng đã bán cho toàn bộ sản phẩm từ lịch sử đơn hàng';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Bắt đầu đồng bộ dữ liệu sold_count...');

        if (class_exists(ProductVariant::class)) {
            $variants = ProductVariant::all();
            $bar = $this->output->createProgressBar(count($variants));
            $this->info("\nĐang xử lý biến thể...");
            $bar->start();

            foreach ($variants as $variant) {
                $sold = DB::table('order_items')
                    ->join('orders', 'orders.id', '=', 'order_items.order_id')
                    ->where('orders.status', '!=', 'cancelled') 
                    ->where('order_items.product_variant_id', $variant->id)
                    ->sum('order_items.quantity');

                $variant->update(['sold_count' => $sold]);
                $bar->advance();
            }
            $bar->finish();
        }

        $products = Product::all();
        $this->info("\n\nĐang xử lý sản phẩm cha...");
        $bar = $this->output->createProgressBar(count($products));
        $bar->start();

        foreach ($products as $product) {
            $sold = DB::table('order_items')
                ->join('orders', 'orders.id', '=', 'order_items.order_id')
                ->where('orders.status', '!=', 'cancelled') 
                ->where('order_items.product_id', $product->id)
                ->sum('order_items.quantity');

            $product->update(['sold_count' => $sold]);
            $bar->advance();
        }
        $bar->finish();

        $this->info("\n\nĐồng bộ hoàn tất thành công!");
    }
}
