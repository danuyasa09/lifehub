<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between w-full">
            <div class="flex-1 min-w-0 pr-4 mb-2 sm:mb-0">
                <a href="{{ route('projects.index') }}" class="text-sm text-text-secondary hover:text-text mb-1 inline-flex items-center">
                    <svg class="w-4 h-4 mr-1 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Back
                </a>
                <h2 class="font-semibold text-lg md:text-xl text-text leading-tight truncate">
                    {{ $project->name }}
                </h2>
            </div>
            <div class="flex space-x-3 shrink-0">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs sm:text-sm font-medium bg-surface-secondary text-text">
                    {{ ucfirst(str_replace('_', ' ', $project->status)) }}
                </span>
            </div>
        </div>
    </x-slot>

    <div class="py-6" x-data="projectTabs()">
        <div class="max-w-[1400px] mx-auto sm:px-6 lg:px-8">
            
            <!-- Tabs Navigation -->
            <div class="mb-6 border-b border-border">
                <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                    <button @click="activeTab = 'board'" :class="{'border-primary text-primary': activeTab === 'board', 'border-transparent text-text-secondary hover:text-text hover:border-border': activeTab !== 'board'}" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                        Board
                    </button>
                    <button @click="activeTab = 'timeline'" :class="{'border-primary text-primary': activeTab === 'timeline', 'border-transparent text-text-secondary hover:text-text hover:border-border': activeTab !== 'timeline'}" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                        Timeline
                    </button>
                    <button @click="activeTab = 'files'" :class="{'border-primary text-primary': activeTab === 'files', 'border-transparent text-text-secondary hover:text-text hover:border-border': activeTab !== 'files'}" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                        Files
                    </button>
                </nav>
            </div>

            <!-- Tab Contents -->
            <div x-show="activeTab === 'board'" class="pb-4" style="display: none;" x-transition>
                
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-medium text-text">Task Board</h3>
                    <button x-on:click="$dispatch('open-modal', 'create-task')" class="px-4 py-2 bg-primary text-white text-sm font-medium rounded-lg shadow-sm hover:bg-primary/90 transition">
                        + Add Task
                    </button>
                </div>

                <!-- Kanban Board -->
                <div class="flex space-x-6 overflow-x-auto pb-8" x-data="kanbanBoard()">
                    <template x-for="column in columns" :key="column.id">
                        <div class="flex-shrink-0 w-80 bg-surface-secondary/50 rounded-2xl border border-border flex flex-col max-h-[calc(100vh-16rem)]">
                            <!-- Column Header -->
                            <div class="p-4 border-b border-border flex justify-between items-center bg-surface/50 rounded-t-2xl">
                                <h4 class="font-semibold text-text capitalize flex items-center space-x-2">
                                    <span class="w-2 h-2 rounded-full" :class="column.color"></span>
                                    <span x-text="column.name"></span>
                                </h4>
                                <span class="bg-surface-secondary text-text-secondary text-xs font-medium px-2 py-0.5 rounded-full" x-text="getTasks(column.id).length"></span>
                            </div>
                            
                            <!-- Column Body (Drop Zone) -->
                            <div class="p-3 flex-1 overflow-y-auto space-y-3"
                                 @dragover.prevent="onDragOver($event)"
                                 @drop="onDrop($event, column.id)">
                                
                                <template x-for="task in getTasks(column.id)" :key="task.id">
                                    <!-- Kanban Card -->
                                    <div draggable="true" 
                                         @dragstart="onDragStart($event, task.id)"
                                         @dragend="onDragEnd($event)"
                                         class="bg-surface p-4 rounded-xl shadow-sm border border-border cursor-grab active:cursor-grabbing hover:shadow-md hover:border-primary/30 transition-all group relative">
                                        
                                        <div class="flex justify-between items-start mb-2">
                                            <h5 class="font-medium text-text text-sm" x-text="task.title"></h5>
                                            <!-- Delete Task Form -->
                                            <form :action="`/project-tasks/${task.id}`" method="POST" class="opacity-0 group-hover:opacity-100 transition-opacity" onsubmit="return confirm('Delete this task?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-text-secondary hover:text-red-500">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                </button>
                                            </form>
                                        </div>
                                        <p class="text-xs text-text-secondary line-clamp-2" x-text="task.description || 'No description'"></p>
                                        
                                    </div>
                                </template>

                                <!-- Empty State for Column -->
                                <div x-show="getTasks(column.id).length === 0" class="border-2 border-dashed border-border rounded-xl p-4 text-center text-xs text-text-secondary">
                                    Drop tasks here
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
            
            <div x-show="activeTab === 'timeline'" style="display: none;" x-transition>
                <div class="bg-surface rounded-2xl shadow-sm border border-border p-6">
                    <h3 class="text-lg font-medium text-text mb-6">Project Timeline</h3>
                    
                    <div class="relative border-l-2 border-border ml-3 md:ml-6 space-y-8 pb-8">
                        @forelse($project->tasks->sortBy('created_at') as $task)
                            <div class="relative pl-6 md:pl-8">
                                <span class="absolute -left-[9px] top-1 w-4 h-4 rounded-full bg-surface border-2 {{ $task->status === 'done' ? 'border-emerald-500' : 'border-primary' }}"></span>
                                <div class="flex flex-col md:flex-row md:items-start md:justify-between">
                                    <div>
                                        <h4 class="font-medium text-text">{{ $task->title }}</h4>
                                        <p class="text-sm text-text-secondary mt-1">{{ $task->description }}</p>
                                    </div>
                                    <div class="mt-2 md:mt-0 flex items-center space-x-4">
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-surface-secondary text-text-secondary capitalize">
                                            {{ $task->status }}
                                        </span>
                                        <span class="text-xs text-text-secondary whitespace-nowrap">
                                            {{ $task->created_at->format('M d, Y') }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-text-secondary pl-6">No tasks available for the timeline.</p>
                        @endforelse
                    </div>
                </div>
            </div>
            
            <div x-show="activeTab === 'files'" style="display: none;" x-transition>
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-medium text-text">Project Files</h3>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Upload Area -->
                    <div class="lg:col-span-1">
                        <div class="bg-surface p-6 rounded-2xl shadow-sm border border-border">
                            <h4 class="font-medium text-text mb-4">Upload File</h4>
                            <form action="{{ route('attachments.store') }}" method="POST" enctype="multipart/form-data" x-data="{ fileName: '' }">
                                @csrf
                                <input type="hidden" name="attachable_type" value="App\Models\Project">
                                <input type="hidden" name="attachable_id" value="{{ $project->id }}">
                                
                                <div class="space-y-4">
                                    <div>
                                        <x-input-label for="file_name" value="File Name" />
                                        <x-text-input id="file_name" name="file_name" type="text" class="mt-1 block w-full text-sm" required placeholder="e.g. Design Specs" x-model="fileName" />
                                    </div>
                                    
                                    <div class="border-2 border-dashed rounded-xl p-6 flex flex-col items-center justify-center text-center relative transition-colors"
                                         x-data="{ isDropping: false }"
                                         @dragover.prevent="isDropping = true"
                                         @dragleave.prevent="isDropping = false"
                                         @drop.prevent="isDropping = false; if($event.dataTransfer.files.length) { fileName = $event.dataTransfer.files[0].name; $refs.fileInput.files = $event.dataTransfer.files; }"
                                         :class="isDropping ? 'border-primary bg-primary/5' : 'border-border'">
                                        
                                        <input type="file" name="file" class="hidden" x-ref="fileInput" @change="if($event.target.files.length) { fileName = $event.target.files[0].name; }">
                                        
                                        <svg class="w-8 h-8 mb-2 transition-colors" :class="isDropping ? 'text-primary' : 'text-text-secondary'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                                        <p class="text-sm text-text-secondary">Drag & drop or <span class="text-primary font-medium cursor-pointer" @click="$refs.fileInput.click()">browse</span></p>
                                        <p class="text-xs text-text-secondary mt-1" x-text="fileName ? 'Selected: ' + fileName : 'Mock upload: actual file will be ignored, but name used'"></p>
                                    </div>

                                    <button type="submit" x-bind:disabled="!fileName" class="w-full py-2 bg-primary text-white text-sm font-medium rounded-lg shadow-sm hover:bg-primary/90 transition disabled:opacity-50 disabled:cursor-not-allowed">
                                        Upload File
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Files List -->
                    <div class="lg:col-span-2">
                        <div class="bg-surface rounded-2xl shadow-sm border border-border overflow-hidden">
                            <ul class="divide-y divide-border">
                                @forelse($project->attachments as $file)
                                    <li class="p-4 flex items-center justify-between hover:bg-surface-secondary transition">
                                        <div class="flex items-center space-x-3">
                                            <div class="p-2 bg-surface-secondary rounded-lg text-text-secondary">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                            </div>
                                            <div>
                                                <p class="text-sm font-medium text-text">{{ $file->file_name }}</p>
                                                <p class="text-xs text-text-secondary">{{ round($file->file_size / 1024, 2) }} KB • {{ $file->created_at->format('M d, Y') }}</p>
                                            </div>
                                        </div>
                                        <div class="flex space-x-2">
                                            @if(\Illuminate\Support\Str::endsWith(strtolower($file->file_path), ['.jpg', '.jpeg', '.png', '.gif', '.webp', '.pdf']))
                                                <a href="{{ asset($file->file_path) }}" target="_blank" class="p-1.5 text-text-secondary hover:text-primary hover:bg-primary/10 rounded-lg transition" title="Preview">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                                </a>
                                            @endif
                                            
                                            <a href="{{ asset($file->file_path) }}" download="{{ $file->file_name }}" class="p-1.5 text-text-secondary hover:text-primary hover:bg-primary/10 rounded-lg transition" title="Download">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                            </a>

                                            <form action="{{ route('attachments.destroy', $file) }}" method="POST" onsubmit="return confirm('Delete this file?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-1.5 text-text-secondary hover:text-red-500 hover:bg-red-50 rounded-lg transition" title="Delete">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                </button>
                                            </form>
                                        </div>
                                    </li>
                                @empty
                                    <li class="p-12 text-center text-text-secondary">
                                        <p>No files attached to this project.</p>
                                    </li>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Task Modal -->
    <x-modal name="create-task" focusable>
        <form method="post" action="{{ route('project-tasks.store', $project) }}" class="p-6">
            @csrf
            <h2 class="text-lg font-medium text-text mb-4">
                {{ __('Add New Task') }}
            </h2>

            <div class="space-y-4">
                <div>
                    <x-input-label for="title" value="{{ __('Title') }}" />
                    <x-text-input id="title" name="title" type="text" class="mt-1 block w-full" required />
                </div>
                <div>
                    <x-input-label for="description" value="{{ __('Description') }}" />
                    <textarea id="description" name="description" class="mt-1 block w-full border-border focus:border-primary focus:ring-primary rounded-md shadow-sm" rows="3"></textarea>
                </div>
                <div>
                    <x-input-label for="status" value="{{ __('Status') }}" />
                    <select id="status" name="status" class="mt-1 block w-full border-border focus:border-primary focus:ring-primary rounded-md shadow-sm">
                        <option value="todo">To Do</option>
                        <option value="doing">Doing</option>
                        <option value="review">Review</option>
                        <option value="done">Done</option>
                    </select>
                </div>
            </div>

            <div class="mt-6 flex justify-end">
                <button type="button" class="px-4 py-2 text-sm font-medium text-text bg-surface border border-border rounded-lg shadow-sm hover:bg-surface-secondary focus:outline-none" x-on:click="$dispatch('close')">
                    {{ __('Cancel') }}
                </button>
                <button type="submit" class="ml-3 px-4 py-2 text-sm font-medium text-white bg-dark border border-transparent rounded-lg shadow-sm hover:bg-gray-800 focus:outline-none">
                    {{ __('Add Task') }}
                </button>
            </div>
        </form>
    </x-modal>

    @push('scripts')
    <script>
        function projectTabs() {
            return {
                activeTab: 'board',
            }
        }

        function kanbanBoard() {
            return {
                tasks: {!! json_encode($project->tasks) !!},
                columns: [
                    { id: 'todo', name: 'To Do', color: 'bg-gray-400' },
                    { id: 'doing', name: 'Doing', color: 'bg-blue-400' },
                    { id: 'review', name: 'Review', color: 'bg-yellow-400' },
                    { id: 'done', name: 'Done', color: 'bg-emerald-400' }
                ],
                draggedTaskId: null,
                
                getTasks(status) {
                    return this.tasks.filter(t => t.status === status).sort((a, b) => a.order - b.order);
                },
                
                onDragStart(e, taskId) {
                    this.draggedTaskId = taskId;
                    e.dataTransfer.effectAllowed = 'move';
                    setTimeout(() => {
                        e.target.classList.add('opacity-50', 'ring-2', 'ring-primary');
                    }, 0);
                },
                
                onDragEnd(e) {
                    this.draggedTaskId = null;
                    e.target.classList.remove('opacity-50', 'ring-2', 'ring-primary');
                },
                
                onDragOver(e) {
                    e.dataTransfer.dropEffect = 'move';
                },
                
                onDrop(e, status) {
                    if (!this.draggedTaskId) return;
                    
                    let taskIndex = this.tasks.findIndex(t => t.id === this.draggedTaskId);
                    if (taskIndex > -1) {
                        let task = this.tasks[taskIndex];
                        
                        if (task.status !== status) {
                            task.status = status;
                            
                            let colTasks = this.getTasks(status);
                            task.order = colTasks.length > 0 ? Math.max(...colTasks.map(t => t.order)) + 1 : 0;
                            
                            this.updateTaskStatus(task);
                        }
                    }
                    this.draggedTaskId = null;
                },
                
                async updateTaskStatus(task) {
                    try {
                        let response = await fetch(`/project-tasks/${task.id}/status`, {
                            method: 'PATCH',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                status: task.status,
                                order: task.order
                            })
                        });
                        
                        if (!response.ok) {
                            console.error('Failed to update task status');
                        }
                    } catch (error) {
                        console.error('Error updating task:', error);
                    }
                }
            }
        }
    </script>
    @endpush
</x-app-layout>
