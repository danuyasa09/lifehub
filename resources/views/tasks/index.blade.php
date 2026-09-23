<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-text leading-tight">
            {{ __('Tasks') }}
        </h2>
    </x-slot>

    <div x-data="taskManager()" class="space-y-6">
        @if ($errors->any())
            <div class="p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        
        <!-- Header & Actions -->
        <div class="flex justify-between items-center">
            <h1 class="text-3xl font-bold tracking-tight text-text">My Tasks</h1>
            <button @click="openModal()" class="bg-primary hover:bg-primary/90 text-white px-4 py-2 rounded-xl font-medium transition shadow-sm shadow-primary/30 flex items-center space-x-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                <span>New Task</span>
            </button>
        </div>

        <!-- Tabs -->
        <div class="flex space-x-1 border-b border-border overflow-x-auto hide-scrollbar">
            <template x-for="t in tabs" :key="t.id">
                <button @click="activeTab = t.id"
                    :class="activeTab === t.id ? 'border-primary text-primary' : 'border-transparent text-text-secondary hover:text-text hover:border-border'"
                    class="px-4 py-2 border-b-2 font-medium text-sm transition-colors duration-200 whitespace-nowrap"
                    x-text="t.label"></button>
            </template>
        </div>

        <!-- Task List -->
        <div class="bg-surface rounded-[20px] shadow-sm border border-border overflow-hidden">
            <ul role="list" class="divide-y divide-border">
                @forelse($tasks as $task)
                    <li class="p-4 sm:p-6 hover:bg-surface-secondary transition flex items-start space-x-4 task-item" 
                        x-show="isVisible('{{ $task->status }}', '{{ $task->due_date ? $task->due_date->format('Y-m-d') : '' }}')"
                        x-cloak>
                        
                        <!-- Checkbox -->
                        <div class="flex-shrink-0 mt-1">
                            <form method="POST" action="{{ route('tasks.update', $task) }}" class="inline">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="title" value="{{ $task->title }}">
                                <input type="hidden" name="priority" value="{{ $task->priority }}">
                                <input type="hidden" name="status" value="{{ $task->status === 'completed' ? 'pending' : 'completed' }}">
                                <input type="hidden" name="is_recurring" value="{{ $task->is_recurring }}">
                                <input type="hidden" name="progress" value="{{ $task->status === 'completed' ? 0 : 100 }}">
                                
                                <button type="submit" class="w-5 h-5 rounded border flex items-center justify-center transition-colors {{ $task->status === 'completed' ? 'bg-success border-success text-white' : 'border-border hover:border-primary' }}">
                                    @if($task->status === 'completed')
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                    @endif
                                </button>
                            </form>
                        </div>

                        <!-- Content -->
                        <div class="flex-1 min-w-0" @click="openModal({{ $task->toJson() }})">
                            <div class="flex items-center space-x-3 cursor-pointer">
                                <p class="text-sm font-semibold text-text truncate {{ $task->status === 'completed' ? 'line-through text-text-secondary' : '' }}">
                                    {{ $task->title }}
                                </p>
                                @if($task->category)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium" style="{{ 'background-color: ' . $task->category->color . '20; color: ' . $task->category->color }}">
                                        {{ $task->category->name }}
                                    </span>
                                @endif
                                
                                @php
                                    $priorityColors = [
                                        'urgent' => 'bg-danger/10 text-danger border-danger/20',
                                        'high' => 'bg-warning/10 text-warning border-warning/20',
                                        'medium' => 'bg-primary/10 text-primary border-primary/20',
                                        'low' => 'bg-surface-secondary text-text-secondary border-border',
                                    ];
                                @endphp
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium border {{ $priorityColors[$task->priority] }}">
                                    {{ ucfirst($task->priority) }}
                                </span>
                            </div>
                            
                            @if($task->description)
                                <p class="text-sm text-text-secondary mt-1 truncate">{{ $task->description }}</p>
                            @endif

                            <div class="flex items-center space-x-4 mt-2">
                                @if($task->due_date)
                                    <div class="flex items-center text-xs text-text-secondary">
                                        <svg class="w-4 h-4 mr-1 text-text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        {{ $task->due_date->format('M d, Y') }}
                                    </div>
                                @endif
                                
                                @if($task->progress > 0 && $task->progress < 100)
                                    <div class="flex items-center text-xs text-text-secondary w-24">
                                        <div class="w-full bg-surface-secondary rounded-full h-1.5 mr-2">
                                            <div class="bg-primary h-1.5 rounded-full" style="{{ 'width: ' . $task->progress . '%' }}"></div>
                                        </div>
                                        <span>{{ $task->progress }}%</span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex-shrink-0 flex items-center space-x-2">
                            <form method="POST" action="{{ route('tasks.destroy', $task) }}" onsubmit="return confirm('Delete this task?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-text-secondary hover:text-danger p-1 rounded transition focus:outline-none">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </form>
                        </div>
                    </li>
                @empty
                    <li class="p-4 sm:p-6 text-center text-text-secondary py-8 sm:py-12">
                        <div class="w-12 h-12 bg-surface-secondary rounded-full flex items-center justify-center mx-auto mb-3">
                            <svg class="w-6 h-6 text-text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                        </div>
                        <p class="font-medium text-text">No tasks found</p>
                        <p class="text-sm mt-1">You're all caught up for this view.</p>
                    </li>
                @endforelse
            </ul>
        </div>

        <x-task-modal :categories="$categories" />
    </div>

    <script>
        function taskManager() {
            return {
                activeTab: 'today',
                tabs: [
                    { id: 'today', label: 'Today' },
                    { id: 'tomorrow', label: 'Tomorrow' },
                    { id: 'upcoming', label: 'Upcoming' },
                    { id: 'completed', label: 'Completed' },
                    { id: 'all', label: 'All Tasks' }
                ],
                isModalOpen: false,
                isEditing: false,
                formAction: "{{ route('tasks.store') }}",
                form: {
                    title: '',
                    description: '',
                    due_date: '',
                    priority: 'medium',
                    category_id: '',
                    status: 'pending',
                    progress: 0,
                    is_recurring: 'none'
                },
                isVisible(status, dueDateStr) {
                    if (this.activeTab === 'all') return true;
                    if (this.activeTab === 'completed') return status === 'completed';
                    if (status === 'completed') return false; 
                    
                    const today = new Date();
                    today.setHours(0,0,0,0);
                    
                    const tomorrow = new Date(today);
                    tomorrow.setDate(tomorrow.getDate() + 1);

                    if (!dueDateStr) {
                        return this.activeTab === 'upcoming';
                    }

                    // Handle timezone offsets
                    const [year, month, day] = dueDateStr.split('-');
                    const due = new Date(year, month - 1, day);
                    due.setHours(0,0,0,0);

                    if (this.activeTab === 'today') {
                        return due <= today; 
                    }
                    if (this.activeTab === 'tomorrow') {
                        return due.getTime() === tomorrow.getTime();
                    }
                    if (this.activeTab === 'upcoming') {
                        return due > tomorrow;
                    }
                    return false;
                },
                openModal(task = null) {
                    this.isEditing = !!task;
                    if (task) {
                        this.formAction = `/tasks/${task.id}`;
                        this.form = {
                            title: task.title || '',
                            description: task.description || '',
                            due_date: task.due_date ? task.due_date.substring(0, 10) : '',
                            priority: task.priority || 'medium',
                            category_id: task.category_id || '',
                            status: task.status || 'pending',
                            progress: task.progress || 0,
                            is_recurring: task.is_recurring || 'none'
                        };
                    } else {
                        this.formAction = "{{ route('tasks.store') }}";
                        this.form = {
                            title: '',
                            description: '',
                            due_date: new Date().toISOString().substring(0, 10),
                            priority: 'medium',
                            category_id: '',
                            status: 'pending',
                            progress: 0,
                            is_recurring: 'none'
                        };
                    }
                    this.isModalOpen = true;
                },
                closeModal() {
                    this.isModalOpen = false;
                }
            }
        }
    </script>
</x-app-layout>
