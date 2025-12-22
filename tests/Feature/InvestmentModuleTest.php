<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Investment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class InvestmentModuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_investment()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post(route('investments.store'), [
            'name' => 'Apple Stock',
            'type' => 'stock',
            'status' => 'active',
            'start_date' => '2025-01-01',
            'initial_investment' => 1000,
        ]);

        $response->assertRedirect(route('investments.index'));
        $this->assertDatabaseHas('investments', [
            'name' => 'Apple Stock',
            'initial_investment' => 1000,
            'current_value' => 1000,
            'user_id' => $user->id,
        ]);

        $investment = Investment::first();
        $this->assertCount(1, $investment->transactions);
        $this->assertEquals(1000, $investment->transactions->first()->amount);
        $this->assertCount(1, $investment->valuations);
        $this->assertEquals(1000, $investment->valuations->first()->valuation_amount);
    }

    public function test_user_can_add_transaction()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $investment = Investment::create([
            'user_id' => $user->id,
            'name' => 'Tesla Stock',
            'type' => 'stock',
            'status' => 'active',
            'start_date' => '2025-01-01',
            'initial_investment' => 1000,
            'current_value' => 1000,
        ]);

        // Initial transaction created manually for test setup if not using factory/service
        // But our controller handles it. Here we test the addTransaction endpoint.

        $response = $this->post(route('investments.transactions.store', $investment), [
            'type' => 'buy',
            'amount' => 500,
            'transaction_date' => '2025-02-01',
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('investment_transactions', [
            'investment_id' => $investment->id,
            'amount' => 500,
            'type' => 'buy',
        ]);

        $investment->refresh();
        // Total invested should be 1000 (initial) + 500 (new) = 1500
        // Wait, the model's total_invested attribute calculates based on transactions.
        // Since we created the investment manually without transactions, we need to add the initial one too or just check if it adds up.
        // Let's rely on the attribute logic: sum of buy/add - withdraw.
        // We only added one transaction of 500.
        // The initial_investment column is 1000.
        // The attribute: return $this->initial_investment + $contributions - $withdrawals;
        // Contributions = 500.
        // Total = 1500.
        $this->assertEquals(1500, $investment->total_invested);
    }

    public function test_user_can_add_valuation_and_roi_updates()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $investment = Investment::create([
            'user_id' => $user->id,
            'name' => 'Bitcoin',
            'type' => 'crypto',
            'status' => 'active',
            'start_date' => '2025-01-01',
            'initial_investment' => 1000,
            'current_value' => 1000,
        ]);

        $response = $this->post(route('investments.valuations.store', $investment), [
            'valuation_amount' => 1500,
            'valuation_date' => '2025-03-01',
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('investment_valuations', [
            'investment_id' => $investment->id,
            'valuation_amount' => 1500,
        ]);

        $investment->refresh();
        $this->assertEquals(1500, $investment->current_value);

        // ROI Calculation
        // Total Invested = 1000 (initial)
        // Current Value = 1500
        // Gain = 500
        // ROI = (500 / 1000) * 100 = 50%
        $this->assertEquals(50, $investment->roi);
    }

    public function test_dashboard_access()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get(route('investments.index'));
        $response->assertStatus(200);
        $response->assertViewIs('investments.index');
    }
}
