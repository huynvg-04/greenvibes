<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use Carbon\Carbon;

class AutoConfirmOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'order:auto-confirm';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tự động chuyển đơn hàng Pending quá 30 phút sang Confirmed';
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
        $threshold = Carbon::now()->subMinutes(30);

        $affected = Order::where('status', 'pending')
            ->where('created_at', '<=', $threshold)
            ->update(['status' => 'confirmed']); 

        if ($affected > 0) {
            $this->info("Đã tự động xác nhận {$affected} đơn hàng quá hạn hủy.");
        } else {
            $this->info("Không có đơn hàng nào cần xử lý.");
        }
    }   
}
