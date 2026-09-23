<!-- Slide-over Modal -->
<div x-show="isModalOpen" style="display: none;" class="relative z-50" aria-labelledby="slide-over-title" role="dialog" aria-modal="true">
    <div x-show="isModalOpen" x-transition.opacity class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm transition-opacity"></div>
    
    <div class="fixed inset-0 overflow-hidden">
        <div class="absolute inset-0 overflow-hidden">
            <div class="pointer-events-none fixed inset-y-0 right-0 flex max-w-full pl-10">
                <div x-show="isModalOpen" 
                        x-transition:enter="transform transition ease-in-out duration-300 sm:duration-500" 
                        x-transition:enter-start="translate-x-full" 
                        x-transition:enter-end="translate-x-0" 
                        x-transition:leave="transform transition ease-in-out duration-300 sm:duration-500" 
                        x-transition:leave-start="translate-x-0" 
                        x-transition:leave-end="translate-x-full" 
                        @click.away="closeModal()"
                        class="pointer-events-auto w-screen max-w-md">
                    
                    <div class="flex h-full flex-col overflow-y-scroll bg-surface shadow-xl">
                        <div class="px-6 py-6 border-b border-border flex items-center justify-between">
                            <h2 class="text-xl font-semibold text-text" id="slide-over-title" x-text="isEditing ? 'Edit Task' : 'New Task'"></h2>
                            <button type="button" @click="closeModal()" class="text-text-secondary hover:text-text-secondary focus:outline-none">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                        
                        <form :action="formAction" method="POST" class="flex-1 flex flex-col">
                            @csrf
                            <template x-if="isEditing">
                                <input type="hidden" name="_method" value="PUT">
                            </template>
                            
                            <div class="flex-1 p-6 space-y-5">
                                <div>
                                    <label for="title" class="block text-sm font-medium text-text">Task Title</label>
                                    <input type="text" name="title" id="title" x-model="form.title" required class="mt-1 block w-full rounded-xl border-border shadow-sm focus:border-primary focus:ring-primary sm:text-sm">
                                </div>
                                
                                <div>
                                    <label for="description" class="block text-sm font-medium text-text">Description</label>
                                    <textarea id="description" name="description" x-model="form.description" rows="3" class="mt-1 block w-full rounded-xl border-border shadow-sm focus:border-primary focus:ring-primary sm:text-sm"></textarea>
                                </div>
                                
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label for="due_date" class="block text-sm font-medium text-text">Due Date</label>
                                        <input type="date" name="due_date" id="due_date" x-model="form.due_date" class="mt-1 block w-full rounded-xl border-border shadow-sm focus:border-primary focus:ring-primary sm:text-sm">
                                    </div>
                                    <div>
                                        <label for="priority" class="block text-sm font-medium text-text">Priority</label>
                                        <select id="priority" name="priority" x-model="form.priority" class="mt-1 block w-full rounded-xl border-border shadow-sm focus:border-primary focus:ring-primary sm:text-sm">
                                            <option value="low">Low</option>
                                            <option value="medium">Medium</option>
                                            <option value="high">High</option>
                                            <option value="urgent">Urgent</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label for="category_id" class="block text-sm font-medium text-text">Category</label>
                                        <select id="category_id" name="category_id" x-model="form.category_id" class="mt-1 block w-full rounded-xl border-border shadow-sm focus:border-primary focus:ring-primary sm:text-sm">
                                            <option value="">None</option>
                                            @foreach($categories as $cat)
                                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label for="status" class="block text-sm font-medium text-text">Status</label>
                                        <select id="status" name="status" x-model="form.status" class="mt-1 block w-full rounded-xl border-border shadow-sm focus:border-primary focus:ring-primary sm:text-sm">
                                            <option value="pending">Pending</option>
                                            <option value="in_progress">In Progress</option>
                                            <option value="completed">Completed</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label for="progress" class="block text-sm font-medium text-text">Progress (%)</label>
                                        <input type="number" name="progress" id="progress" min="0" max="100" x-model="form.progress" class="mt-1 block w-full rounded-xl border-border shadow-sm focus:border-primary focus:ring-primary sm:text-sm">
                                    </div>
                                    <div>
                                        <label for="is_recurring" class="block text-sm font-medium text-text">Recurring</label>
                                        <select id="is_recurring" name="is_recurring" x-model="form.is_recurring" class="mt-1 block w-full rounded-xl border-border shadow-sm focus:border-primary focus:ring-primary sm:text-sm">
                                            <option value="none">None</option>
                                            <option value="daily">Daily</option>
                                            <option value="weekly">Weekly</option>
                                            <option value="monthly">Monthly</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="border-t border-border px-6 py-4 bg-surface-secondary flex justify-end space-x-3">
                                <button type="button" @click="closeModal()" class="px-4 py-2 text-sm font-medium text-text bg-surface border border-border rounded-xl hover:bg-surface-secondary focus:outline-none transition">Cancel</button>
                                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-primary rounded-xl hover:bg-primary/90 focus:outline-none transition shadow-sm shadow-primary/30" x-text="isEditing ? 'Save Changes' : 'Create Task'"></button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
