<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-text leading-tight">
            Daily Journal
        </h2>
    </x-slot>

    <div class="max-w-4xl mx-auto flex space-x-8 mt-4">
        
        <!-- Main Content -->
        <div class="flex-1">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-8 space-y-4 sm:space-y-0">
                <div>
                    <h1 class="text-2xl font-bold text-text">{{ $carbonDate->format('l, F j, Y') }}</h1>
                    <p class="text-text-secondary mt-1">Reflect on your day.</p>
                </div>
                
                <div class="flex flex-col-reverse sm:flex-row items-center gap-4 w-full sm:w-auto">
                    <a href="{{ route('journals.history') }}" class="w-full sm:w-auto text-center px-4 py-2 bg-surface text-text-secondary border border-border rounded-lg shadow-sm hover:bg-surface-secondary transition text-sm font-medium">
                        View History
                    </a>
                    
                    <div class="flex items-center space-x-4 w-full sm:w-auto justify-center">
                        <a href="{{ route('journals.index', ['date' => $carbonDate->copy()->subDay()->toDateString()]) }}" class="p-2 bg-surface border border-border rounded-lg hover:bg-surface-secondary transition">
                            <svg class="w-5 h-5 text-text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                        </a>
                    
                    <span class="text-sm font-medium text-text-secondary">
                        @if($carbonDate->isToday()) Today @else {{ $carbonDate->format('M j') }} @endif
                    </span>
                    
                    <a href="{{ route('journals.index', ['date' => $carbonDate->copy()->addDay()->toDateString()]) }}" class="p-2 bg-surface border border-border rounded-lg hover:bg-surface-secondary transition {{ $carbonDate->isToday() ? 'opacity-50 pointer-events-none' : '' }}">
                        <svg class="w-5 h-5 text-text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </a>
                    </div>
                </div>
            </div>

            <div class="bg-surface rounded-2xl shadow-sm border border-border p-8">
                <form method="POST" action="{{ route('journals.store') }}">
                    @csrf
                    <input type="hidden" name="date" value="{{ $carbonDate->toDateString() }}">

                    <!-- Mood Selector -->
                    <div class="mb-8">
                        <label class="block text-sm font-semibold text-text mb-4">How are you feeling?</label>
                        <div class="flex space-x-4">
                            @php
                                $moods = [
                                    'happy' => '😄',
                                    'neutral' => '😐',
                                    'sad' => '😔',
                                    'excited' => '🤩',
                                    'tired' => '🥱'
                                ];
                            @endphp
                            @foreach($moods as $value => $emoji)
                                <label class="cursor-pointer relative">
                                    <input type="radio" name="mood" value="{{ $value }}" class="peer sr-only" {{ old('mood', $journal->mood) === $value ? 'checked' : '' }}>
                                    <div class="w-12 h-12 flex items-center justify-center text-2xl bg-surface-secondary rounded-xl border-2 border-transparent peer-checked:border-primary peer-checked:bg-primary/5 hover:bg-surface-secondary transition">
                                        {{ $emoji }}
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- What Happened -->
                    <div class="mb-6">
                        <label for="what_happened" class="block text-sm font-semibold text-text mb-2">What happened today?</label>
                        <textarea name="what_happened" id="what_happened" rows="4" class="block w-full border-border bg-surface-secondary text-text rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm resize-none" placeholder="Write down the key events of your day...">{{ old('what_happened', $journal->what_happened) }}</textarea>
                    </div>

                    <!-- Gratitude -->
                    <div class="mb-6">
                        <label for="gratitude" class="block text-sm font-semibold text-text mb-2">What are you grateful for?</label>
                        <textarea name="gratitude" id="gratitude" rows="2" class="block w-full border-border bg-surface-secondary text-text rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm resize-none" placeholder="I am grateful for...">{{ old('gratitude', $journal->gratitude) }}</textarea>
                    </div>

                    <!-- Lessons Learned -->
                    <div class="mb-8">
                        <label for="lessons_learned" class="block text-sm font-semibold text-text mb-2">Lessons learned</label>
                        <textarea name="lessons_learned" id="lessons_learned" rows="2" class="block w-full border-border bg-surface-secondary text-text rounded-xl shadow-sm focus:ring-primary focus:border-primary sm:text-sm resize-none" placeholder="What did you learn today?">{{ old('lessons_learned', $journal->lessons_learned) }}</textarea>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="px-6 py-2.5 bg-primary text-white text-sm font-medium rounded-xl shadow-sm hover:bg-primary/90 transition">
                            Save Entry
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
    </div>
</x-app-layout>
