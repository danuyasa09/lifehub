<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-text leading-tight">
            {{ __('Finance Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-[1400px] mx-auto sm:px-6 lg:px-8">
            
            <!-- Page Title & Actions -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 space-y-4 sm:space-y-0">
                <h1 class="text-2xl md:text-3xl font-bold tracking-tight text-text">Overview</h1>
                <div class="flex space-x-2 sm:space-x-3 w-full sm:w-auto">
                    <button class="flex-1 sm:flex-none px-4 py-2 bg-surface text-text border border-border text-sm font-medium rounded-xl shadow-sm hover:bg-surface-secondary transition" x-data x-on:click="$dispatch('open-modal', 'set-budget')">
                        Set Budget
                    </button>
                    <button class="flex-1 sm:flex-none px-4 py-2 bg-primary text-white text-sm font-medium rounded-xl shadow-sm hover:bg-primary/90 transition" x-data x-on:click="$dispatch('open-modal', 'add-transaction')">
                        + Add Transaction
                    </button>
                </div>
            </div>

            <!-- Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Current Balance -->
                <div class="bg-surface rounded-2xl p-4 sm:p-6 shadow-sm border border-border">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-medium text-text-secondary">Current Balance</h3>
                        <div class="w-10 h-10 rounded-full bg-blue-500/10 flex items-center justify-center text-blue-500">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                    </div>
                    <div class="text-3xl font-bold text-text">
                        {{ auth()->user()->currency_symbol }}{{ number_format($currentBalance, 2) }}
                    </div>
                </div>

                <!-- Total Income -->
                <div class="bg-surface rounded-2xl p-4 sm:p-6 shadow-sm border border-border">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-medium text-text-secondary">Total Income</h3>
                        <div class="w-10 h-10 rounded-full bg-emerald-500/10 flex items-center justify-center text-emerald-500">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                        </div>
                    </div>
                    <div class="text-3xl font-bold text-text">
                        {{ auth()->user()->currency_symbol }}{{ number_format($totalIncome, 2) }}
                    </div>
                </div>

                <!-- Total Expense -->
                <div class="bg-surface rounded-2xl p-4 sm:p-6 shadow-sm border border-border">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-medium text-text-secondary">Total Expense</h3>
                        <div class="w-10 h-10 rounded-full bg-red-500/10 flex items-center justify-center text-red-500">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path></svg>
                        </div>
                    </div>
                    <div class="text-3xl font-bold text-text">
                        {{ auth()->user()->currency_symbol }}{{ number_format($totalExpense, 2) }}
                    </div>
                </div>

                <!-- Total Savings -->
                <div class="bg-surface rounded-2xl p-4 sm:p-6 shadow-sm border border-border">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-medium text-text-secondary">Total Savings</h3>
                        <div class="w-10 h-10 rounded-full bg-amber-500/10 flex items-center justify-center text-amber-500">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>
                        </div>
                    </div>
                    <div class="text-3xl font-bold text-text">
                        {{ auth()->user()->currency_symbol }}{{ number_format($totalSavings, 2) }}
                    </div>
                </div>
            </div>

            <!-- Recent Transactions Table -->
            <div class="bg-surface rounded-2xl shadow-sm border border-border overflow-hidden">
                <div class="p-6 border-b border-border flex flex-col sm:flex-row sm:items-center justify-between space-y-4 sm:space-y-0">
                    <h3 class="text-lg font-medium text-text">Recent Transactions</h3>
                    
                    <!-- Filters -->
                    <form method="GET" action="{{ route('finances.index') }}" class="flex flex-wrap items-center gap-3" x-data="{ submit() { this.$el.submit() } }">
                        <select name="month" @change="submit" class="bg-surface-secondary text-text border-border text-sm rounded-xl focus:ring-primary focus:border-primary">
                            <option value="">All Months</option>
                            @foreach(range(1, 12) as $m)
                                <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>
                                    {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                                </option>
                            @endforeach
                        </select>
                        <select name="type" @change="submit" class="bg-surface-secondary text-text border-border text-sm rounded-xl focus:ring-primary focus:border-primary">
                            <option value="">All Types</option>
                            <option value="income" {{ request('type') == 'income' ? 'selected' : '' }}>Income</option>
                            <option value="expense" {{ request('type') == 'expense' ? 'selected' : '' }}>Expense</option>
                            <option value="savings" {{ request('type') == 'savings' ? 'selected' : '' }}>Savings</option>
                        </select>
                        @if(request('month') || request('type'))
                            <a href="{{ route('finances.index') }}" class="text-sm text-text-secondary hover:text-text">Clear</a>
                        @endif
                    </form>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-border">
                        <thead class="bg-surface-secondary/50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-text-secondary uppercase tracking-wider">Transaction</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-text-secondary uppercase tracking-wider">Date</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-text-secondary uppercase tracking-wider">Category</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-text-secondary uppercase tracking-wider">Type</th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-text-secondary uppercase tracking-wider">Amount</th>
                            </tr>
                        </thead>
                        <tbody class="bg-surface divide-y divide-border">
                            @forelse($transactions as $transaction)
                                <tr class="hover:bg-surface-secondary/50 transition">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-text">{{ $transaction->description ?: 'N/A' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-text-secondary">{{ $transaction->transaction_date->format('M d, Y') }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-text-secondary">{{ $transaction->category }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $typeColors = [
                                                'income' => 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400',
                                                'expense' => 'bg-red-500/10 text-red-600 dark:text-red-400',
                                                'savings' => 'bg-amber-500/10 text-amber-600 dark:text-amber-400',
                                            ];
                                        @endphp
                                        <span class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full {{ $typeColors[$transaction->type] }}">
                                            {{ ucfirst($transaction->type) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <span class="{{ $transaction->type === 'income' ? 'text-emerald-600' : ($transaction->type === 'expense' ? 'text-red-600' : 'text-amber-600') }}">
                                            {{ $transaction->type === 'expense' ? '-' : '+' }}{{ auth()->user()->currency_symbol }}{{ number_format($transaction->amount, 2) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center text-text-secondary">
                                        No transactions found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <!-- Pagination -->
                @if($transactions->hasPages())
                    <div class="px-6 py-4 border-t border-border">
                        {{ $transactions->withQueryString()->links() }}
                    </div>
                @endif
            </div>
            
        </div>
    </div>

    <!-- Add Transaction Modal -->
    <x-modal name="add-transaction" focusable>
        <form method="post" action="{{ route('finances.store') }}" class="p-6">
            @csrf
            <h2 class="text-lg font-medium text-text mb-4">
                {{ __('Add New Transaction') }}
            </h2>

            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="type" value="{{ __('Type') }}" />
                        <select id="type" name="type" class="bg-surface-secondary text-text mt-1 block w-full border-border focus:border-primary focus:ring-primary rounded-xl shadow-sm text-sm">
                            <option value="expense">Expense</option>
                            <option value="income">Income</option>
                            <option value="savings">Savings</option>
                        </select>
                    </div>
                    <div>
                        <x-input-label for="amount" value="{{ __('Amount') }} ({{ auth()->user()->currency_symbol }})" />
                        <x-text-input id="amount" name="amount" type="number" step="0.01" min="0" class="mt-1 block w-full text-sm rounded-xl" required />
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="category" value="{{ __('Category') }}" />
                        <x-text-input id="category" name="category" type="text" class="mt-1 block w-full text-sm rounded-xl" placeholder="e.g. Groceries, Salary..." required />
                    </div>
                    <div>
                        <x-input-label for="transaction_date" value="{{ __('Date') }}" />
                        <x-text-input id="transaction_date" name="transaction_date" type="date" value="{{ date('Y-m-d') }}" class="mt-1 block w-full text-sm rounded-xl" required />
                    </div>
                </div>
                <div>
                    <x-input-label for="description" value="{{ __('Description (Optional)') }}" />
                    <x-text-input id="description" name="description" type="text" class="mt-1 block w-full text-sm rounded-xl" />
                </div>
                <div>
                    <x-input-label for="is_recurring" value="{{ __('Recurring?') }}" />
                    <select id="is_recurring" name="is_recurring" class="bg-surface-secondary text-text mt-1 block w-full border-border focus:border-primary focus:ring-primary rounded-xl shadow-sm text-sm">
                        <option value="none">No</option>
                        <option value="daily">Daily</option>
                        <option value="weekly">Weekly</option>
                        <option value="monthly">Monthly</option>
                    </select>
                </div>
            </div>

            <div class="mt-6 flex justify-end">
                <button type="button" class="px-4 py-2 text-sm font-medium text-text bg-surface border border-border rounded-xl shadow-sm hover:bg-surface-secondary focus:outline-none" x-on:click="$dispatch('close')">
                    {{ __('Cancel') }}
                </button>
                <button type="submit" class="ml-3 px-4 py-2 text-sm font-medium text-white bg-primary border border-transparent rounded-xl shadow-sm hover:bg-primary/90 focus:outline-none">
                    {{ __('Save Transaction') }}
                </button>
            </div>
        </form>
    </x-modal>

    <!-- Set Budget Modal -->
    <x-modal name="set-budget" focusable>
        <form method="post" action="{{ route('budgets.store') }}" class="p-6">
            @csrf
            <h2 class="text-lg font-medium text-text mb-4">
                {{ __('Set Category Budget') }}
            </h2>

            <div class="space-y-4">
                <div>
                    <x-input-label for="budget_category" value="{{ __('Category') }}" />
                    <x-text-input id="budget_category" name="category" type="text" class="mt-1 block w-full text-sm rounded-xl" placeholder="e.g. Groceries" required />
                </div>
                <div>
                    <x-input-label for="limit_amount" value="{{ __('Limit Amount') }} ({{ auth()->user()->currency_symbol }})" />
                    <x-text-input id="limit_amount" name="limit_amount" type="number" step="0.01" min="0" class="mt-1 block w-full text-sm rounded-xl" required />
                </div>
                <div>
                    <x-input-label for="period" value="{{ __('Period (Start Date)') }}" />
                    <x-text-input id="period" name="period" type="date" value="{{ date('Y-m-01') }}" class="mt-1 block w-full text-sm rounded-xl" required />
                </div>
            </div>

            <div class="mt-6 flex justify-end">
                <button type="button" class="px-4 py-2 text-sm font-medium text-text bg-surface border border-border rounded-xl shadow-sm hover:bg-surface-secondary focus:outline-none" x-on:click="$dispatch('close')">
                    {{ __('Cancel') }}
                </button>
                <button type="submit" class="ml-3 px-4 py-2 text-sm font-medium text-white bg-primary border border-transparent rounded-xl shadow-sm hover:bg-primary/90 focus:outline-none">
                    {{ __('Save Budget') }}
                </button>
            </div>
        </form>
    </x-modal>
</x-app-layout>
