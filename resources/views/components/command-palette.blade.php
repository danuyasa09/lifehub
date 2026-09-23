<div x-data="commandPalette()"
     x-on:keydown.window.prevent.cmd.k="openPalette()"
     x-on:keydown.window.prevent.ctrl.k="openPalette()"
     x-show="isOpen"
     class="relative z-50"
     style="display: none;">

    <div x-show="isOpen" x-transition.opacity class="fixed inset-0 bg-gray-500/50 backdrop-blur-sm transition-opacity"></div>

    <div class="fixed inset-0 z-10 w-screen overflow-y-auto p-4 sm:p-6 md:p-20">
        <div x-show="isOpen" x-transition.scale.origin.top @click.away="closePalette()" class="mx-auto max-w-2xl transform divide-y divide-border overflow-hidden rounded-2xl bg-surface shadow-2xl ring-1 ring-black ring-opacity-5 transition-all">
            
            <div class="relative">
                <svg class="pointer-events-none absolute left-4 top-3.5 h-5 w-5 text-text-secondary" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd" />
                </svg>
                <input x-model="searchQuery" @input.debounce.300ms="fetchResults" @keydown.arrow-down.prevent="moveDown()" @keydown.arrow-up.prevent="moveUp()" @keydown.enter.prevent="selectItem()" type="text" class="h-12 w-full border-0 bg-transparent pl-11 pr-4 text-text placeholder:text-text-secondary focus:ring-0 sm:text-sm" placeholder="Search tasks, notes, projects... (Cmd+K)" autofocus>
            </div>

            <!-- Results -->
            <ul class="max-h-80 scroll-py-2 overflow-y-auto p-2 text-sm text-text" x-show="results.length > 0">
                <template x-for="(result, index) in results" :key="index">
                    <li :class="{'bg-primary text-white': selectedIndex === index, 'text-text': selectedIndex !== index}"
                        class="cursor-pointer select-none rounded-xl px-4 py-2"
                        @click="window.location.href = result.url"
                        @mouseenter="selectedIndex = index">
                        <div class="flex items-center justify-between">
                            <span x-text="result.title" class="font-medium"></span>
                            <span x-text="result.type" :class="{'text-primary-100': selectedIndex === index, 'text-text-secondary': selectedIndex !== index}" class="text-xs"></span>
                        </div>
                    </li>
                </template>
            </ul>
            
            <!-- No results -->
            <div x-show="searchQuery !== '' && results.length === 0" class="px-6 py-14 text-center text-sm sm:px-14">
                <svg class="mx-auto h-6 w-6 text-text-secondary" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <p class="mt-4 font-semibold text-text">No results found</p>
                <p class="mt-2 text-text-secondary">We couldn't find anything with that term. Please try again.</p>
            </div>
            
        </div>
    </div>
</div>

<script>
    function commandPalette() {
        return {
            isOpen: false,
            searchQuery: '',
            results: [],
            selectedIndex: 0,

            openPalette() {
                this.isOpen = true;
                setTimeout(() => {
                    this.$el.querySelector('input').focus();
                }, 50);
            },
            closePalette() {
                this.isOpen = false;
                this.searchQuery = '';
                this.results = [];
                this.selectedIndex = 0;
            },
            async fetchResults() {
                if (this.searchQuery.length < 2) {
                    this.results = [];
                    return;
                }
                const response = await fetch(`/search?q=${encodeURIComponent(this.searchQuery)}`);
                this.results = await response.json();
                this.selectedIndex = 0;
            },
            moveDown() {
                if (this.selectedIndex < this.results.length - 1) {
                    this.selectedIndex++;
                }
            },
            moveUp() {
                if (this.selectedIndex > 0) {
                    this.selectedIndex--;
                }
            },
            selectItem() {
                if (this.results.length > 0 && this.results[this.selectedIndex]) {
                    window.location.href = this.results[this.selectedIndex].url;
                }
            }
        }
    }
</script>
