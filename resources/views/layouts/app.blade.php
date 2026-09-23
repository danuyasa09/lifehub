<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'LifeHub') }}</title>

        <!-- PWA Meta Tags -->
        <link rel="manifest" href="{{ asset('manifest.json') }}">
        <meta name="theme-color" content="#4F46E5">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="default">
        <meta name="apple-mobile-web-app-title" content="LifeHub">
        <link rel="apple-touch-icon" href="https://laravel.com/img/logomark.min.svg">

        <!-- Theme Initialization -->
        <script>
            if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        </script>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased text-text bg-background flex h-screen overflow-hidden" x-data="{ sidebarOpen: false }">
        
        <!-- Sidebar -->
        <x-sidebar />

        <!-- Main Content -->
        <div class="flex-1 flex flex-col h-screen overflow-y-auto bg-background text-text">
            
            <header class="h-16 flex items-center justify-between px-4 md:px-8 py-4 bg-surface/80 backdrop-blur-md sticky top-0 z-10 border-b border-border">
                <div class="flex items-center space-x-4 flex-1 min-w-0">
                    <!-- Hamburger Menu (Mobile) -->
                    <button @click="sidebarOpen = true" class="md:hidden p-2 text-text-secondary hover:text-text hover:bg-surface-secondary rounded-lg transition shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                    </button>

                    @isset($header)
                        <div class="flex-1 min-w-0">
                            {{ $header }}
                        </div>
                    @endisset
                </div>

                <div class="flex items-center space-x-2 sm:space-x-4 shrink-0 ml-4">
                    
                    <!-- Theme Toggle -->
                    <button x-data="{
                        darkMode: document.documentElement.classList.contains('dark'),
                        toggle() {
                            this.darkMode = !this.darkMode;
                            if (this.darkMode) {
                                document.documentElement.classList.add('dark');
                                localStorage.setItem('theme', 'dark');
                            } else {
                                document.documentElement.classList.remove('dark');
                                localStorage.setItem('theme', 'light');
                            }
                        }
                    }" @click="toggle()" class="p-2 text-text-secondary hover:text-text hover:bg-surface-secondary rounded-full transition relative">
                        <svg x-show="!darkMode" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
                        <svg x-show="darkMode" style="display: none;" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    </button>

                    <!-- Notification Bell -->
                    <div x-data="{ open: false, unreadCount: {{ auth()->user()->unreadNotifications->count() }} }" class="relative">
                        <button @click="open = !open" class="relative p-2 text-text-secondary hover:text-text hover:bg-surface-secondary rounded-full transition">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                            <span x-show="unreadCount > 0" x-text="unreadCount" class="absolute top-0 right-0 inline-flex items-center justify-center px-1.5 py-0.5 text-xs font-bold leading-none text-white bg-danger rounded-full"></span>
                        </button>
                        
                        <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-2 w-80 bg-surface rounded-xl shadow-lg py-2 border border-border z-50 overflow-hidden">
                            <div class="px-4 py-2 border-b border-border flex justify-between items-center">
                                <h3 class="text-sm font-semibold text-text">Notifications</h3>
                            </div>
                            <div class="max-h-64 overflow-y-auto">
                                @forelse(auth()->user()->unreadNotifications as $notification)
                                    <div id="notif-{{ $notification->id }}" class="px-4 py-3 hover:bg-surface-secondary border-b border-border transition relative group flex justify-between items-start">
                                        <div class="text-sm text-text pr-4">
                                            {{ $notification->data['message'] ?? 'New notification' }}
                                            <div class="text-xs text-text-secondary mt-1">{{ $notification->created_at->diffForHumans() }}</div>
                                        </div>
                                        <button @click="
                                            fetch('/notifications/{{ $notification->id }}/read', {
                                                method: 'PATCH',
                                                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
                                            }).then(() => {
                                                document.getElementById('notif-{{ $notification->id }}').remove();
                                                unreadCount--;
                                            });
                                        " class="text-xs text-primary opacity-0 group-hover:opacity-100 transition whitespace-nowrap">Mark Read</button>
                                    </div>
                                @empty
                                    <div class="px-4 py-6 text-sm text-text-secondary text-center">No new notifications.</div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <!-- User Dropdown (Alpine.js) -->
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" class="flex items-center space-x-2 text-sm focus:outline-none bg-surface border border-border rounded-full p-1.5 sm:px-4 sm:py-1.5 hover:bg-surface-secondary transition">
                            <div class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold shrink-0">
                                {{ substr(Auth::user()->name, 0, 1) }}
                            </div>
                            <span class="hidden sm:inline-block font-medium text-text">{{ Auth::user()->name }}</span>
                            <svg class="hidden sm:block w-4 h-4 text-text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>

                        <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 mt-2 w-48 bg-surface rounded-xl shadow-lg py-2 border border-border z-50">
                            <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-text hover:bg-surface-secondary">Profile</a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-danger hover:bg-surface-secondary">
                                    Log Out
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="p-4 md:p-8">
                {{ $slot }}
            </main>
        </div>

        <!-- Command Palette (Cmd+K) -->
        <x-command-palette />
        
        <!-- Pomodoro Timer -->
        <x-pomodoro />
        
        @stack('scripts')
        
        @if(session('xp_gained'))
            <script>
                document.addEventListener('DOMContentLoaded', () => {
                    setTimeout(() => {
                        window.dispatchEvent(new CustomEvent('xp-gained', { detail: {{ session('xp_gained') }} }));
                    }, 500);
                });
            </script>
        @endif
        
        <script>
            let deferredPrompt;
            window.addEventListener('beforeinstallprompt', (e) => {
                e.preventDefault();
                deferredPrompt = e;
                window.dispatchEvent(new Event('pwa-install-available'));
            });
            
            window.installPwa = async () => {
                if (deferredPrompt) {
                    deferredPrompt.prompt();
                    const { outcome } = await deferredPrompt.userChoice;
                    if (outcome === 'accepted') {
                        deferredPrompt = null;
                        window.dispatchEvent(new Event('pwa-installed'));
                    }
                }
            };

            if ('serviceWorker' in navigator) {
                window.addEventListener('load', () => {
                    navigator.serviceWorker.register('/sw.js').then((registration) => {
                        console.log('ServiceWorker registration successful with scope: ', registration.scope);
                    }, (err) => {
                        console.log('ServiceWorker registration failed: ', err);
                    });
                });
            }
        </script>
    </body>
</html>
