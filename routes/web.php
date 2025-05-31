<?php

use App\Models\Product;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [\App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Public product view
Route::get('/product/{product}', [\App\Http\Controllers\HomeController::class, 'showProduct'])->name('product.show');

// Search routes
Route::get('products', [\App\Http\Controllers\OrderController::class, 'products'])->name('products')->middleware('admins');
Route::post('/search', [App\Http\Controllers\OrderController::class, 'search'])->name('products.search')->middleware('admins');

// User management
Route::get('/users', [\App\Http\Controllers\HomeController::class, 'users'])->name('users')->middleware('auth')->middleware('admins');
Route::get('/profile', [\App\Http\Controllers\HomeController::class, 'profile'])->name('profile')->middleware('auth');

// Cart and orders
Route::post('/product-add', [App\Http\Controllers\OrderController::class, 'productAdd'])->name('product.add');
Route::post('/change-amount', [App\Http\Controllers\OrderController::class, 'changeAmount'])->name('product.change-amount');
Route::post('/product-remove', [App\Http\Controllers\OrderController::class, 'productRemove'])->name('product.remove');
Route::get('/cart', [App\Http\Controllers\OrderController::class, 'cart'])->name('product.cart');
Route::post('/create-order', [App\Http\Controllers\OrderController::class, 'create'])->name('product.create-order');

// Materials
Route::get('/materials', [App\Http\Controllers\MaterialController::class, 'index'])->name('materials');
Route::post('/material-add', [App\Http\Controllers\MaterialController::class, 'materialAdd'])->name('material.add');
Route::post('/material-change-amount', [App\Http\Controllers\MaterialController::class, 'changeAmount'])->name('material.change-amount');
Route::post('/material-remove', [App\Http\Controllers\MaterialController::class, 'materialRemove'])->name('material.remove');
Route::get('/material-cart', [App\Http\Controllers\MaterialController::class, 'cart'])->name('material.cart');
Route::post('/material-create-order', [App\Http\Controllers\MaterialController::class, 'create'])->name('material.create-order');

// Order management
Route::get('/orders', [\App\Http\Controllers\OrderController::class, 'index'])->name('orders')->middleware('auth')->middleware('admins');
Route::get('/order/{order}', [App\Http\Controllers\OrderController::class, 'view'])->name('orders.view')->middleware('admins');
Route::put('/order/{order}', [App\Http\Controllers\OrderController::class, 'update'])->name('orders.update')->middleware('admins');
Route::get('/pdf/{order}', [\App\Http\Controllers\OrderController::class, 'pdf'])->name('pdf');

// Forecasting
Route::get('/forecast', [\App\Http\Controllers\OrderController::class, 'forecast'])->name('order.forecast')->middleware('admins');
Route::get('/forecast_view/{product}', [\App\Http\Controllers\OrderController::class, 'forecastView'])->name('order.forecast.view')->middleware('admins');

// Video functionality
Route::get('/web', function () {
    return view('web', ['data' => Product::all()]);
})->name('web')->middleware('admins');

// Admin routes group
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admins'])->group(function() {

    // Product management
    Route::resource('products', App\Http\Controllers\Admin\ProductController::class);
    Route::patch('products/{product}/toggle-status', [App\Http\Controllers\Admin\ProductController::class, 'toggleStatus'])
        ->name('products.toggle-status');

    // Analytics and clustering
    Route::get('analytics', [App\Http\Controllers\Admin\AnalyticsController::class, 'index'])
        ->name('analytics.index');
    Route::get('analytics/data', [App\Http\Controllers\Admin\AnalyticsController::class, 'getClusteringData'])
        ->name('analytics.data');
});

Auth::routes();