<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
//        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $clients = DB::select("SELECT * FROM `clients`");

        $data = Product::active()->get();
        //check order
        $order = isset($_COOKIE['order']) ? (array)json_decode($_COOKIE['order']) : [];

        $productIds = array_keys($order);
        foreach ($data as $product) {
            $product->ordered = false;
            if (in_array($product->id, $productIds)) {
                $product->ordered = true;
            }
        }

        return view('welcome', ['data' => $data, 'clients' => $clients]);
    }

    /**
     * Show individual product page
     */
    public function showProduct(Product $product)
    {
        $clients = DB::select("SELECT * FROM `clients`");

        // Check if product is in current order
        $order = isset($_COOKIE['order']) ? (array)json_decode($_COOKIE['order']) : [];
        $product->ordered = in_array($product->id, array_keys($order));

        return view('product_show', [
            'product' => $product,
            'clients' => $clients,
            'data' => Product::all() // для сайдбара
        ]);
    }

    public function users()
    {
        $users = User::all()->except(Auth::id());
        $clients = DB::select("SELECT * FROM `clients`");

        return view('users', ['users' => $users, 'clients' => $clients, 'data' => Product::all()]);
    }

    public function profile()
    {
        if (Auth::user()->role !== 'user')
            return redirect(route('home'));

        $user = User::find(Auth::id());

        return view('profile', ['user' => $user]);
    }
}