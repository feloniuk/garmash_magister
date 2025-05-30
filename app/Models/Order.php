<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'client_id',
        'status',
        'comment'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function getOrderSum()
    {
        $orderId = $this->id;
        $fullOrder = DB::table('order_products')->where('order_id', '=', $orderId)->get();
        $products = DB::table('products')->get();
        $fullPrice = 0;

        foreach ($fullOrder as $orderPoint) {
            foreach ($products as $product) {
                if ($orderPoint->product_id === $product->id) {
                    $fullPrice += ($product->price * $orderPoint->amount);
                    continue 2;
                }
            }
        }

        $this->orderSum = $fullPrice;
    }

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'order_products')->withPivot('amount');
    }
}
