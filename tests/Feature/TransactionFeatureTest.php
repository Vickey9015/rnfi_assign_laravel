<?php
namespace Tests\Feature;

use App\Models\User;
use App\Models\Transaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TransactionFeatureTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function authenticated_user_can_create_a_transaction()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/transactions', [
            'title' => 'Payment',
            'content' => 'Payment for services',
            'amount' => 200.00,
            'description' => 'Test transaction',
        ]);

        $response->assertStatus(201)
                 ->assertJson([
                     'message' => 'Transaction created successfully',
                 ]);

        $this->assertDatabaseHas('transactions', [
            'user_id' => $user->id,
            'amount' => 200.00,
            'description' => 'Test transaction',
        ]);
    }

    /** @test */
    public function unauthenticated_user_cannot_create_a_transaction()
    {
        $response = $this->postJson('/api/transactions', [
            'title' => 'Payment',
            'content' => 'Payment for services',
            'amount' => 100.50,
            'description' => 'Unauthorized transaction',
        ]);

        $response->assertStatus(401);
    }

    /** @test */
    public function authenticated_user_can_view_their_transactions()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        Transaction::factory()->create(['user_id' => $user->id]);

        $response = $this->getJson('/api/transactions');

        $response->assertStatus(200)
                 ->assertJsonStructure(['transactions']);
    }

    /** @test */
    public function unauthenticated_user_cannot_view_transactions()
    {
        $response = $this->getJson('/api/transactions');

        $response->assertStatus(401);
    }

    /** @test */
    public function authenticated_user_can_view_a_single_transaction()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $transaction = Transaction::factory()->create(['user_id' => $user->id]);

        $response = $this->getJson("/api/transactions/{$transaction->id}");

        $response->assertStatus(200)
                 ->assertJson(['transaction' => [
                     'id' => $transaction->id,
                     'amount' => $transaction->amount,
                 ]]);
    }

    /** @test */
    public function user_cannot_access_other_users_transactions()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        Sanctum::actingAs($user1);

        $transaction = Transaction::factory()->create(['user_id' => $user2->id]);

        $response = $this->getJson("/api/transactions/{$transaction->id}");

        $response->assertStatus(404)
                 ->assertJson(['error' => 'Transaction not found or unauthorized.']);
    }
}
