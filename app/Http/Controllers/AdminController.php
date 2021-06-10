<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Order;
class AdminController extends Controller
{
  function index() {
    $response = [];
    $order = new Order;

    $sold_products = $order->where('status', 'sold')->with('product')->get();
    $response['completed']['amount'] = count($sold_products);
    $response['completed']['revenue'] = $order->count_revenue($sold_products);

    $pending_products = $order->where('status', 'pending')->with('product')->get();
    $pending['pending']['amount'] = count($pending_products);
    $pending['pending']['revenue'] = $order->count_revenue($pending_products);

    $expired_products = $order->where('status', 'expired')->with('product')->get();
    $expired['expired']['amount'] = count($expired_products);
    $expired['expired']['revenue'] = $order->count_revenue($expired_products);

    return api_response($response, 200);
  }

  public function orders_index()
  {
    return api_response(Order::all(), 200);
  }

  public function orders_removed_index()
  {
    $orders = Order::where('status', 'removed')->with('user')->get();
    return api_response($orders, 200);
  }
}
