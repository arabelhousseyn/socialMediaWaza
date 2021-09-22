<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
class innovationTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_example()
    {
        $data = [
            'title' => 'testtest',
            'description' => 'hello hello hello',
            'innovation_domain_id' => 1,
            'type' => 0,
        ];
        $user = User::find(24);
        $this->actingAs($user)->withSession(['foo' => 'bar'])->post(route('innovations.store',$data))
        ->assertStatus(200);
    }
}
