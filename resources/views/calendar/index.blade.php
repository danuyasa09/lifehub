<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-text leading-tight">
            {{ __('Calendar') }}
        </h2>
    </x-slot>

    <div x-data="calendarManager({{ $tasks->toJson() }})" class="space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <h1 class="text-3xl font-bold tracking-tight text-text" x-text="monthName + ' ' + year"></h1>
            <div class="flex items-center space-x-4">
                <button @click="previousMonth()" class="p-2 rounded-full hover:bg-surface-secondary transition">
                    <svg class="w-5 h-5 text-text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                </button>
                <button @click="resetToToday()" class="px-3 py-1 text-sm font-medium text-text bg-surface border border-border rounded-lg hover:bg-surface-secondary transition">Today</button>
                <button @click="nextMonth()" class="p-2 rounded-full hover:bg-surface-secondary transition">
                    <svg class="w-5 h-5 text-text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </button>
                <button @click="openModal()" class="bg-primary hover:bg-primary/90 text-white px-4 py-2 rounded-xl font-medium transition shadow-sm shadow-primary/30 flex items-center space-x-2 ml-4">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    <span>New Task</span>
                </button>
            </div>
        </div>

        <!-- Calendar Grid -->
        <div class="bg-surface rounded-[20px] shadow-sm border border-border overflow-hidden">
            <div class="grid grid-cols-7 border-b border-border bg-surface-secondary/50">
                <template x-for="day in daysOfWeek" :key="day">
                    <div class="py-3 text-center text-xs font-semibold text-text-secondary uppercase tracking-wider" x-text="day"></div>
                </template>
            </div>
            <div class="grid grid-cols-7 auto-rows-fr">
                <template x-for="(day, index) in blankDays" :key="'blank-'+index">
                    <div class="border-b border-r border-border min-h-[120px] bg-surface-secondary/30"></div>
                </template>
                <template x-for="(date, index) in daysInMonth" :key="'date-'+index">
                    <div class="border-b border-r border-border min-h-[120px] p-2 transition hover:bg-surface-secondary" :class="{'bg-primary/5': isToday(date)}">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium w-7 h-7 flex items-center justify-center rounded-full" :class="{'bg-primary text-white': isToday(date), 'text-text': !isToday(date)}" x-text="date"></span>
                        </div>
                        <div class="mt-2 space-y-1 overflow-y-auto max-h-[80px] hide-scrollbar">
                            <template x-for="task in getTasksForDate(date)" :key="task.id">
                                <div @click="openModal(task)" class="text-xs truncate px-2 py-1 rounded cursor-pointer transition border"
                                     :class="{
                                         'bg-danger/10 text-danger border-danger/20': task.priority === 'urgent',
                                         'bg-warning/10 text-warning border-warning/20': task.priority === 'high',
                                         'bg-primary/10 text-primary border-primary/20': task.priority === 'medium',
                                         'bg-surface-secondary text-text border-border': task.priority === 'low',
                                         'opacity-50 line-through': task.status === 'completed'
                                     }">
                                    <span x-text="task.title"></span>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <x-task-modal :categories="$categories" />
    </div>

    <script>
        function calendarManager(tasks) {
            return {
                tasks: tasks,
                month: new Date().getMonth(),
                year: new Date().getFullYear(),
                daysOfWeek: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
                blankDays: [],
                daysInMonth: [],
                monthName: '',
                
                isModalOpen: false,
                isEditing: false,
                formAction: '{{ route('tasks.store') }}',
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

                init() {
                    this.getDays();
                },

                getDays() {
                    let daysInMonth = new Date(this.year, this.month + 1, 0).getDate();
                    let firstDayOfWeek = new Date(this.year, this.month, 1).getDay();

                    this.monthName = new Date(this.year, this.month).toLocaleString('default', { month: 'long' });
                    
                    this.blankDays = Array.from({ length: firstDayOfWeek });
                    this.daysInMonth = Array.from({ length: daysInMonth }, (_, i) => i + 1);
                },

                nextMonth() {
                    if (this.month === 11) {
                        this.month = 0;
                        this.year++;
                    } else {
                        this.month++;
                    }
                    this.getDays();
                },

                previousMonth() {
                    if (this.month === 0) {
                        this.month = 11;
                        this.year--;
                    } else {
                        this.month--;
                    }
                    this.getDays();
                },
                
                resetToToday() {
                    this.month = new Date().getMonth();
                    this.year = new Date().getFullYear();
                    this.getDays();
                },

                isToday(date) {
                    const today = new Date();
                    return date === today.getDate() && this.month === today.getMonth() && this.year === today.getFullYear();
                },

                getTasksForDate(date) {
                    // due_date format is YYYY-MM-DD from PHP JSON (Carbon dates serialize to ISO string in newer Laravel or YYYY-MM-DDTHH:MM:SS.000000Z)
                    // Let's match carefully by extracting YYYY-MM-DD part from the task's due_date string
                    const targetDateStr = `${this.year}-${String(this.month + 1).padStart(2, '0')}-${String(date).padStart(2, '0')}`;
                    return this.tasks.filter(t => {
                        if (!t.due_date) return false;
                        // Handle Laravel Carbon JSON serialization (either Y-m-d or ISO8601)
                        const taskDate = String(t.due_date).substring(0, 10);
                        return taskDate === targetDateStr;
                    });
                },

                openModal(task = null) {
                    this.isEditing = !!task;
                    if (task) {
                        this.formAction = `/tasks/${task.id}`;
                        this.form = {
                            title: task.title || '',
                            description: task.description || '',
                            due_date: task.due_date ? String(task.due_date).substring(0, 10) : '',
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
