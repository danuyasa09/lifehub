<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-text leading-tight">
            {{ __('Analytics & Insights') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-[1400px] mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                
                <!-- Productivity Chart -->
                <div class="bg-surface p-6 rounded-3xl shadow-sm border border-border">
                    <div class="mb-4">
                        <h3 class="text-lg font-medium text-text">Productivity</h3>
                        <p class="text-sm text-text-secondary">Task Completion Rate</p>
                    </div>
                    <div class="relative h-64 w-full">
                        <canvas id="productivityChart"></canvas>
                    </div>
                </div>

                <!-- Expenses Chart -->
                <div class="bg-surface p-6 rounded-3xl shadow-sm border border-border">
                    <div class="mb-4">
                        <h3 class="text-lg font-medium text-text">Expenses</h3>
                        <p class="text-sm text-text-secondary">Breakdown by Category</p>
                    </div>
                    <div class="relative h-64 w-full">
                        <canvas id="expensesChart"></canvas>
                    </div>
                </div>

                <!-- Habits Chart -->
                <div class="bg-surface p-6 rounded-3xl shadow-sm border border-border md:col-span-2">
                    <div class="mb-4">
                        <h3 class="text-lg font-medium text-text">Habits Activity</h3>
                        <p class="text-sm text-text-secondary">Completions over the last 30 days</p>
                    </div>
                    <div class="relative h-72 w-full">
                        <canvas id="habitsChart"></canvas>
                    </div>
                </div>

                <!-- Mood Chart -->
                <div class="bg-surface p-6 rounded-3xl shadow-sm border border-border md:col-span-2">
                    <div class="mb-4">
                        <h3 class="text-lg font-medium text-text">Mood Trends</h3>
                        <p class="text-sm text-text-secondary">Journal mood distribution</p>
                    </div>
                    <div class="relative h-64 w-full">
                        <canvas id="moodChart"></canvas>
                    </div>
                </div>

            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        const initCharts = () => {
            if (typeof Chart === 'undefined') {
                setTimeout(initCharts, 100);
                return;
            }
            
            const colors = ['#4F46E5', '#06B6D4', '#22C55E', '#EF4444', '#F59E0B'];
                
                // 1. Productivity (Doughnut)
                const prodCtx = document.getElementById('productivityChart');
                new Chart(prodCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Completed', 'Pending'],
                        datasets: [{
                            data: [{{ $taskStats['completed'] }}, {{ $taskStats['pending'] }}],
                            backgroundColor: ['#22C55E', '#F59E0B'],
                            borderWidth: 0,
                            cutout: '75%'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { position: 'bottom' }
                        }
                    }
                });

                // 2. Expenses (Pie)
                const expCtx = document.getElementById('expensesChart');
                new Chart(expCtx, {
                    type: 'pie',
                    data: {
                        labels: {!! json_encode($expenseLabels) !!},
                        datasets: [{
                            data: {!! json_encode($expenseValues) !!},
                            backgroundColor: colors,
                            borderWidth: 0
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { position: 'right' }
                        }
                    }
                });

                // 3. Habits (Line)
                const habCtx = document.getElementById('habitsChart');
                new Chart(habCtx, {
                    type: 'line',
                    data: {
                        labels: {!! json_encode($habitDates) !!},
                        datasets: [{
                            label: 'Habits Completed',
                            data: {!! json_encode($habitCounts) !!},
                            borderColor: '#4F46E5',
                            backgroundColor: 'rgba(79, 70, 229, 0.1)',
                            fill: true,
                            tension: 0.4,
                            borderWidth: 3,
                            pointRadius: 4,
                            pointBackgroundColor: '#4F46E5'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: { beginAtZero: true, ticks: { stepSize: 1 } }
                        },
                        plugins: {
                            legend: { display: false }
                        }
                    }
                });

                // 4. Mood (Bar)
                const moodCtx = document.getElementById('moodChart');
                new Chart(moodCtx, {
                    type: 'bar',
                    data: {
                        labels: {!! json_encode($moodLabels) !!},
                        datasets: [{
                            label: 'Entries count',
                            data: {!! json_encode($moodValues) !!},
                            backgroundColor: '#06B6D4',
                            borderRadius: 6
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: { beginAtZero: true, ticks: { stepSize: 1 } }
                        },
                        plugins: {
                            legend: { display: false }
                        }
                    }
                });
        };
        
        // Start checking when DOM is ready
        document.addEventListener('DOMContentLoaded', initCharts);
    </script>
    @endpush
</x-app-layout>
