<?php

namespace App\Console\Commands;

use App\Order;
use Illuminate\Console\Command;

class TargetProductsRemovedFromCart extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'target:removed';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This gives us the products that a user added to cart but later removed before checkout. We want to use this to do targeted discounts.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
      parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
      // We can either send a grouped discount 
      // Or separate email for each. I'll be going
      // with the separate emails approach for this 
      // If we wanted to do a grouped mail instead
      // where we offer a discount for all the products
      // they have removed, we'll simply select * from
      // orders where user=user and status=removed. 
      $remove_orders = Order::where('status', 'removed')->with('user', 'product')->get();
      foreach ($remove_orders as $key => $order) {
        $productName = $order->product->name;
        $original_price = $order->product->price;
        $discounted_price = ($original_price / 2); // give 50% discount
        $username = $order->user->name;
        $email = $order->user->email;

        $message = "Hi {$username},\nYou added \"{$productName}\" to your basked for ($){$original_price} but later removed. We offer you a whooping 50% discount and you can purchase same item at ($){$discounted_price} price. Hurry before we change our mind :D";
        exit($message);

        // email implementation can go here. Please
        // let me know if you'd like me to do that as well
      }

      return 0;
    }
  }
