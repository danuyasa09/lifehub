<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-text leading-tight">
            {{ __('Projects') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Page Title & Actions -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 space-y-4 sm:space-y-0">
                <h1 class="text-2xl md:text-3xl font-bold tracking-tight text-text">Overview</h1>
                <button class="w-full sm:w-auto px-4 py-2 bg-primary text-white text-sm font-medium rounded-xl shadow-sm hover:bg-primary/90 transition" x-data x-on:click="$dispatch('open-modal', 'create-project')">
                    + New Project
                </button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($projects as $project)
                    @php
                        $totalTasks = $project->tasks->count();
                        $completedTasks = $project->tasks->where('status', 'done')->count();
                        $progress = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;
                        
                        $statusColors = [
                            'active' => 'bg-emerald-100 text-emerald-700',
                            'completed' => 'bg-surface-secondary text-text',
                            'on_hold' => 'bg-amber-100 text-amber-700',
                        ];
                    @endphp
                    <a href="{{ route('projects.show', $project) }}" class="group block p-6 bg-surface border border-border rounded-2xl shadow-sm hover:shadow-md hover:border-primary/30 transition duration-200">
                        <div class="flex justify-between items-start mb-4">
                            <h3 class="text-lg font-semibold text-text truncate">{{ $project->name }}</h3>
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$project->status] ?? 'bg-surface-secondary text-text' }}">
                                {{ ucfirst(str_replace('_', ' ', $project->status)) }}
                            </span>
                        </div>
                        
                        <p class="text-sm text-text-secondary line-clamp-2 mb-6 h-10">
                            {{ $project->description ?: 'No description provided.' }}
                        </p>

                        <!-- Progress Bar -->
                        <div class="mb-2 flex justify-between text-xs font-medium text-text-secondary">
                            <span>Progress</span>
                            <span>{{ $progress }}%</span>
                        </div>
                        <div class="w-full bg-surface-secondary rounded-full h-2 mb-4">
                            <div class="bg-primary h-2 rounded-full transition-all duration-500" style="{{ 'width: ' . $progress . '%' }}"></div>
                        </div>

                        <div class="flex items-center justify-between text-xs text-text-secondary mt-auto pt-4 border-t border-gray-50">
                            <div class="flex items-center space-x-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                <span>{{ $project->end_date ? \Carbon\Carbon::parse($project->end_date)->format('M d, Y') : 'No deadline' }}</span>
                            </div>
                            <div class="flex space-x-1">
                                <span class="bg-surface-secondary text-text-secondary px-2 py-1 rounded-md">{{ $completedTasks }}/{{ $totalTasks }} Tasks</span>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="col-span-full py-16 text-center bg-surface rounded-2xl border border-dashed border-border">
                        <svg class="w-12 h-12 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        <p class="text-lg font-medium text-text">No projects yet.</p>
                        <p class="text-sm text-text-secondary mt-1 mb-6">Create your first project to start tracking work.</p>
                        <button class="px-4 py-2 bg-primary text-white text-sm font-medium rounded-xl shadow-sm hover:bg-primary/90 transition" x-data x-on:click="$dispatch('open-modal', 'create-project')">
                            Create Project
                        </button>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Create Project Modal (Tailwind/Alpine based) -->
    <x-modal name="create-project" focusable>
        <form method="post" action="{{ route('projects.store') }}" class="p-6">
            @csrf
            <h2 class="text-lg font-medium text-text mb-4">
                {{ __('Create New Project') }}
            </h2>

            <div class="space-y-4">
                <div>
                    <x-input-label for="name" value="{{ __('Name') }}" />
                    <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" required />
                </div>
                <div>
                    <x-input-label for="description" value="{{ __('Description') }}" />
                    <textarea id="description" name="description" class="mt-1 block w-full border-border focus:border-primary focus:ring-primary rounded-md shadow-sm" rows="3"></textarea>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="start_date" value="{{ __('Start Date') }}" />
                        <x-text-input id="start_date" name="start_date" type="date" class="mt-1 block w-full" />
                    </div>
                    <div>
                        <x-input-label for="end_date" value="{{ __('End Date') }}" />
                        <x-text-input id="end_date" name="end_date" type="date" class="mt-1 block w-full" />
                    </div>
                </div>
                <div>
                    <x-input-label for="status" value="{{ __('Status') }}" />
                    <select id="status" name="status" class="mt-1 block w-full border-border focus:border-primary focus:ring-primary rounded-md shadow-sm">
                        <option value="active">Active</option>
                        <option value="on_hold">On Hold</option>
                        <option value="completed">Completed</option>
                    </select>
                </div>
            </div>

            <div class="mt-6 flex justify-end">
                <button type="button" class="px-4 py-2 text-sm font-medium text-text bg-surface border border-border rounded-lg shadow-sm hover:bg-surface-secondary focus:outline-none" x-on:click="$dispatch('close')">
                    {{ __('Cancel') }}
                </button>
                <button type="submit" class="ml-3 px-4 py-2 text-sm font-medium text-white bg-primary border border-transparent rounded-lg shadow-sm hover:bg-primary/90 focus:outline-none">
                    {{ __('Create Project') }}
                </button>
            </div>
        </form>
    </x-modal>

</x-app-layout>
