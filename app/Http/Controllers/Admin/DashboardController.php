<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $amountCol = Schema::hasColumn('orders', 'total') ? 'total' : 'total_amount';

        $revenueStats = $this->calculateGrowth(Order::class, 'sum', $amountCol, function($q) {
            $q->where('status', 'completed');
        });

        $orderStats = $this->calculateGrowth(Order::class, 'count');

        $productStats = $this->calculateGrowth(Product::class, 'count');

        $userStats = $this->calculateGrowth(User::class, 'count', null, function($q) {
            $q->has('customerProfile');
        });

        $recentOrders = Order::with('user')
            ->latest()
            ->take(9)
            ->get();

        if (Schema::hasColumn('products', 'sold_count')) {
            $topProducts = Product::with(['category', 'images']) 
                ->orderBy('sold_count', 'desc')
                ->take(5)
                ->get();
        } else {
            $topProducts = Product::with(['category', 'images'])
                ->latest()
                ->take(5)
                ->get();
        }

        return view('admin.dashboard', compact(
            'revenueStats', 
            'orderStats', 
            'productStats', 
            'userStats',
            'recentOrders', 
            'topProducts'
        ));
    }

    /**
     * Hàm Helper: Tính toán số liệu tháng này, tháng trước và % tăng trưởng.
     * * @param string $modelClass  Class Model (VD: Order::class)
     * @param string $type        Loại tính: 'count' hoặc 'sum'
     * @param string|null $column Cột cần tính tổng (nếu type = sum)
     * @param callable|null $callback Hàm callback để thêm điều kiện query (where status...)
     * * @return array ['this_month', 'last_month', 'growth']
     */
    private function calculateGrowth($modelClass, $type = 'count', $column = null, $callback = null)
    {
        $now = Carbon::now();
    
        $qCurrent = $modelClass::query()
            ->whereMonth('created_at', $now->month)
            ->whereYear('created_at', $now->year);

        $lastMonth = $now->copy()->subMonth();
        $qLast = $modelClass::query()
            ->whereMonth('created_at', $lastMonth->month)
            ->whereYear('created_at', $lastMonth->year);

        if (is_callable($callback)) {
            $callback($qCurrent);
            $callback($qLast);
        }

        if ($type === 'sum') {
            $currentVal = $qCurrent->sum($column);
            $lastVal = $qLast->sum($column);
        } else {
            $currentVal = $qCurrent->count();
            $lastVal = $qLast->count();
        }

        if ($lastVal > 0) {
            $growth = (($currentVal - $lastVal) / $lastVal) * 100;
        } else {
            // Nếu tháng trước = 0:
            // - Tháng này > 0 => Tăng 100%
            // - Tháng này = 0 => Không đổi (0%)
            $growth = $currentVal > 0 ? 100 : 0;
        }

        return [
            'this_month' => $currentVal,      // Giá trị tháng này (Hiển thị số to)
            'last_month' => $lastVal,         // Giá trị tháng trước (Hiển thị tooltip)
            'growth'     => round($growth, 1) // Phần trăm tăng/giảm
        ];
    }

    /**
     * API trả về dữ liệu biểu đồ doanh thu theo năm (nếu bạn dùng Chart.js)
     */
    public function revenue()
    {
        $year = Carbon::now()->year;
        $amountColumn = Schema::hasColumn('orders', 'total') ? 'total' : 'total_amount';

        $revenues = Order::whereYear('created_at', $year)
            ->where('status', 'completed')
            ->selectRaw("MONTH(created_at) as month, SUM($amountColumn) as total")
            ->groupBy('month')
            ->pluck('total', 'month')
            ->toArray();

        $data = [];
        for ($i = 1; $i <= 12; $i++) {
            $data[$i] = $revenues[$i] ?? 0;
        }

        return view('admin.revenue', [
            'data' => $data,
            'year' => $year,
        ]);
    }
}