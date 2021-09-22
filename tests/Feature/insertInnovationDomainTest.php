<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
class insertInnovationDomainTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_example()
    {
        $user = User::find(24);
        $data = [
            'title' => 'FoodTechhh',
            'image' => 'test'
        ];
        $this->actingAs($user)->withSession(['foo' => 'bar'])->post(route('innovationDomains.store',$data))
        ->assertStatus(201);
    }
}
