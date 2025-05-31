<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Product;
use App\Models\User;
use App\Models\Material;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    /**
     * Display analytics dashboard
     */
    public function index()
    {
        return view('admin.analytics.index');
    }

    /**
     * Get clustering data based on criteria
     */
    public function getClusteringData(Request $request)
    {
        $criteria = $request->get('criteria', 'products');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');
        $status = $request->get('status', 'apply');

        $query = Order::query()
            ->where('status', $status)
            ->with(['user', 'products']);

        if ($dateFrom) {
            $query->whereDate('created_at', '>=', $dateFrom);
        }

        if ($dateTo) {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        $orders = $query->get();

        switch ($criteria) {
            case 'products':
                return $this->getProductClustering($orders);
            case 'prices':
                return $this->getPriceClustering($orders);
            case 'materials':
                return $this->getMaterialClustering($orders);
            case 'regions':
                return $this->getRegionClustering($orders);
            case 'time':
                return $this->getTimeClustering($orders);
            default:
                return response()->json(['error' => 'Invalid criteria'], 400);
        }
    }

    /**
     * Product-based clustering
     */
    private function getProductClustering($orders)
    {
        $productData = [];
        $totalRevenue = 0;

        foreach ($orders as $order) {
            foreach ($order->products as $product) {
                $productId = $product->id;
                $quantity = $product->pivot->amount;
                $revenue = $quantity * $product->price;

                if (!isset($productData[$productId])) {
                    $productData[$productId] = [
                        'name' => $product->title,
                        'quantity' => 0,
                        'revenue' => 0,
                        'orders_count' => 0,
                        'composition' => $product->composition ?? []
                    ];
                }

                $productData[$productId]['quantity'] += $quantity;
                $productData[$productId]['revenue'] += $revenue;
                $productData[$productId]['orders_count']++;
                $totalRevenue += $revenue;
            }
        }

        // Calculate clustering based on revenue and quantity
        $clusters = [
            'high_revenue_high_volume' => [],
            'high_revenue_low_volume' => [],
            'low_revenue_high_volume' => [],
            'low_revenue_low_volume' => []
        ];

        if (count($productData) > 0) {
            $avgRevenue = $totalRevenue / count($productData);
            $avgQuantity = array_sum(array_column($productData, 'quantity')) / count($productData);

            foreach ($productData as $id => $data) {
                $clusterKey = ($data['revenue'] >= $avgRevenue ? 'high_revenue' : 'low_revenue') . '_' .
                             ($data['quantity'] >= $avgQuantity ? 'high_volume' : 'low_volume');
                
                $clusters[$clusterKey][] = array_merge($data, ['id' => $id]);
            }
        }

        return response()->json([
            'clusters' => $clusters,
            'summary' => [
                'total_products' => count($productData),
                'total_revenue' => $totalRevenue,
                'avg_revenue' => count($productData) > 0 ? $totalRevenue / count($productData) : 0,
                'avg_quantity' => count($productData) > 0 ? array_sum(array_column($productData, 'quantity')) / count($productData) : 0
            ],
            'chart_data' => $this->prepareChartData($productData, 'products')
        ]);
    }

    /**
     * Price-based clustering
     */
    private function getPriceClustering($orders)
    {
        $priceRanges = [
            '0-25' => ['min' => 0, 'max' => 25, 'count' => 0, 'orders' => []],
            '25-50' => ['min' => 25, 'max' => 50, 'count' => 0, 'orders' => []],
            '50-100' => ['min' => 50, 'max' => 100, 'count' => 0, 'orders' => []],
            '100+' => ['min' => 100, 'max' => PHP_INT_MAX, 'count' => 0, 'orders' => []]
        ];

        foreach ($orders as $order) {
            $orderTotal = 0;
            foreach ($order->products as $product) {
                $orderTotal += $product->pivot->amount * $product->price;
            }

            foreach ($priceRanges as $range => &$data) {
                if ($orderTotal >= $data['min'] && $orderTotal < $data['max']) {
                    $data['count']++;
                    $data['orders'][] = [
                        'id' => $order->id,
                        'total' => $orderTotal,
                        'client' => $order->user->name ?? 'Unknown',
                        'date' => $order->created_at->format('Y-m-d')
                    ];
                    break;
                }
            }
        }

        return response()->json([
            'clusters' => $priceRanges,
            'chart_data' => $this->prepareChartData($priceRanges, 'prices')
        ]);
    }

    /**
     * Material-based clustering (ИСПРАВЛЕННЫЙ МЕТОД)
     */
    private function getMaterialClustering($orders)
    {
        $materialData = [];
        $totalOrdersProcessed = 0;

        foreach ($orders as $order) {
            foreach ($order->products as $product) {
                $quantity = $product->pivot->amount;
                $totalOrdersProcessed++;

                if ($product->composition && is_array($product->composition)) {
                    foreach ($product->composition as $material => $percentage) {
                        if ($percentage > 0) {
                            if (!isset($materialData[$material])) {
                                $materialData[$material] = [
                                    'name' => ucfirst($material),
                                    'weighted_percentage' => 0, // Взвешенный процент
                                    'total_usage_weight' => 0,   // Общий вес использования
                                    'product_count' => 0,        // Количество уникальных продуктов
                                    'order_count' => 0,          // Количество заказов
                                    'products_used' => []        // Список продуктов где используется
                                ];
                            }

                            // Взвешиваем процент на количество заказанного продукта
                            $materialData[$material]['weighted_percentage'] += ($percentage * $quantity);
                            $materialData[$material]['total_usage_weight'] += $quantity;
                            $materialData[$material]['order_count']++;
                            
                            // Добавляем продукт в список, если его еще нет
                            if (!in_array($product->id, $materialData[$material]['products_used'])) {
                                $materialData[$material]['products_used'][] = $product->id;
                                $materialData[$material]['product_count']++;
                            }
                        }
                    }
                }
            }
        }

        // Рассчитываем итоговые проценты
        foreach ($materialData as $material => &$data) {
            if ($data['total_usage_weight'] > 0) {
                // Средний процент с учетом веса заказов
                $data['average_percentage'] = round($data['weighted_percentage'] / $data['total_usage_weight'], 2);
                
                // Процент от общего использования в заказах
                $data['usage_percentage'] = round(($data['order_count'] / $totalOrdersProcessed) * 100, 2);
                
                // Убираем временные поля
                unset($data['weighted_percentage'], $data['total_usage_weight'], $data['products_used']);
            } else {
                $data['average_percentage'] = 0;
                $data['usage_percentage'] = 0;
            }
        }

        // Сортируем по среднему проценту
        uasort($materialData, function($a, $b) {
            return $b['average_percentage'] <=> $a['average_percentage'];
        });

        return response()->json([
            'clusters' => $materialData,
            'summary' => [
                'total_materials' => count($materialData),
                'total_orders_processed' => $totalOrdersProcessed,
                'materials_found' => array_keys($materialData)
            ],
            'chart_data' => $this->prepareChartData($materialData, 'materials')
        ]);
    }

    /**
     * Region-based clustering (simplified - based on user data)
     */
    private function getRegionClustering($orders)
    {
        $regionData = [];

        foreach ($orders as $order) {
            // Extract region from user's address or email domain
            $region = $this->extractRegion($order->user);
            
            if (!isset($regionData[$region])) {
                $regionData[$region] = [
                    'name' => $region,
                    'orders_count' => 0,
                    'revenue' => 0,
                    'customers' => []
                ];
            }

            $orderTotal = 0;
            foreach ($order->products as $product) {
                $orderTotal += $product->pivot->amount * $product->price;
            }

            $regionData[$region]['orders_count']++;
            $regionData[$region]['revenue'] += $orderTotal;
            
            if (!in_array($order->user->id, $regionData[$region]['customers'])) {
                $regionData[$region]['customers'][] = $order->user->id;
            }
        }

        // Convert customers array to count
        foreach ($regionData as &$data) {
            $data['customers_count'] = count($data['customers']);
            unset($data['customers']);
        }

        return response()->json([
            'clusters' => $regionData,
            'chart_data' => $this->prepareChartData($regionData, 'regions')
        ]);
    }

    /**
     * Time-based clustering
     */
    private function getTimeClustering($orders)
    {
        $timeData = [];

        foreach ($orders as $order) {
            $month = $order->created_at->format('Y-m');
            $dayOfWeek = $order->created_at->format('l');
            $hour = $order->created_at->format('H');

            // Monthly clustering
            if (!isset($timeData['monthly'][$month])) {
                $timeData['monthly'][$month] = ['orders' => 0, 'revenue' => 0];
            }

            // Weekly clustering
            if (!isset($timeData['weekly'][$dayOfWeek])) {
                $timeData['weekly'][$dayOfWeek] = ['orders' => 0, 'revenue' => 0];
            }

            // Hourly clustering
            if (!isset($timeData['hourly'][$hour])) {
                $timeData['hourly'][$hour] = ['orders' => 0, 'revenue' => 0];
            }

            $orderTotal = 0;
            foreach ($order->products as $product) {
                $orderTotal += $product->pivot->amount * $product->price;
            }

            $timeData['monthly'][$month]['orders']++;
            $timeData['monthly'][$month]['revenue'] += $orderTotal;
            
            $timeData['weekly'][$dayOfWeek]['orders']++;
            $timeData['weekly'][$dayOfWeek]['revenue'] += $orderTotal;
            
            $timeData['hourly'][$hour]['orders']++;
            $timeData['hourly'][$hour]['revenue'] += $orderTotal;
        }

        return response()->json([
            'clusters' => $timeData,
            'chart_data' => $this->prepareChartData($timeData, 'time')
        ]);
    }

    /**
     * Extract region from user data
     */
    private function extractRegion($user)
    {
        // Try to get region from clients table
        $client = DB::table('clients')->where('user_id', $user->id)->first();
        
        if ($client && $client->address) {
            if (strpos($client->address, 'Одеса') !== false) {
                return 'Одеса';
            } elseif (strpos($client->address, 'Київ') !== false) {
                return 'Київ';
            } elseif (strpos($client->address, 'Харків') !== false) {
                return 'Харків';
            } elseif (strpos($client->address, 'Одеська') !== false) {
                return 'Одеська область';
            }
        }

        // Default fallback
        return 'Інші регіони';
    }

    /**
     * Prepare chart data for visualization (ОБНОВЛЕННЫЙ МЕТОД)
     */
    private function prepareChartData($data, $type)
    {
        switch ($type) {
            case 'products':
                return [
                    'labels' => array_column($data, 'name'),
                    'datasets' => [
                        [
                            'label' => 'Виручка (грн)',
                            'data' => array_column($data, 'revenue'),
                            'backgroundColor' => 'rgba(54, 162, 235, 0.2)',
                            'borderColor' => 'rgba(54, 162, 235, 1)'
                        ],
                        [
                            'label' => 'Кількість',
                            'data' => array_column($data, 'quantity'),
                            'backgroundColor' => 'rgba(255, 99, 132, 0.2)',
                            'borderColor' => 'rgba(255, 99, 132, 1)'
                        ]
                    ]
                ];

            case 'prices':
                return [
                    'labels' => array_keys($data),
                    'data' => array_column($data, 'count'),
                    'backgroundColor' => [
                        'rgba(255, 99, 132, 0.8)',
                        'rgba(54, 162, 235, 0.8)',
                        'rgba(255, 205, 86, 0.8)',
                        'rgba(75, 192, 192, 0.8)'
                    ]
                ];

            case 'materials':
                return [
                    'labels' => array_column($data, 'name'),
                    'data' => array_column($data, 'average_percentage'), // Используем средний процент
                    'backgroundColor' => 'rgba(75, 192, 192, 0.8)'
                ];

            case 'regions':
                return [
                    'labels' => array_column($data, 'name'),
                    'datasets' => [
                        [
                            'label' => 'Кількість замовлень',
                            'data' => array_column($data, 'orders_count'),
                            'backgroundColor' => 'rgba(153, 102, 255, 0.8)'
                        ],
                        [
                            'label' => 'Виручка (грн)',
                            'data' => array_column($data, 'revenue'),
                            'backgroundColor' => 'rgba(255, 159, 64, 0.8)'
                        ]
                    ]
                ];

            default:
                return [];
        }
    }
}