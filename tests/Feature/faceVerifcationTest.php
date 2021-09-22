<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class faceVerifcationTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_example()
    {
        $data = [
            'image1' => 'haha',
            'image2' => 'haha'
        ];

         $this->post(route('faceVerificationApi'),$data)->assertStatus(200);
    }
}
