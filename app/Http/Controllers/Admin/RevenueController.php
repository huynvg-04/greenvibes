<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class RevenueController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', 'revenue');

        $filterType = $request->get('filter_type', 'date');

        $dateFrom = $request->date_from ? Carbon::parse($request->date_from)->startOfDay() : Carbon::now()->startOfMonth();
        $dateTo = $request->date_to ? Carbon::parse($request->date_to)->endOfDay() : Carbon::now()->endOfMonth();

        if ($filterType === 'month') {
            $dateFrom = Carbon::now()->startOfYear();
            $dateTo = Carbon::now()->endOfYear();
        } elseif ($filterType === 'quarter') {
            $dateFrom = Carbon::now()->startOfYear();
            $dateTo = Carbon::now()->endOfYear();
        } elseif ($filterType === 'year') {
            $dateFrom = Carbon::now()->subYears(5)->startOfYear();
            $dateTo = Carbon::now()->endOfYear();
        }

        $validStatuses = ['completed']; 
        $validReturnStatuses = ['completed']; 

        $orders = Order::whereIn('status', $validStatuses)
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->with([
                'items.product', 
                'items.variant',
                'returns' => function($query) use ($validReturnStatuses) {
                    $query->whereIn('status', $validReturnStatuses);
                },
                // [THAY ĐỔI 1] Đã sửa 'returnItems' thành 'items'
                'returns.items.orderItem.product', 
                'returns.items.orderItem.variant'
            ])
            ->get();

        $totalRevenue = 0;
        $totalCost = 0;
        $totalRefunds = 0; 
        $totalOrders = $orders->count();

        foreach ($orders as $order) {
            $totalRevenue += $order->total_amount;

            foreach ($order->items as $item) {
                $itemCost = 0;
                if ($item->variant) {
                    $itemCost = $item->variant->standard_cost;
                } elseif ($item->product) {
                    $itemCost = $item->product->standard_cost;
                }
                $totalCost += ($itemCost * $item->quantity);
            }

            foreach ($order->returns ?? [] as $return) {
                $totalRevenue -= $return->refund_amount;
                $totalRefunds += $return->refund_amount;

                // [THAY ĐỔI 2] Đã sửa '$return->returnItems' thành '$return->items'
                foreach ($return->items ?? [] as $returnItem) {
                    $originalItem = $returnItem->orderItem; 
                    
                    if ($originalItem) {
                        $costPerUnit = 0;
                        if ($originalItem->variant) {
                            $costPerUnit = $originalItem->variant->standard_cost;
                        } elseif ($originalItem->product) {
                            $costPerUnit = $originalItem->product->standard_cost;
                        }
                        $totalCost -= ($costPerUnit * $returnItem->quantity);
                    }
                }
            }
        }

        $totalProfit = $totalRevenue - $totalCost;
        $averageOrderValue = $totalOrders > 0 ? ($totalRevenue / $totalOrders) : 0;

        // --- Các query phụ (Status, Customer, Product, Category) giữ nguyên ---
        $orderStatusStats = Order::whereBetween('created_at', [$dateFrom, $dateTo])
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        if ($request->has('customer_year')) {
            $custYear = $request->get('customer_year'); 
        } else {
            $custYear = Carbon::now()->year;
        }
        $custMonth = $request->get('customer_month');

        $customerQuery = DB::table('orders')
            ->join('users', 'users.id', '=', 'orders.user_id')
            ->leftJoin('customer_profiles', 'users.id', '=', 'customer_profiles.user_id')
            ->whereIn('orders.status', $validStatuses)
            ->select(
                'users.id',
                'users.email',
                DB::raw('COALESCE(customer_profiles.full_name, users.name) as name'),
                DB::raw('COUNT(orders.id) as total_orders'),
                DB::raw('SUM(orders.total_amount) as total_spent') 
            )
            ->groupBy('users.id', 'users.email', 'users.name', 'customer_profiles.full_name');

        if (!empty($custYear)) {
            if (!empty($custMonth)) {
                $custDateFrom = Carbon::createFromDate($custYear, $custMonth, 1)->startOfMonth();
                $custDateTo   = Carbon::createFromDate($custYear, $custMonth, 1)->endOfMonth();
            } else {
                $custDateFrom = Carbon::createFromDate($custYear, 1, 1)->startOfYear();
                $custDateTo   = Carbon::createFromDate($custYear, 12, 31)->endOfYear();
            }
            $customerQuery->whereBetween('orders.created_at', [$custDateFrom, $custDateTo]);
        }
        
        $topCustomers = $customerQuery->orderByDesc('total_spent')->limit(5)->get();

        // ... (Các đoạn code phía trên giữ nguyên) ...

        // CẬP NHẬT: Top Products (Đã trừ hàng hoàn trả)
        $topProducts = DB::table('order_items')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->join('products', 'products.id', '=', 'order_items.product_id')
            // Join lấy ảnh
            ->leftJoin('product_images', function ($join) {
                $join->on('products.id', '=', 'product_images.product_id')
                    ->where('product_images.is_primary', 1);
            })
            // 1. Join với chi tiết hoàn trả để xem item này có bị trả không
            ->leftJoin('order_return_items', 'order_return_items.order_item_id', '=', 'order_items.id')
            // 2. Join với bảng phiếu hoàn để kiểm tra trạng thái 'completed'
            ->leftJoin('order_returns', function($join) {
                $join->on('order_returns.id', '=', 'order_return_items.order_return_id')
                     ->where('order_returns.status', 'completed');
            })
            ->whereIn('orders.status', $validStatuses)
            ->whereBetween('orders.created_at', [$dateFrom, $dateTo])
            ->select(
                'products.name',
                'product_images.image_url as product_image',
                
                // 3. Tính Số lượng thực: Tổng đặt - Tổng hoàn (nếu phiếu hoàn completed)
                DB::raw('SUM(order_items.quantity) - SUM(CASE WHEN order_returns.status = "completed" THEN COALESCE(order_return_items.quantity, 0) ELSE 0 END) as total_qty'),
                
                // 4. Tính Doanh thu thực: (Tổng đặt * giá) - (Tổng hoàn * giá)
                DB::raw('SUM(order_items.quantity * order_items.price) - SUM(CASE WHEN order_returns.status = "completed" THEN (COALESCE(order_return_items.quantity, 0) * order_items.price) ELSE 0 END) as total_rev')
            )
            ->groupBy('products.name', 'product_images.image_url')
            // 5. Chỉ lấy sản phẩm có số lượng bán thực > 0
            ->having('total_qty', '>', 0)
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get();

        // ... (Các đoạn code phía sau giữ nguyên) ...
        $revenueByCategory = DB::table('order_items')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->join('products', 'products.id', '=', 'order_items.product_id')
            ->join('categories', 'categories.id', '=', 'products.category_id')
            ->whereIn('orders.status', $validStatuses)
            ->whereBetween('orders.created_at', [$dateFrom, $dateTo])
            ->select('categories.name', DB::raw('SUM(order_items.quantity * order_items.price) as revenue'))
            ->groupBy('categories.name')
            ->orderByDesc('revenue')
            ->get();

        $chartLabels = [];
        $chartRevenue = [];
        $chartProfit = [];
        $catLabels = $revenueByCategory->pluck('name')->toArray();
        $catValues = $revenueByCategory->pluck('revenue')->toArray();

        if ($filterType === 'date') {
            $grouped = $orders->groupBy(function ($item) {
                return $item->created_at->format('d/m');
            });

            if ($dateFrom->gt($dateTo)) {
                $temp = $dateFrom; $dateFrom = $dateTo; $dateTo = $temp;
            }
            $period = \Carbon\CarbonPeriod::create($dateFrom, $dateTo);
            
            foreach ($period as $date) {
                $key = $date->format('d/m');
                $chartLabels[] = $key;
                $dailyOrders = $grouped->get($key, collect());
                $data = $this->calculateNetStats($dailyOrders);
                $chartRevenue[] = $data['revenue'];
                $chartProfit[] = $data['profit'];
            }
        } elseif ($filterType === 'month') {
            $grouped = $orders->groupBy(function ($item) {
                return $item->created_at->format('m/Y');
            });
            for ($i = 1; $i <= 12; $i++) {
                $key = str_pad($i, 2, '0', STR_PAD_LEFT) . '/' . Carbon::now()->year;
                $chartLabels[] = "Thg $i";
                $monthlyOrders = $grouped->get($key, collect());
                $data = $this->calculateNetStats($monthlyOrders);
                $chartRevenue[] = $data['revenue'];
                $chartProfit[] = $data['profit'];
            }
        } elseif ($filterType === 'quarter') {
            $grouped = $orders->groupBy(function ($item) {
                return 'Q' . ceil($item->created_at->month / 3);
            });
            for ($i = 1; $i <= 4; $i++) {
                $chartLabels[] = "Quý $i";
                $qOrders = $grouped->get('Q' . $i, collect());
                $data = $this->calculateNetStats($qOrders);
                $chartRevenue[] = $data['revenue'];
                $chartProfit[] = $data['profit'];
            }
        } elseif ($filterType === 'year') {
            $grouped = $orders->groupBy(function ($item) {
                return $item->created_at->format('Y');
            });
            $startYear = $dateFrom->year;
            $endYear = $dateTo->year;
            for ($i = $startYear; $i <= $endYear; $i++) {
                $chartLabels[] = $i;
                $yearOrders = $grouped->get($i, collect());
                $data = $this->calculateNetStats($yearOrders);
                $chartRevenue[] = $data['revenue'];
                $chartProfit[] = $data['profit'];
            }
        }

        return view('admin.revenue.index', compact(
            'totalRevenue',
            'totalProfit',
            'totalOrders',
            'totalRefunds',
            'averageOrderValue',
            'orderStatusStats',
            'topCustomers',
            'revenueByCategory',
            'catLabels',
            'catValues',
            'chartLabels',
            'chartRevenue',
            'chartProfit',
            'topProducts',
            'filterType',
            'dateFrom',
            'dateTo',
            'custMonth',
            'custYear'
        ));
    }

    private function calculateNetStats($orders)
    {
        $revenue = 0;
        $cost = 0;

        foreach ($orders as $order) {
            $revenue += $order->total_amount;

            foreach ($order->items as $item) {
                $c = 0;
                if ($item->variant) {
                    $c = $item->variant->standard_cost;
                } elseif ($item->product) {
                    $c = $item->product->standard_cost ?? 0;
                }
                $cost += $c * $item->quantity;
            }

            foreach ($order->returns ?? [] as $return) {
                if ($return->status === 'completed') {
                    $revenue -= $return->refund_amount;

                    // [THAY ĐỔI 3] Đã sửa '$return->returnItems' thành '$return->items'
                    foreach ($return->items ?? [] as $rItem) {
                        $originalItem = $rItem->orderItem;
                        if ($originalItem) {
                            $c = 0;
                            if ($originalItem->variant) {
                                $c = $originalItem->variant->standard_cost;
                            } elseif ($originalItem->product) {
                                $c = $originalItem->product->standard_cost ?? 0;
                            }
                            $cost -= ($c * $rItem->quantity);
                        }
                    }
                }
            }
        }

        return [
            'revenue' => $revenue,
            'profit' => $revenue - $cost
        ];
    }
}