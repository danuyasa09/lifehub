<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between w-full">
            <h2 class="font-semibold text-lg md:text-xl text-text leading-tight truncate pr-4">
                {{ __('Dashboard') }}
            </h2>
            <div class="text-xs sm:text-sm text-text-secondary font-medium shrink-0">
                {{ now()->format('l, F j') }}
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        <!-- Greeting -->
        <div>
            <h1 class="text-3xl font-bold tracking-tight text-text">{{ $greeting }}, {{ auth()->user()->name }} 👋</h1>
            <p class="text-text-secondary mt-1">Here's a quick overview of your day.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-4">
            
            <!-- AI Insight Widget (Full Width) -->
            <div class="col-span-1 md:col-span-3 bg-surface rounded-2xl shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)] border-l-4 border-primary dark:border-l-primary dark:border-y-0 dark:border-r-0 dark:neon-border-glow dark:bg-primary/5 p-4 md:p-5 flex items-start space-x-4">
                <div class="pt-0.5 text-primary shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                </div>
                <div>
                    <h3 class="text-xs font-bold text-text-secondary uppercase tracking-wider mb-1">AI Insight</h3>
                    <p class="text-text text-sm font-medium">{{ $aiInsight }}</p>
                </div>
            </div>

            <!-- Column 1: Today's Tasks -->
            <div class="bg-surface rounded-2xl shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)] dark:shadow-none dark:dark-card-glow p-4 md:p-5 flex flex-col h-[400px]">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-sm font-semibold text-slate-500 uppercase tracking-wider">Today's Tasks</h3>
                    <a href="{{ route('tasks.index') }}" class="text-xs font-medium text-primary hover:underline">View all</a>
                </div>
                
                <div class="flex-1 overflow-y-auto space-y-3 pr-2 custom-scrollbar">
                    @forelse($todayTasks as $task)
                        <div x-data="{ completed: {{ in_array($task->status, ['completed', 'done']) ? 'true' : 'false' }} }" 
                             class="flex items-start space-x-3 p-2 rounded-xl hover:bg-slate-50 transition-colors group border border-transparent hover:border-slate-100">
                            
                            <button @click="
                                    completed = !completed;
                                    fetch('/tasks/{{ $task->id }}', {
                                        method: 'PUT',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                            'Accept': 'application/json'
                                        },
                                        body: JSON.stringify({
                                            title: {!! json_encode($task->title) !!},
                                            description: {!! json_encode($task->description) !!},
                                            category_id: {!! json_encode($task->category_id) !!},
                                            parent_id: {!! json_encode($task->parent_id) !!},
                                            priority: {!! json_encode($task->priority) !!},
                                            status: completed ? 'completed' : 'pending',
                                            due_date: {!! json_encode($task->due_date ? clone $task->due_date : null) !!},
                                            reminder_at: {!! json_encode($task->reminder_at ? clone $task->reminder_at : null) !!},
                                            is_recurring: {!! json_encode($task->is_recurring) !!},
                                            progress: completed ? 100 : 0
                                        })
                                    })
                                "
                                class="shrink-0 mt-0.5 w-5 h-5 rounded-md border flex items-center justify-center transition-colors"
                                :class="completed ? 'bg-primary border-primary text-white' : 'border-border hover:border-primary'">
                                <svg x-show="completed" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                            </button>
                            
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-text truncate transition-all duration-300" :class="completed ? 'line-through text-text-secondary' : ''">
                                    {{ $task->title }}
                                </p>
                                @if($task->category)
                                    <span class="inline-flex items-center space-x-1.5 mt-1">
                                        <span class="w-1.5 h-1.5 rounded-full" style="background-color: {{ $task->category->color }}"></span>
                                        <span class="text-[10px] font-medium text-text-secondary uppercase tracking-wider">{{ $task->category->name }}</span>
                                    </span>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="flex flex-col items-center justify-center h-full text-center px-4">
                            <div class="w-12 h-12 bg-surface-secondary rounded-full flex items-center justify-center mb-3">
                                <svg class="w-6 h-6 text-text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                            <p class="text-sm text-text-secondary">You have no tasks due today. Enjoy your day!</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Column 2: Finance & Habits -->
            <div class="flex flex-col space-y-6 h-[400px]">
                
                <!-- Monthly Finance -->
                <div class="bg-surface rounded-2xl shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)] dark:shadow-none dark:dark-card-glow p-4 md:p-5 flex-1 flex flex-col">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-sm font-semibold text-slate-500 uppercase tracking-wider">This Month</h3>
                        <a href="{{ route('finances.index') }}" class="text-xs font-medium text-primary hover:underline">Details</a>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4 flex-1">
                        <div class="bg-emerald-50/50 dark:bg-emerald-900/30 rounded-xl p-4 border border-emerald-100/50 dark:border-emerald-800/50 flex flex-col justify-center">
                            <span class="text-xs font-medium text-emerald-600 dark:text-emerald-400 mb-1">Income</span>
                            <span class="text-lg font-bold text-emerald-700 dark:text-emerald-300">{{ auth()->user()->currency_symbol }}{{ number_format($financeSummary['income'], 2) }}</span>
                        </div>
                        <div class="bg-red-50/50 dark:bg-red-900/30 rounded-xl p-4 border border-red-100/50 dark:border-red-800/50 flex flex-col justify-center">
                            <span class="text-xs font-medium text-red-600 dark:text-red-400 mb-1">Expense</span>
                            <span class="text-lg font-bold text-red-700 dark:text-red-300">{{ auth()->user()->currency_symbol }}{{ number_format($financeSummary['expense'], 2) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Habits Streaks -->
                <div class="bg-surface rounded-2xl shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)] dark:shadow-none dark:dark-card-glow p-4 md:p-5 flex-1 flex flex-col">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-sm font-semibold text-slate-500 uppercase tracking-wider">Active Streaks</h3>
                        <a href="{{ route('habits.index') }}" class="text-xs font-medium text-primary hover:underline">View all</a>
                    </div>
                    
                    <div class="flex-1 overflow-y-auto space-y-2 pr-2 custom-scrollbar">
                        @forelse($habitStreaks as $habit)
                            <div class="flex items-center justify-between p-2 hover:bg-slate-50 rounded-xl transition">
                                <div class="flex items-center space-x-3">
                                    <span class="text-xl">{{ $habit['icon'] ?? '🔥' }}</span>
                                    <span class="text-sm font-medium text-text">{{ $habit['name'] }}</span>
                                </div>
                                <div class="flex items-center space-x-1.5 bg-orange-50 px-2.5 py-1 rounded-full border border-orange-100">
                                    <span class="text-xs font-bold text-orange-600">{{ $habit['streak'] }}</span>
                                    <span class="text-[10px] text-orange-500 uppercase font-bold tracking-wider">days</span>
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-sm text-text-secondary mt-4">
                                No active streaks. Start building habits!
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Column 3: Active Projects -->
            <div class="bg-surface rounded-2xl shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)] dark:shadow-none dark:dark-card-glow p-4 md:p-5 flex flex-col h-[400px]">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-sm font-semibold text-slate-500 uppercase tracking-wider">Active Projects</h3>
                    <a href="{{ route('projects.index') }}" class="text-xs font-medium text-primary hover:underline">View all</a>
                </div>
                
                <div class="flex-1 overflow-y-auto space-y-4 pr-2 custom-scrollbar">
                    @forelse($activeProjects as $project)
                        @php
                            $totalTasks = $project->tasks->count();
                            $completedTasks = $project->tasks->where('status', 'done')->count();
                            $progress = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;
                        @endphp
                        <a href="{{ route('projects.show', $project) }}" class="block p-3 rounded-xl border border-border hover:border-primary/30 hover:shadow-sm transition group bg-surface-secondary/50 hover:bg-surface">
                            <div class="flex justify-between items-start mb-2">
                                <h4 class="text-sm font-semibold text-text group-hover:text-primary transition truncate pr-2">{{ $project->name }}</h4>
                                <span class="text-xs font-medium text-text-secondary">{{ $progress }}%</span>
                            </div>
                            <div class="w-full bg-surface-secondary rounded-full h-1.5 overflow-hidden">
                                <div class="bg-primary h-1.5 rounded-full transition-all duration-500" style="width: {{ $progress }}%"></div>
                            </div>
                            <div class="mt-2 text-[10px] text-text-secondary font-medium uppercase tracking-wider flex justify-between">
                                <span>{{ $completedTasks }} / {{ $totalTasks }} Tasks</span>
                                <span>Due {{ $project->end_date ? \Carbon\Carbon::parse($project->end_date)->format('M d') : 'N/A' }}</span>
                            </div>
                        </a>
                    @empty
                        <div class="flex flex-col items-center justify-center h-full text-center px-4">
                            <div class="w-12 h-12 bg-surface-secondary rounded-full flex items-center justify-center mb-3">
                                <svg class="w-6 h-6 text-text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                            </div>
                            <p class="text-sm text-text-secondary">No active projects right now.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Quick Note Widget (Full Width) -->
            <div class="col-span-1 md:col-span-3 bg-surface rounded-2xl shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)] dark:shadow-none dark:dark-card-glow p-4 md:p-5 mt-2">
                <h3 class="text-sm font-semibold text-slate-500 uppercase tracking-wider mb-3">Quick Capture</h3>
                <form action="{{ route('notes.store') }}" method="POST" class="flex items-center space-x-3">
                    @csrf
                    <input type="hidden" name="title" value="Quick Note {{ now()->format('M d, Y H:i') }}">
                    <input type="text" name="content" required placeholder="Jot down a quick thought or idea..." class="flex-1 bg-surface-secondary border border-border text-sm rounded-xl px-4 py-2.5 focus:bg-surface focus:ring-2 focus:ring-primary/50 dark:focus:ring-primary/80 focus:border-primary transition">
                    <button type="submit" class="bg-[#0F172A] hover:bg-gray-800 dark:bg-primary dark:hover:bg-primary/90 dark:neon-glow-primary text-white px-5 py-2.5 rounded-xl text-sm font-medium transition shadow-sm">Save Note</button>
                </form>
            </div>

        </div>
    </div>
    
    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background-color: #e2e8f0;
            border-radius: 10px;
        }
        .custom-scrollbar:hover::-webkit-scrollbar-thumb {
            background-color: #cbd5e1;
        }
    </style>
</x-app-layout>
