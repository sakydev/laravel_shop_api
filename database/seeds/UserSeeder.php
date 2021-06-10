<?php

use Illuminate\Database\Seeder;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::create([
          'name' => 'Saqib',
          'email' => 'saqib@saqib.com',
          'password' => Hash::make('password'),
          'level' => 1
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;
    }
}
