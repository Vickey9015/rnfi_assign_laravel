<?php

namespace Tests\Unit;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_a_transaction()
    {
        $user = User::factory()->create();

        $transaction = Transaction::create([
            'user_id' => $user->id,
            'amount' => 250.50,
            'description' => 'Test Transaction',
            'title' => 'Payment',
            'content' => 'Payment for service',
        ]);

        $this->assertDatabaseHas('transactions', [
            'user_id' => $user->id,
            'amount' => 250.50,
            'description' => 'Test Transaction',
        ]);
    }

    /** @test */
    public function it_belongs_to_a_user()
    {
        $user = User::factory()->create();
        $transaction = Transaction::factory()->create(['user_id' => $user->id]);

        $this->assertEquals($user->id, $transaction->user_id);
    }
}
