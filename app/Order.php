<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Order extends Model
{
  protected $fillable = [
    'user_id', 'product_id'
  ];

  public function product()
  {
    return $this->hasOne('App\Product', 'id', 'product_id');
  }

  public function user()
  {
    return $this->hasOne('App\User', 'id', 'user_id');
  }

  public function has_in_cart($product_id) {
    $items = $this->where('user_id', Auth::id())->where('product_id', $product_id)->where('status', 'pending')->get();
    return !$items->isEmpty();
  }

  public function get_cart() {
    $items = $this->where('user_id', Auth::id())->where('status', 'pending')->with('product')->get();
    return $items;
  }

  public function count_in_cart() {
    return $this->get_cart(Auth::id())->count();
  }

  public function remove_from_cart($product_id) {
    return $this->where('user_id', Auth::id())->where('product_id', $product_id)->where('status', 'pending')->update(['status' => 'removed']);
  }

  public function count_revenue($orders) {
    $total = 0;
    foreach ($orders as $key => $order) {
      $total += $order->product->price;
    }

    return $total;
  }
}
