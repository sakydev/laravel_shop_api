<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/me', [AuthController::class, 'me'])->middleware('auth:sanctum');


/* User paths */
Route::group(['middleware' => ['auth:sanctum']], function() {
  // Returns user authenticated with API (only admins can)
  Route::post('/user', function (Request $request) {
    return $request->user();
  });

  // list all products
  Route::get('/products', 'ProductController@index')->name('products');

  // show details for a single product
  Route::get('/products/{product}', 'ProductController@show')->name('product');

  // show everything in cart
  Route::get('/cart', 'OrderController@index')->name('cart');

  // add new item to cart
  Route::post('/cart', 'OrderController@store')->name('create-cart-item');

  // remove an item from cart
  Route::delete('/cart/{product_id}', 'OrderController@destroy')->name('remove-cart-item');

  // mimick checkout process
  Route::get('/checkout', 'OrderController@checkout')->name('checkout'); // nothing really happens here. Just displays success
});

Route::group(['middleware' => ['auth:sanctum', 'admin']], function() {
  /* ADMIN Area Routes */
  // show quick sales stats
  Route::get('/admin', 'AdminController@index')->name('admin')->middleware('admin');

  // lists all orders
  Route::get('/admin/orders', 'AdminController@orders_index')->name('admin-orders');

  // lists all items removed from orders
  Route::get('/admin/orders/removed', 'AdminController@orders_removed_index')->name('admin-orders-removed');
});