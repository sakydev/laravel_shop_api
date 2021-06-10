<?php

use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      $today = date("Y/m/d H:i:s");
      DB::table('products')->insert([
        'name' => 'Pioneer DJ Mixer',
        'price' => 699,
        'created_at' => $today,
        'updated_at' => $today
      ]);

      DB::table('products')->insert([
        'name' => 'Roland Wave Sampler',
        'price' => 485,
        'created_at' => $today,
        'updated_at' => $today
      ]);

      DB::table('products')->insert([
        'name' => 'Reloop Headphone',
        'price' => 159,
        'created_at' => $today,
        'updated_at' => $today
      ]);

      DB::table('products')->insert([
        'name' => 'Rokit Monitor',
        'price' => 189.9,
        'created_at' => $today,
        'updated_at' => $today
      ]);

      DB::table('products')->insert([
        'name' => 'Fisherprice Baby Mixer',
        'price' => 120,
        'created_at' => $today,
        'updated_at' => $today
      ]);
    }
}
