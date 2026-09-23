<div x-data="pomodoroTimer({{ auth()->check() ? auth()->user()->pomodoro_focus : 25 }}, {{ auth()->check() ? auth()->user()->pomodoro_break : 5 }})" 
     class="fixed bottom-6 right-6 z-50 transition-all duration-300"
     :class="isOpen ? 'w-80' : 'w-14'">
    
    <!-- Minimized view -->
    <button x-show="!isOpen" @click="isOpen = true" 
            class="w-14 h-14 bg-primary text-white rounded-full shadow-lg shadow-primary/30 dark:shadow-none dark:neon-glow-primary dark:border dark:border-primary flex flex-col items-center justify-center hover:bg-primary/90 transition group">
        <svg class="w-6 h-6 mb-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        <span class="text-[10px] font-bold group-hover:scale-110 transition-transform" x-text="formattedTime"></span>
    </button>

    <!-- Expanded view -->
    <div x-show="isOpen" style="display: none;" 
         class="bg-surface rounded-3xl shadow-2xl shadow-gray-200/50 border border-border overflow-hidden flex flex-col">
        <div class="p-4 border-b border-gray-50 flex justify-between items-center bg-surface-secondary/50">
            <h3 class="font-bold text-text flex items-center text-sm">
                <svg class="w-4 h-4 mr-1.5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Pomodoro
            </h3>
            <button @click="isOpen = false" class="p-1 text-text-secondary hover:text-text-secondary hover:bg-surface-secondary rounded-full transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>
        
        <div class="p-6 flex flex-col items-center">
            <!-- Mode switch -->
            <div class="flex space-x-1 bg-surface-secondary/80 p-1 rounded-xl mb-6">
                <button @click="setMode('focus')" :class="mode === 'focus' ? 'bg-surface shadow-sm text-primary font-bold' : 'text-text-secondary hover:text-text'" class="px-4 py-1.5 rounded-lg text-xs transition-all">Focus</button>
                <button @click="setMode('break')" :class="mode === 'break' ? 'bg-surface shadow-sm text-blue-500 font-bold' : 'text-text-secondary hover:text-text'" class="px-4 py-1.5 rounded-lg text-xs transition-all">Break</button>
            </div>
            
            <!-- Timer -->
            <div class="text-6xl font-black text-text mb-8 tracking-tighter" style="font-variant-numeric: tabular-nums;" x-text="formattedTime"></div>
            
            <!-- Controls -->
            <div class="flex space-x-4">
                <button @click="toggleTimer" class="w-14 h-14 rounded-full flex items-center justify-center text-white shadow-lg transition-transform hover:scale-105" :class="mode === 'focus' ? 'bg-primary shadow-primary/30' : 'bg-blue-500 shadow-blue-500/30'">
                    <svg x-show="!isRunning" class="w-7 h-7 ml-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"></path></svg>
                    <svg x-show="isRunning" class="w-7 h-7" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zM7 8a1 1 0 012 0v4a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v4a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                </button>
                <button @click="resetTimer" class="w-14 h-14 rounded-full bg-surface-secondary border border-border flex items-center justify-center text-text-secondary hover:bg-surface-secondary transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Audio for completion -->
    <audio id="pomodoro-sound" preload="auto">
        <!-- Short notification sound -->
        <source src="https://assets.mixkit.co/active_storage/sfx/2869/2869-preview.mp3" type="audio/mpeg">
    </audio>
</div>

<script>
    function pomodoroTimer(focusMinutes = 25, breakMinutes = 5) {
        return {
            isOpen: false,
            mode: 'focus', // focus or break
            focusMinutes: focusMinutes,
            breakMinutes: breakMinutes,
            time: focusMinutes * 60, // seconds
            isRunning: false,
            interval: null,
            
            get formattedTime() {
                const minutes = Math.floor(this.time / 60);
                const seconds = this.time % 60;
                return `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
            },
            
            setMode(newMode) {
                this.mode = newMode;
                this.resetTimer();
            },
            
            toggleTimer() {
                if (this.isRunning) {
                    clearInterval(this.interval);
                    this.isRunning = false;
                } else {
                    this.isRunning = true;
                    this.interval = setInterval(() => {
                        if (this.time > 0) {
                            this.time--;
                            // Update document title for background tracking
                            document.title = `${this.formattedTime} - ${this.mode === 'focus' ? 'Focus' : 'Break'} | LifeHub`;
                        } else {
                            this.completeTimer();
                        }
                    }, 1000);
                }
            },
            
            resetTimer() {
                clearInterval(this.interval);
                this.isRunning = false;
                this.time = this.mode === 'focus' ? this.focusMinutes * 60 : this.breakMinutes * 60;
                document.title = 'LifeHub';
            },
            
            completeTimer() {
                clearInterval(this.interval);
                this.isRunning = false;
                
                const audio = document.getElementById('pomodoro-sound');
                if(audio) audio.play().catch(e => console.log('Audio play failed:', e));
                
                document.title = 'Time is up! | LifeHub';
                
                const isFocus = this.mode === 'focus';
                
                // Show notification if supported
                if ("Notification" in window && Notification.permission === "granted") {
                    new Notification("Pomodoro Complete!", {
                        body: isFocus ? "Time for a break! Great job focusing." : "Break is over! Ready to focus?",
                    });
                }
                
                // Switch mode automatically
                this.setMode(isFocus ? 'break' : 'focus');
            },
            
            init() {
                if ("Notification" in window && Notification.permission !== "granted" && Notification.permission !== "denied") {
                    Notification.requestPermission();
                }
            }
        }
    }
</script>
