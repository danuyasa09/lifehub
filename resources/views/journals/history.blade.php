<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between w-full">
            <h2 class="font-semibold text-lg md:text-xl text-text leading-tight">
                Journal History
            </h2>
            <a href="{{ route('journals.index') }}" class="px-4 py-2 bg-surface text-text-secondary border border-border rounded-xl shadow-sm hover:bg-surface-secondary transition text-sm font-medium">
                Back to Today
            </a>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto py-8">
        @php
            $moodEmojis = [
                'happy' => '😄',
                'neutral' => '😐',
                'sad' => '😔',
                'excited' => '🤩',
                'tired' => '🥱'
            ];
        @endphp

        @forelse($journals as $month => $monthJournals)
            <div class="mb-10">
                <h3 class="text-xl font-bold text-text mb-4">{{ $month }}</h3>
                <div class="space-y-4">
                    @foreach($monthJournals as $journal)
                        <a href="{{ route('journals.index', ['date' => $journal->date]) }}" class="block bg-surface p-6 rounded-2xl border border-border shadow-sm hover:border-primary/30 hover:shadow-md transition group">
                            <div class="flex items-start justify-between">
                                <div class="flex items-center space-x-4">
                                    <div class="w-12 h-12 flex items-center justify-center text-2xl bg-surface-secondary rounded-xl group-hover:bg-primary/5 transition">
                                        {{ $journal->mood ? ($moodEmojis[$journal->mood] ?? '📝') : '📝' }}
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-text group-hover:text-primary transition">
                                            {{ \Carbon\Carbon::parse($journal->date)->format('l, F j, Y') }}
                                        </h4>
                                        @if($journal->what_happened)
                                            <p class="text-sm text-text-secondary mt-1 line-clamp-2">
                                                {{ Str::limit($journal->what_happened, 100) }}
                                            </p>
                                        @else
                                            <p class="text-sm text-text-secondary mt-1 italic">
                                                No content written for this day.
                                            </p>
                                        @endif
                                    </div>
                                </div>
                                <svg class="w-5 h-5 text-text-secondary opacity-0 group-hover:opacity-100 group-hover:text-primary transition translate-x-[-10px] group-hover:translate-x-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @empty
            <div class="bg-surface p-12 rounded-2xl border border-border text-center">
                <svg class="w-12 h-12 text-text-secondary mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477-4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                <h3 class="text-lg font-medium text-text mb-2">No Journals Yet</h3>
                <p class="text-text-secondary mb-6">You haven't written any journal entries.</p>
                <a href="{{ route('journals.index') }}" class="inline-flex items-center px-4 py-2 bg-primary text-white text-sm font-medium rounded-xl shadow-sm hover:bg-primary/90 transition">
                    Write Today's Journal
                </a>
            </div>
        @endforelse
    </div>
</x-app-layout>
