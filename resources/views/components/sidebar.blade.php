<!-- Mobile overlay backdrop -->
<div x-show="sidebarOpen" x-transition.opacity class="fixed inset-0 bg-gray-600/50 backdrop-blur-sm z-30 md:hidden" @click="sidebarOpen = false" style="display: none;"></div>

<aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="w-64 bg-surface border-r border-border flex flex-col justify-between h-screen shrink-0 fixed inset-y-0 left-0 z-40 transform transition-transform duration-300 md:relative md:translate-x-0">
    <div class="p-6">
        <!-- Logo -->
        <div class="flex items-center justify-between mb-8">
            <a href="{{ route('dashboard') }}" class="flex items-center space-x-3">
                <div class="w-8 h-8 bg-primary rounded-xl flex items-center justify-center shadow-sm shadow-primary/30">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                </div>
                <span class="text-xl font-bold tracking-tight text-text">LifeHub</span>
            </a>
            <!-- Close Sidebar Mobile -->
            <button @click="sidebarOpen = false" class="md:hidden text-text-secondary hover:text-text">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <!-- Gamification Stats -->
        @auth
        <div class="mb-6 px-3" x-data="{ showXpGain: false, xpGained: 0 }" 
             @xp-gained.window="xpGained = $event.detail; showXpGain = true; setTimeout(() => showXpGain = false, 3000)">
            <div class="bg-surface rounded-xl p-4 border border-border/50 dark:border-transparent shadow-sm dark:dark-card-glow relative overflow-hidden">
                <!-- Floating XP animation -->
                <div x-show="showXpGain" x-transition.duration.500ms class="absolute top-2 right-4 text-success font-bold text-sm" style="display: none;">
                    +<span x-text="xpGained"></span> XP
                </div>
                
                <div class="flex justify-between items-center mb-3 relative z-10">
                    <div>
                        <p class="text-[10px] text-text-secondary font-bold uppercase tracking-wider">{{ __('Level') }} <span class="text-primary">{{ auth()->user()->level }}</span></p>
                        <p class="text-sm font-bold text-text mt-0.5">{{ auth()->user()->experience }} XP</p>
                    </div>
                    @php
                        $currentLevel = auth()->user()->level;
                        $nextLevelXP = pow($currentLevel, 2) * 100;
                        $prevLevelXP = pow($currentLevel - 1, 2) * 100;
                        $xpInCurrentLevel = auth()->user()->experience - $prevLevelXP;
                        $xpNeededForNextLevel = $nextLevelXP - $prevLevelXP;
                        $progressPercentage = min(100, max(0, ($xpInCurrentLevel / $xpNeededForNextLevel) * 100));
                    @endphp
                    <p class="text-xs text-text-secondary font-medium">{{ $nextLevelXP }}</p>
                </div>
                <div class="w-full bg-surface-secondary rounded-full h-1.5 relative z-10 overflow-hidden">
                    <div class="bg-[#4F46E5] h-1.5 rounded-full transition-all duration-1000 ease-out" style="{{ 'width: ' . $progressPercentage . '%' }}"></div>
                </div>
            </div>
        </div>
        @endauth

        <!-- Navigation -->
        <nav class="space-y-1">
            @php
                $navItems = [
                    ['name' => __('Dashboard'), 'route' => 'dashboard', 'icon' => 'M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z'],
                    ['name' => __('Calendar'), 'route' => 'calendar.index', 'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
                    ['name' => __('Tasks'), 'route' => 'tasks.index', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4'],
                    ['name' => __('Notes'), 'route' => 'notes.index', 'icon' => 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z'],
                    ['name' => __('Habits'), 'route' => 'habits.index', 'icon' => 'M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
                    ['name' => __('Journal'), 'route' => 'journals.index', 'icon' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477-4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253'],
                    ['name' => __('Projects'), 'route' => 'projects.index', 'icon' => 'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10'],
                    ['name' => __('Finance'), 'route' => 'finances.index', 'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
                    ['name' => __('Analytics'), 'route' => 'analytics.index', 'icon' => 'M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z'],
                    ['name' => __('AI Assistant'), 'route' => 'ai.index', 'icon' => 'M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z'],
                ];
            @endphp

            @foreach ($navItems as $item)
                @php
                    $isActive = request()->routeIs($item['route']);
                @endphp
                <a href="{{ $item['route'] !== '#' ? route($item['route']) : '#' }}" class="flex items-center px-3 py-2.5 text-sm font-medium rounded-xl transition-all duration-200 {{ $isActive ? 'bg-primary dark:bg-transparent text-white dark:text-primary dark:neon-border-glow shadow-sm shadow-primary/30' : 'text-text-secondary hover:bg-surface-secondary hover:text-text' }}">
                    <svg class="w-5 h-5 mr-3 {{ $isActive ? 'text-white dark:text-primary' : 'text-text-secondary' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $item['icon'] }}"></path></svg>
                    {{ $item['name'] }}
                </a>
            @endforeach
        </nav>
    </div>

    <div class="p-6 space-y-2">
        <div x-data="{ showInstall: false }" 
             @pwa-install-available.window="showInstall = true"
             @pwa-installed.window="showInstall = false">
            <button x-show="showInstall" @click="window.installPwa()" style="display: none;" class="w-full flex items-center justify-center px-3 py-2.5 text-sm font-medium rounded-xl text-primary bg-primary/10 hover:bg-primary/20 transition-all duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                Install App
            </button>
        </div>
        <a href="{{ route('profile.edit') }}" class="flex items-center px-3 py-2.5 text-sm font-medium rounded-xl text-text-secondary hover:bg-surface-secondary hover:text-text transition-all duration-200">
            <svg class="w-5 h-5 mr-3 text-text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
            {{ __('Settings') }}
        </a>
    </div>
</aside>
