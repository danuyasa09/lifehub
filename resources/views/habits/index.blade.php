<x-app-layout>
    <x-slot name="header">
        Habit Tracker
    </x-slot>

    <div class="max-w-4xl mx-auto">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-2xl font-bold text-text">Your Habits</h1>
                <p class="text-text-secondary mt-1">Track your daily progress and build streaks.</p>
            </div>
            <button onclick="document.getElementById('create-habit-modal').classList.remove('hidden')" class="px-4 py-2 bg-primary text-white text-sm font-medium rounded-xl shadow-sm hover:bg-primary/90 transition">
                + New Habit
            </button>
        </div>

        <div class="bg-surface rounded-2xl shadow-sm border border-border">
            <div class="divide-y divide-border">
                @forelse($habits as $habit)
                    <div class="p-4 sm:p-6 flex items-center justify-between hover:bg-surface-secondary transition first:rounded-t-2xl last:rounded-b-2xl" x-data="{
                        isCompleted: {{ $habit->today_completed ? 'true' : 'false' }},
                        streak: {{ $habit->current_streak }},
                        toggleHabit() {
                            fetch('{{ route('habits.toggle', $habit) }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: JSON.stringify({ date: '{{ \Carbon\Carbon::today()->toDateString() }}' })
                            })
                            .then(res => res.json())
                            .then(data => {
                                if(data.success) {
                                    if(data.is_completed && !this.isCompleted) {
                                        this.streak++;
                                    } else if(!data.is_completed && this.isCompleted) {
                                        this.streak--;
                                    }
                                    this.isCompleted = data.is_completed;
                                }
                            })
                        }
                    }">
                        <div class="flex items-center space-x-4">
                            <!-- Custom Checkbox -->
                            <button @click="toggleHabit()" 
                                class="w-8 h-8 rounded-full border-2 flex items-center justify-center transition-colors duration-200 focus:outline-none"
                                :class="isCompleted ? 'bg-success border-success text-white' : 'border-border hover:border-success'">
                                <svg x-show="isCompleted" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                            </button>
                            
                            <div>
                                <h3 class="text-lg font-semibold text-text flex items-center space-x-2">
                                    @if($habit->icon)<span>{{ $habit->icon }}</span>@endif
                                    <span>{{ $habit->name }}</span>
                                </h3>
                                <div class="text-sm text-text-secondary mt-0.5 capitalize">{{ $habit->frequency }}</div>
                            </div>
                        </div>

                        <div class="flex items-center space-x-6">
                            <!-- Streak Indicator -->
                            <div class="flex items-center space-x-1.5 px-3 py-1 bg-orange-50 rounded-lg">
                                <span class="text-orange-500">🔥</span>
                                <span class="text-sm font-medium text-orange-700"><span x-text="streak"></span> Day Streak</span>
                            </div>

                            <!-- Options Dropdown -->
                            <div x-data="{ open: false }" class="relative">
                                <button @click="open = !open" class="p-2 text-text-secondary hover:text-text hover:bg-surface-secondary rounded-lg transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path></svg>
                                </button>
                                <div x-show="open" @click.away="open = false" style="display: none;" class="absolute right-0 mt-2 w-32 bg-surface rounded-xl shadow-lg border border-border z-10 py-1">
                                    <form method="POST" action="{{ route('habits.destroy', $habit) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="w-full text-left px-4 py-2 text-sm text-danger hover:bg-red-50">Delete</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-6 sm:p-8 text-center text-text-secondary">
                        <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                        <p>No habits tracked yet. Create one to get started!</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Create Habit Modal -->
    <div id="create-habit-modal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="document.getElementById('create-habit-modal').classList.add('hidden')"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-surface rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form method="POST" action="{{ route('habits.store') }}">
                    @csrf
                    <div class="bg-surface px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-semibold text-text mb-4" id="modal-title">
                                    Create New Habit
                                </h3>
                                
                                <div class="space-y-4">
                                    <div>
                                        <label for="name" class="block text-sm font-medium text-text">Name</label>
                                        <input type="text" name="name" id="name" required class="mt-1 block w-full border-border rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm">
                                    </div>
                                    <div>
                                        <label for="icon" class="block text-sm font-medium text-text">Icon (Emoji)</label>
                                        <input type="text" name="icon" id="icon" class="mt-1 block w-full border-border rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm" placeholder="e.g. 🏃">
                                    </div>
                                    <div>
                                        <label for="frequency" class="block text-sm font-medium text-text">Frequency</label>
                                        <select name="frequency" id="frequency" class="mt-1 block w-full border-border rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm">
                                            <option value="daily">Daily</option>
                                            <option value="weekly">Weekly</option>
                                            <option value="monthly">Monthly</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-surface-secondary px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-4 py-2 bg-primary text-base font-medium text-white hover:bg-primary/90 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                            Save
                        </button>
                        <button type="button" onclick="document.getElementById('create-habit-modal').classList.add('hidden')" class="mt-3 w-full inline-flex justify-center rounded-xl border border-border shadow-sm px-4 py-2 bg-surface text-base font-medium text-text hover:bg-surface-secondary focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
