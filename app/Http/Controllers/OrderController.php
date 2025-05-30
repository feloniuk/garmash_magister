<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Product;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{

    public function index()
    {
        $clients = DB::select("SELECT * FROM `clients`");

        $orders = Order::orderBy('id', 'desc')->paginate(10);
        foreach ($orders as $order) {
            $order->user = $order->user()->get();
            $order->getOrderSum();
        }
        return view('orders', [
            'orders' => $orders,
            'clients' => $clients,
        ]);
    }

    public function view(Order $order)
    {
        $orderInfo = DB::table('order_products')->where('order_id', '=', $order->id)->get();

        $order->fullInfo = $orderInfo;
        $products = [];
        foreach ($orderInfo as $orderPoint) {
            $product = DB::table('products')->where('id', '=', $orderPoint->product_id)->get();
            $product[0]->amount = $orderPoint->amount;
            $products[] = $product;
        }

        $order->user = $order->user()->get();
        $order->getOrderSum();

        $clients = DB::select("SELECT * FROM `clients`");

        return view('orders_view', ['order' => $order, 'products' => $products, 'clients' => $clients,]);
    }

    public function update(Order $order, Request $request)
    {
        $request->validate([
            'status' => 'required|max:255|min:4',
            'comment'  => 'max:1000',
        ]);

        $order->update($request->all());

        return redirect()->route('orders.view', $order->id)->with('message', ' Замовлення оновлено!');
    }

    public function productAdd()
    {
        $productId = \request()->get('productId');

        //check amount in stock
        $stock = DB::select("SELECT `quantity`, `id` FROM `products` WHERE `id` = {$productId} LIMIT 1");
        $amount = isset($stock[0]->quantity) ? $stock[0]->quantity : 0;

        if (!$amount)
            return response()->json(['error' => 'Товару недостатньо на складі']);

        $order = $this->getCurrentOrder();

        $order[$productId] = (isset($order[$productId]) ? $order[$productId] : 0) + 1;

        setcookie('order', json_encode($order), time() + (3600*24*30), '/');

        return response()->json($productId);
    }

    public function changeAmount()
    {
        $productId = \request()->get('productId');
        $operation = \request()->get('operation');

        $product = Product::find($productId);
        $order = $this->getCurrentOrder();

        if ($operation == '+')
            $order[$productId] = (isset($order[$productId]) ? $order[$productId] : 0) + 1;
        else {
            if ($order[$productId] == 0) {
                return response()->json(['error' => 'Неможна вказати кількість меншу за 0']);
            }
            $order[$productId] = $order[$productId] - 1;
        }

        //check amount in stock
        $stock = DB::select("SELECT `quantity`, `id` FROM `products` WHERE `id` = {$productId} LIMIT 1");
        $amount = isset($stock[0]->quantity) ? $stock[0]->quantity : 0;

        if (!$amount || $order[$productId] > $amount)
            return response()->json(['error' => 'Товару недостатньо на складі']);

        setcookie('order', json_encode($order), time() + (3600*24*30), '/');

        $response = [];
        $response['productId'] = $productId;
        $response['amount'] = $order[$productId];
        $response['sum'] = round($order[$productId] * $product->price, 2);
        $response['fullPrice'] = $this->getFullPrice($order);

        return response()->json($response);
    }

    public function productRemove()
    {
        $productId = \request()->get('productId');

        $order = $this->getCurrentOrder();
        unset($order[$productId]);

        setcookie('order', json_encode($order), time() + (3600*24*30), '/');

        return response()->json(['response' => 'product removed']);
    }

    public function cart()
    {
        $order = $this->getCurrentOrder();
        $data = \App\Models\Product::all();
        $cartProducts = [];
        if ($order) {
            foreach ($order as $k => $amount) {
                foreach ($data as $product) {
                    if ($product->id === $k) {
                        $cartProducts[$k]['id'] = $product->id;
                        $cartProducts[$k]['amount'] = $amount;
                        $cartProducts[$k]['price'] = $product->price;
                        $cartProducts[$k]['name'] = $product->title;
                        $cartProducts[$k]['image'] = $product->image;
                    }
                }
            }
        }

        return view('cart', ['order' => $cartProducts, 'fullPrice' => $this->getFullPrice()]);
    }

    public function create()
    {
        $temporaryOrder = $this->getCurrentOrder();

        $newOrder = Order::create([
            'client_id' => Auth::id(),
            'status' => 'active',
        ]);

        $insertData = [];
        foreach ($temporaryOrder as $productId => $amount) {
            $insertData[] = [
                'order_id' => $newOrder->id,
                'product_id' => $productId,
                'amount' => $amount,
                'created_at' => now(),
                'updated_at' => now(),
            ];


            DB::update("UPDATE `products` SET `quantity` = `quantity` - {$amount} WHERE `id` = {$productId}");
        }

        DB::table('order_products')->insert($insertData);

        $this->removeOrder();

        return response()->json(['success' => true]);
    }

    /**
     * @return array
     */
    private function getCurrentOrder() : array
    {
        if (isset($_COOKIE['order']))
            $order = (array)json_decode($_COOKIE['order']);
        else
            $order = [];

        return $order;
    }

    private function getFullPrice($currentOrder = false) : float
    {
        $order = $currentOrder ?: $this->getCurrentOrder();
        $data = \App\Models\Product::all();
        $fullPrice = 0;
        if ($order) {
            foreach ($order as $k => $amount) {
                foreach ($data as $product) {
                    if ($product->id === $k) {
                        $fullPrice += $amount * $product->price;
                        continue 2;
                    }
                }
            }
        }

        return round($fullPrice, 2);
    }

    private function removeOrder() : bool
    {
        if ($_COOKIE['order']) {
            unset($_COOKIE['order']);
            setcookie('order', null, -1, '/');

            return true;
        }
        return false;
    }

    public function pdf(Order $order)
    {

        $orderInfo = DB::table('order_products')->where('order_id', '=', $order->id)->get();

        $order->fullInfo = $orderInfo;
        $products = [];
        foreach ($orderInfo as $orderPoint) {
            $product = DB::table('products')->where('id', '=', $orderPoint->product_id)->get();
            $product[0]->amount = $orderPoint->amount;
            $products[] = $product;
        }

        $order->user = $order->user()->get();
        $order->getOrderSum();

        $clients = DB::select("SELECT * FROM `clients`");

        $data = ['order' => $order, 'products' => $products, 'clients' => $clients];
        $pdf = PDF::loadView('pdf', $data);
        return $pdf->stream('order.pdf');
    }

    public function products()
    {
        $clients = DB::select("SELECT * FROM `clients`");
        return view('products', ['products' => Product::all(), 'data' => Product::all(), 'clients' => $clients]);
    }

    public function search(Request $request)
    {
        $search = $request->get('search');

        if (!$search) {
            $products = Product::all();
        } else {
            $products = Product::where('code', '=', $search)->get();
        }

        return response(json_encode($products), 200);
    }

    public function forecast()
    {
        $clients = DB::select("SELECT * FROM `clients`");
        $products = Product::all();

        return view('forecast', compact('clients', 'products'));
    }

    public function forecastView(Product $product)
    {
        $clients = DB::select("SELECT * FROM `clients`");

        $salesData = OrderProduct::where('product_id', $product->id)
            ->join('orders', 'orders.id', '=', 'order_products.order_id')
            ->where('orders.status', 'apply')
            ->select(DB::raw('DATE(orders.created_at) as date'), DB::raw('SUM(order_products.amount) as sales'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $sales = [];
        $warning = '';

        if ($salesData->isNotEmpty()) {
            $sales = [
                'dates' => $salesData->pluck('date')->toArray(),
                'sales' => $salesData->pluck('sales')->toArray(),
            ];

            if (count($sales['dates']) < 5) {
                $warning = 'Відсутня достатня кількість даних для прогнозування цього товару. Рекомендується мінімум 5 часових точок записів продажів.';
            }
        } else {
            $warning = 'Відсутні дані для прогнозування цього товару.';
        }

        return view('forecast_view', compact('clients', 'product', 'sales', 'warning'));
    }
}
