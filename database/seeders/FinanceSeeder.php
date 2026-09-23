<?php

namespace Database\Seeders;

use App\Models\Budget;
use App\Models\Finance;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class FinanceSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();
        if (!$user) return;

        // Seed Budgets
        $budgetCategories = ['Groceries', 'Entertainment', 'Utilities', 'Transportation'];
        foreach ($budgetCategories as $cat) {
            Budget::create([
                'user_id' => $user->id,
                'category' => $cat,
                'limit_amount' => fake()->randomFloat(2, 100, 1000),
                'period' => now()->startOfMonth(),
            ]);
        }

        // Seed Finances (past 3 months)
        $expenseCategories = ['Groceries', 'Entertainment', 'Utilities', 'Transportation', 'Dining', 'Shopping'];

        // Add 3 salary incomes
        for ($i = 0; $i < 3; $i++) {
            Finance::create([
                'user_id' => $user->id,
                'type' => 'income',
                'amount' => 4500,
                'category' => 'Salary',
                'description' => 'Monthly Salary',
                'transaction_date' => Carbon::now()->subMonths($i)->startOfMonth()->addDays(5),
                'is_recurring' => 'monthly',
            ]);
        }

        // Add 50 random expenses
        for ($i = 0; $i < 50; $i++) {
            Finance::create([
                'user_id' => $user->id,
                'type' => 'expense',
                'amount' => fake()->randomFloat(2, 10, 150),
                'category' => fake()->randomElement($expenseCategories),
                'description' => fake()->sentence(3),
                'transaction_date' => fake()->dateTimeBetween('-3 months', 'now'),
                'is_recurring' => 'none',
            ]);
        }

        // Add 5 random savings
        for ($i = 0; $i < 5; $i++) {
            Finance::create([
                'user_id' => $user->id,
                'type' => 'savings',
                'amount' => fake()->randomFloat(2, 50, 500),
                'category' => 'Emergency Fund',
                'description' => 'Transfer to savings',
                'transaction_date' => fake()->dateTimeBetween('-3 months', 'now'),
                'is_recurring' => 'none',
            ]);
        }
    }
}
