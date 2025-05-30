<?php

namespace App\Http\Controllers;

use App\Models\Material;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MaterialController extends Controller
{
    public function index()
    {
        $materials = Material::all();
        //check order
        $order = isset($_COOKIE['order_materials']) ? (array)json_decode($_COOKIE['order_materials']) : [];

        $productIds = array_keys($order);
        foreach ($materials as $product) {
            $product->ordered = false;
            if (in_array($product->id, $productIds)) {
                $product->ordered = true;
            }
        }

        $data = Product::all();
        return view('materials', [
            'materials' => $materials,
            'data' => $data
        ]);
    }

    public function materialAdd()
    {
        $productId = \request()->get('productId');

        $order = $this->getCurrentOrder();

        $order[$productId] = (isset($order[$productId]) ? $order[$productId] : 0) + 1;

        setcookie('order_materials', json_encode($order), time() + (3600*24*30), '/');

        return response()->json($productId);
    }

    public function changeAmount()
    {
        $productId = \request()->get('productId');
        $operation = \request()->get('operation');

        $product = Material::find($productId);
        $order = $this->getCurrentOrder();

        if ($operation == '+')
            $order[$productId] = (isset($order[$productId]) ? $order[$productId] : 0) + 1;
        else {
            if ($order[$productId] == 0) {
                return response()->json(['error' => 'Неможна вказати кількість меншу за 0']);
            }
            $order[$productId] = $order[$productId] - 1;
        }

        setcookie('order_materials', json_encode($order), time() + (3600*24*30), '/');

        $response = [];
        $response['productId'] = $productId;
        $response['amount'] = $order[$productId];
        $response['sum'] = round($order[$productId] * $product->price, 2);
        $response['fullPrice'] = $this->getFullPrice($order);

        return response()->json($response);
    }

    public function materialRemove()
    {
        $productId = \request()->get('productId');

        $order = $this->getCurrentOrder();
        unset($order[$productId]);

        setcookie('order_materials', json_encode($order), time() + (3600*24*30), '/');

        return response()->json(['response' => 'сировину видалено']);
    }

    public function cart()
    {
        $order = $this->getCurrentOrder();
        $data = \App\Models\Material::all();
        $cartProducts = [];
        if ($order) {
            foreach ($order as $k => $amount) {
                foreach ($data as $product) {
                    if ($product->id === $k) {
                        $cartProducts[$k]['id'] = $product->id;
                        $cartProducts[$k]['amount'] = $amount;
                        $cartProducts[$k]['price'] = $product->price;
                        $cartProducts[$k]['name'] = $product->title;
                    }
                }
            }
        }
        $data = Product::all();
        return view('cart_materials', [
            'order' => $cartProducts,
            'fullPrice' => $this->getFullPrice(),
            'data' => $data
        ]);
    }

    public function create()
    {
        $temporaryOrder = $this->getCurrentOrder();

        $newOrderId = DB::table('materials_orders')->insertGetId(['status' => 'active']);

        $insertData = [];
        foreach ($temporaryOrder as $productId => $amount) {
            $insertData[] = [
                'order_id' => $newOrderId,
                'material_id' => $productId,
                'amount' => $amount,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('materials_order_products')->insert($insertData);

        $this->removeOrder();

        return response()->json(['success' => true]);
    }

    /**
     * @return array
     */
    private function getCurrentOrder() : array
    {
        if (isset($_COOKIE['order_materials']))
            $order = (array)json_decode($_COOKIE['order_materials']);
        else
            $order = [];

        return $order;
    }

    private function getFullPrice($currentOrder = false) : float
    {
        $order = $currentOrder ?: $this->getCurrentOrder();
        $data = \App\Models\Material::all();
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
        if ($_COOKIE['order_materials']) {
            unset($_COOKIE['order_materials']);
            setcookie('order_materials', null, -1, '/');

            return true;
        }
        return false;
    }
}
