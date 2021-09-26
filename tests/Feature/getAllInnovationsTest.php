<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
class getAllInnovationsTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_example()
    {
        $user = User::find(24);
        $this->actingAs($user)->withSession(['foo' => 'bar'])->get(route('getInnovationByDomainApi',['id'=> 0]))
        ->assertStatus(200);
    }
}