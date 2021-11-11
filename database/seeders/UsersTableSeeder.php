<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'gendre' => 0,
            'profession' => 'web developer',
            'wilaya_id' => 1,
            'phone' => '0699687499',
            'email' => 'potency.football@gmail.com',
            'is_freelancer' => 0,
            'receive_ads' => 0,
            'password' => Hash::make('password')
        ]);
    }
}
