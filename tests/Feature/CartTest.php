<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Product;
use App\User;
use App\Order;

class CartTest extends TestCase
{
  use RefreshDatabase;
  
  public function testsAddToCartSuccessfully()
  {
    $user = factory(User::class)->create();
    $product = factory(Product::class)->create(); 

    $token = $user->createToken('auth_token')->plainTextToken;
    $payload = ['user_id' => $user->id, 'product_id' => $product->id];

    $headers = ['Authorization' => "Bearer $token"];

    $this->json('POST', 'api/cart', $payload, $headers)
    ->assertStatus(201)
    ->assertJsonStructure([
      'data' => [
        'product_id'
      ],
    ]);
  }

  public function testsAddToCartProductInteger()
  {
    $user = factory(User::class)->create();
    $token = $user->createToken('auth_token')->plainTextToken;
    $payload = ['user_id' => $user->id, 'product_id' => 'hello'];

    $headers = ['Authorization' => "Bearer $token"];

    $this->json('POST', 'api/cart', $payload, $headers)
    ->assertStatus(400)
    ->assertJsonStructure([
      "data" => [
        "product_id" 
      ],
    ]);
  }

  public function testsAddToCartProductNotFound()
  {
    $user = factory(User::class)->create();
    $token = $user->createToken('auth_token')->plainTextToken;
    $payload = ['user_id' => $user->id, 'product_id' => 99999999];

    $headers = ['Authorization' => "Bearer $token"];

    $this->json('POST', 'api/cart', $payload, $headers)
    ->assertStatus(401)
    ->assertJsonStructure([
      "data" => [
        "product_id" 
      ],
    ]);
  }

  public function testsGetCartSuccessfully()
  {
    $user = factory(User::class)->create();
    for ($i=0; $i < 5; $i++) { 
       $product = factory(Product::class)->create();
       $order = factory(Order::class)->create([
        'user_id' => $user->id,
        'product_id' => $product->id
       ]);
     } 

    $token = $user->createToken('auth_token')->plainTextToken;
    $payload = [];

    $headers = ['Authorization' => "Bearer $token"];

    $this->json('GET', 'api/cart', $payload, $headers)
    ->assertStatus(200)
    ->assertJsonStructure([
      'data' => [
        'orders',
        'cart_total'
      ],
    ]);
  }
}
