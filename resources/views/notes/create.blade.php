<x-app-layout>
    <x-slot name="header">
        Create Note
    </x-slot>

    <!-- Load Marked.js and DOMPurify via CDN for Markdown rendering -->
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/dompurify/3.0.6/purify.min.js"></script>

    <div class="h-[calc(100vh-8rem)] -mt-4 -mb-8 -mx-8 bg-surface flex flex-col"
         x-data="{ 
             content: '',
             preview: false,
             get parsedContent() {
                 return DOMPurify.sanitize(marked.parse(this.content));
             }
         }">
        
        <form method="POST" action="{{ route('notes.store') }}" class="flex flex-col h-full">
            @csrf

            <!-- Toolbar -->
            <div class="flex items-center justify-between px-8 py-4 border-b border-border bg-surface">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('notes.index') }}" class="text-text-secondary hover:text-text transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    </a>
                    
                    <div class="flex bg-surface-secondary p-1 rounded-lg">
                        <button type="button" @click="preview = false" class="px-4 py-1.5 text-sm font-medium rounded-md transition" :class="!preview ? 'bg-surface shadow-sm text-text' : 'text-text-secondary hover:text-text'">Write</button>
                        <button type="button" @click="preview = true" class="px-4 py-1.5 text-sm font-medium rounded-md transition" :class="preview ? 'bg-surface shadow-sm text-text' : 'text-text-secondary hover:text-text'">Preview</button>
                    </div>
                </div>

                <div class="flex items-center space-x-4">
                    <!-- Favorite Toggle -->
                    <label class="flex items-center cursor-pointer text-text-secondary hover:text-yellow-500 transition">
                        <input type="checkbox" name="is_favorite" value="1" class="peer sr-only">
                        <svg class="w-6 h-6 peer-checked:text-yellow-400 peer-checked:fill-current" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11.049 2.927c.3-.921 1.592-.921 1.892 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                    </label>

                    <button type="submit" class="px-6 py-2 bg-primary text-white text-sm font-medium rounded-xl shadow-sm hover:bg-primary/90 transition">
                        Save Note
                    </button>
                </div>
            </div>

            <!-- Meta Data (Title, Folder, Tags) -->
            <div class="px-8 py-6 border-b border-border space-y-4">
                <input type="text" name="title" placeholder="Note Title" required class="w-full text-3xl font-bold text-text border-none bg-transparent p-0 focus:ring-0 placeholder-gray-300">
                
                <div class="flex space-x-6">
                    <div class="flex-1">
                        <input type="text" name="folder" placeholder="Folder (Optional)" class="w-full text-sm text-text-secondary border-none bg-transparent p-0 focus:ring-0 placeholder-gray-300">
                    </div>
                    <div class="flex-1">
                        <input type="text" name="tags" placeholder="Tags (comma separated)" class="w-full text-sm text-text-secondary border-none bg-transparent p-0 focus:ring-0 placeholder-gray-300">
                    </div>
                </div>
            </div>

            <!-- Editor / Preview Area -->
            <div class="flex-1 overflow-hidden flex relative">
                <!-- Write Mode -->
                <textarea 
                    name="content" 
                    x-model="content"
                    x-show="!preview"
                    class="w-full h-full p-8 border-none resize-none focus:ring-0 text-text bg-transparent font-mono"
                    placeholder="Write your note here using Markdown..."
                ></textarea>

                <!-- Preview Mode -->
                <div 
                    x-show="preview" 
                    class="w-full h-full p-8 overflow-y-auto prose prose-indigo dark:prose-invert max-w-none"
                    x-html="parsedContent"
                ></div>
            </div>
        </form>
    </div>
</x-app-layout>
