<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use App\Order;
use App\User;
use App\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
  private function product_exists($product_id)
  {
    return Product::exists('id', $product_id);
  }

  private function validate_product($requestData)
  {
    $validator = Validator::make($requestData, [
      'product_id' => ['required', 'integer']
    ]);

    if ($validator->fails()) {
      $fieldsWithErrors = $validator->messages()->get('*');
      return api_response($fieldsWithErrors, 400, 'error: incomplete / unexpected input');
    }

    if (!$this->product_exists($requestData['product_id'])) {
      return api_response($requestData, 401, 'Product not found');
    }
  }
  
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index(Request $request)
  {
    $order = new Order;
    $orders = $order->get_cart();
    $cart_total = $order->count_revenue($orders);

    $response['orders'] = $orders;
    $response['cart_total'] = $cart_total;

    return api_response($response, 200);
  }

  // Normally I would have a proper payment integrations
  // and handle checkout the right way. In this case, 
  // since our main focus is simulation, I'll just display 
  // success message
  public function checkout(Request $request) 
  {
    $errors = $success = [];
    $checkout = Order::where('user_id', Auth::id())->where('status', 'pending')->update(['status' => 'sold']);

    if (!$checkout) {
      return api_response(false, 400, 'error: Unable to proccess order');
    } else {
      return api_response(false, 200, 'Checkout successful');
    }
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {
    $validator = $this->validate_product($request->all());
    if (!empty($validator)) {
      return $validator;
    }

    $order = new Order;
    $errors = [];

    $product_id = $request->product_id;
    $has_in_cart = $order->has_in_cart($product_id);

    if (!$has_in_cart) {
        $created = Order::create([
        'user_id' => Auth::id(),
        'product_id' => $product_id
      ]);

      if (!$created) {
        return api_response(compact('product_id'), 400, 'error: failed to add to cart: unknown problem');
      } else {
        // If user previously added this product
        // but later removed, we still have this saved
        // as "removed". Since, user is now adding same
        // product again, let's remove that previous
        // entry
        Order::where('user_id', Auth::id())->where('product_id', $product_id)->where('status', 'removed')->delete();
        return api_response(compact('product_id'), 201, 'Product added to cart successfully');
      }
    }
    
    return api_response(compact('product_id'), 400, 'error: failed to add to cart: already exists');
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  \App\Order  $order
   * @return \Illuminate\Http\Response
   */
  public function destroy(Request $request)
  {
    $validator = $this->validate_product($request->all());
    if (!empty($validator)) {
      return $validator;
    } 

    $order = new Order;
    $product_id = $request->product_id;
    $has_in_cart = $order->has_in_cart(Auth::id(), $product_id);
    if (!$has_in_cart) {
      return api_response(compact('product_id'), 400, 'error: failed to remove from cart: item doesn\'t exist'); 
    }

    $destroyed = $order->remove_from_cart(Auth::id(), $product_id);
    if ($destroyed) { 
      return api_response([
        'product_id' => $product_id], 200, 'Successfully removed from cart'); 
    }
  }
}
