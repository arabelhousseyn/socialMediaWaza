<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_example()
    {
        $data = [
            'fullName' => 'hocine',
            'dob' => '1999-06-20',
            'picture' => 'test',
            'gender' => '1',
            "profession" => 'web developer',
            'wilaya_id' => '1',
            'phone' => '0799687499',
            'email' => 'mowaf93331@soulsuns.com',
            'password' => 'hocine.123',
            'is_freelancer' => '0',
            'receive_ads' => '1',
            'hide_phone' => '1'
        ];
        $response = $this->post(route('registerApi'),$data);

        $response->assertStatus(200);
    }
}
