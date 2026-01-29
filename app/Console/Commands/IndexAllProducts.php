<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\GenerateProductEmbedding;
use App\Models\Product;


class IndexAllProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'product:index-all';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Đẩy toàn bộ sản phẩm vào hàng đợi để tạo Vector';

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
        $products = Product::whereNull('embedding')->get();
        $this->info("Tìm thấy " . $products->count() . " sản phẩm chưa có Vector.");

        foreach ($products as $product) {
            GenerateProductEmbedding::dispatch($product);
            $this->info("Đã đẩy vào Queue: " . $product->name);
        }

        $this->info("Hoàn tất! Hãy chạy 'php artisan queue:work' để xử lý.");
    }
}
