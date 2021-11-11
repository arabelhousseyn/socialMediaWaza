<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Http\Controllers\loginController;
class LoginTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_example()
    {
        $data = [
            'phone' => '0699687499',
            'password' => 'hocine.12',
            'country_code' => '213'
        ];

        $this->post(route('loginApi'),$data)->assertStatus(200);
    }
}
