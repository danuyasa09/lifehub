<x-app-layout>
    <x-slot name="header">
        Notes
    </x-slot>

    <div class="flex flex-col md:flex-row h-[calc(100vh-8rem)] -mt-4 -mb-8 -mx-4 md:-mx-8">
        
        <!-- Secondary Sidebar (Folders & Tags) -->
        <div class="w-full md:w-64 border-b md:border-b-0 md:border-r border-border bg-surface-secondary/50 flex flex-col shrink-0 md:h-full max-h-48 md:max-h-full">
            <div class="p-6 border-b border-border">
                <a href="{{ route('notes.create') }}" class="w-full flex justify-center items-center px-4 py-2 bg-primary text-white text-sm font-medium rounded-xl shadow-sm hover:bg-primary/90 transition">
                    + New Note
                </a>
            </div>
            
            <div class="flex-1 overflow-y-auto p-4 space-y-6">
                
                <!-- Filters -->
                <div>
                    <h3 class="px-3 text-xs font-semibold text-text-secondary uppercase tracking-wider mb-2">Filters</h3>
                    <div class="space-y-1">
                        <a href="{{ route('notes.index') }}" class="flex items-center justify-between px-3 py-2 text-sm font-medium rounded-lg {{ !request('folder') && !request('tag') && !request('favorites') ? 'bg-surface shadow-sm text-primary' : 'text-text-secondary hover:bg-surface-secondary' }}">
                            <span>All Notes</span>
                        </a>
                        <a href="{{ route('notes.index', ['favorites' => 1]) }}" class="flex items-center justify-between px-3 py-2 text-sm font-medium rounded-lg {{ request('favorites') ? 'bg-surface shadow-sm text-primary' : 'text-text-secondary hover:bg-surface-secondary' }}">
                            <span class="flex items-center"><svg class="w-4 h-4 mr-2 text-yellow-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>Favorites</span>
                        </a>
                    </div>
                </div>

                <!-- Folders -->
                @if($folders->count() > 0)
                <div>
                    <h3 class="px-3 text-xs font-semibold text-text-secondary uppercase tracking-wider mb-2">Folders</h3>
                    <div class="space-y-1">
                        @foreach($folders as $folder)
                            <a href="{{ route('notes.index', ['folder' => $folder]) }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg {{ request('folder') == $folder ? 'bg-surface shadow-sm text-primary' : 'text-text-secondary hover:bg-surface-secondary' }}">
                                <svg class="w-4 h-4 mr-2 text-text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path></svg>
                                {{ $folder }}
                            </a>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Tags -->
                @if($tags->count() > 0)
                <div>
                    <h3 class="px-3 text-xs font-semibold text-text-secondary uppercase tracking-wider mb-2">Tags</h3>
                    <div class="flex flex-wrap gap-2 px-3">
                        @foreach($tags as $tag)
                            <a href="{{ route('notes.index', ['tag' => $tag->name]) }}" class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium {{ request('tag') == $tag->name ? 'bg-primary text-white' : 'bg-surface-secondary text-text-secondary hover:bg-surface-secondary' }}">
                                #{{ $tag->name }}
                            </a>
                        @endforeach
                    </div>
                </div>
                @endif
                
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="flex-1 overflow-y-auto bg-surface p-4 md:p-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($notes as $note)
                    <a href="{{ route('notes.edit', $note) }}" class="group block p-4 sm:p-6 bg-surface border border-border rounded-2xl shadow-sm hover:shadow-md hover:border-primary/30 transition duration-200">
                        <div class="flex justify-between items-start mb-4">
                            <h3 class="text-lg font-semibold text-text truncate">{{ $note->title }}</h3>
                            @if($note->is_favorite)
                                <svg class="w-5 h-5 text-yellow-400 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                            @endif
                        </div>
                        <div class="text-sm text-text-secondary line-clamp-3 mb-4">
                            {{ strip_tags($note->content) }}
                        </div>
                        <div class="flex items-center justify-between text-xs text-text-secondary mt-auto">
                            <span>{{ $note->updated_at->diffForHumans() }}</span>
                            <div class="flex space-x-2">
                                @foreach($note->tags->take(2) as $tag)
                                    <span class="inline-block w-2 h-2 rounded-full" style="{{ 'background-color: ' . $tag->color }}"></span>
                                @endforeach
                                @if($note->tags->count() > 2)
                                    <span class="text-[10px]">+{{ $note->tags->count() - 2 }}</span>
                                @endif
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="col-span-full py-12 text-center text-text-secondary">
                        <svg class="w-12 h-12 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        <p class="text-lg font-medium text-text">No notes found.</p>
                        <p class="text-sm mt-1">Create a new note to get started.</p>
                    </div>
                @endforelse
            </div>
        </div>

    </div>
</x-app-layout>
