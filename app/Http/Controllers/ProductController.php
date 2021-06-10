<?php

namespace App\Http\Controllers;

use App\Order;
use App\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{

  private function validateNameAndPrice($requestData, $update = false)
  {
    $name_rule = $update ? ['required', 'string'] : ['required', 'string', 'unique:products'];
    $validator = Validator::make($requestData, [
      'name' => $name_rule,
      'price' => ['required', 'integer']
    ]);

    if ($validator->fails()) {
      $fieldsWithErrors = $validator->messages()->get('*');
      return api_response($fieldsWithErrors, 400, 'error: incomplete / unexpected input');
    }
  }
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    $products = Product::all();
    return api_response($products, 200);
  }

  public function show(Product $product)
  {
    return api_response($product, 200);
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {
    $validated = $this->validateNameAndPrice($request->all());
    if (!empty($validated)) {
      return $validated;
    }

    $created = Product::insert([
      'name' => $request->name,
      'price' => $request->price
    ]);

    if ($created) {
      return api_response($created, 201, 'Product added successfully');
    }

    return api_response(false, 400, 'error: Something went wrong trying to add product');
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \App\Product  $product
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, Product $product)
  {
    $validated = $this->validateNameAndPrice($request->all(), 'update');
    if (!empty($validated)) {
      return $validated;
    }
      
    $product_id = $product->id;
    Product::where('id', $product_id)->update([
      'name' => $request->name,
      'price' => $request->price
    ]);
    return api_response(compact('product_id'), 200, 'Product updated successfully');
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  \App\Product  $product
   * @return \Illuminate\Http\Response
   */
  public function destroy(Product $product)
  {
    $orders = new Order;
    $pending_orders = $orders->where('product_id', $product->id)->where('status', 'pending')->get();
    if ($pending_orders->isEmpty()) {
      Product::where('id', $product->id)->delete();
      
      return api_response($product->id, 200, 'Product has been deleted');
    }

    return api_response($products->id, 400, 'error: Cannot delete a product with pending orders');
  }
}
