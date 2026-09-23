<x-app-layout>
    <div class="h-full flex -m-8" x-data="{ aiSidebarOpen: window.innerWidth > 768 }">
        <!-- Mobile Sidebar Overlay -->
        <div x-show="aiSidebarOpen" class="fixed inset-0 bg-gray-600/50 backdrop-blur-sm z-40 md:hidden" @click="aiSidebarOpen = false" style="display: none;"></div>

        <!-- Sidebar -->
        <div :class="aiSidebarOpen ? 'w-64 border-r' : 'w-0 border-r-0'" class="bg-surface-secondary border-border flex flex-col h-[calc(100vh-4rem)] shrink-0 absolute inset-y-0 left-0 z-50 transition-all duration-300 overflow-hidden md:relative">
            <div class="p-4 border-b border-border shrink-0 flex justify-between items-center">
                <a href="{{ route('ai.index') }}" class="block w-full text-center px-4 py-2 bg-surface border border-border rounded-xl shadow-sm hover:bg-surface-secondary transition text-sm font-medium text-text">
                    + New Chat
                </a>
                <button @click="aiSidebarOpen = false" class="md:hidden ml-2 p-1 text-text-secondary hover:text-text rounded-md shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <div class="flex-1 overflow-y-auto p-4 space-y-2">
                @foreach($chats as $c)
                    <a href="{{ route('ai.show', $c) }}" class="block px-3 py-2 text-sm rounded-xl transition truncate {{ isset($ai_chat) && $ai_chat->id == $c->id ? 'bg-primary/10 text-primary font-medium' : 'text-text-secondary hover:bg-surface-secondary/50' }}">
                        {{ $c->title ?? 'New Chat' }}
                    </a>
                @endforeach
            </div>
        </div>

        <!-- Chat Area -->
        <div class="flex-1 flex flex-col bg-surface h-[calc(100vh-4rem)] overflow-hidden relative" x-data="aiChat({{ isset($ai_chat) ? $ai_chat->id : 'null' }})">
            <!-- Header -->
            <div class="h-16 border-b border-border flex items-center px-4 sm:px-6 shrink-0 bg-surface z-10 w-full min-w-0">
                <button @click="aiSidebarOpen = !aiSidebarOpen" class="mr-3 p-2 text-text-secondary hover:text-text hover:bg-surface-secondary rounded-lg transition shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </button>
                <h2 class="text-lg font-semibold text-text truncate">
                    {{ isset($ai_chat) ? $ai_chat->title : 'New Conversation' }}
                </h2>
            </div>
            
            <!-- Messages -->
            <div class="flex-1 overflow-y-auto p-6 space-y-6" id="messages-container">
                @if(isset($ai_chat))
                    @foreach($ai_chat->messages as $msg)
                        @if($msg->role === 'user')
                            <div class="flex justify-end">
                                <div class="bg-primary text-white rounded-3xl rounded-tr-sm px-6 py-4 max-w-[80%] prose prose-invert">
                                    {!! Str::markdown($msg->content) !!}
                                </div>
                            </div>
                        @else
                            <div class="flex justify-start">
                                <div class="bg-surface-secondary text-text rounded-3xl rounded-tl-sm px-6 py-4 max-w-[80%] prose prose-sm dark:prose-invert">
                                    {!! Str::markdown($msg->content) !!}
                                </div>
                            </div>
                        @endif
                    @endforeach
                @else
                    <div class="flex h-full items-center justify-center">
                        <div class="text-center text-text-secondary">
                            <div class="w-16 h-16 bg-primary/10 text-primary rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                            </div>
                            <h3 class="text-xl font-semibold text-text mb-2">LifeHub AI Assistant</h3>
                            <p class="text-sm">How can I help you be more productive today?</p>
                        </div>
                    </div>
                @endif

                <!-- Dynamic bubbles will be appended here -->
                <template x-for="msg in dynamicMessages">
                    <div class="flex" :class="msg.role === 'user' ? 'justify-end' : 'justify-start'">
                        <div :class="msg.role === 'user' ? 'bg-primary text-white rounded-tr-sm prose-invert' : 'bg-surface-secondary text-text rounded-tl-sm prose-sm dark:prose-invert'" class="rounded-3xl px-6 py-4 max-w-[80%] prose" x-html="msg.html"></div>
                    </div>
                </template>

                <div x-show="loading" class="flex justify-start">
                    <div class="bg-surface-secondary text-text rounded-3xl rounded-tl-sm px-6 py-4 flex items-center space-x-1.5 h-12">
                        <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce"></div>
                        <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.15s"></div>
                        <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.3s"></div>
                    </div>
                </div>
            </div>

            <!-- Input Form -->
            <div class="p-6 bg-surface border-t border-border shrink-0">
                <form @submit.prevent="sendMessage" class="max-w-4xl mx-auto relative shadow-sm border border-border rounded-full bg-surface-secondary flex items-center">
                    <button type="button" @click="toggleListen" :class="isListening ? 'text-red-500 animate-pulse bg-red-50' : 'text-text-secondary hover:text-text hover:bg-border'" class="flex shrink-0 items-center justify-center w-10 h-10 ml-2 rounded-full transition" title="Voice Input">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"></path></svg>
                    </button>
                    <input type="text" x-model="input" :disabled="loading" placeholder="Message AI Assistant..." class="w-full bg-transparent border-0 text-text text-sm focus:ring-0 block px-4 py-4 disabled:opacity-50">
                    <button type="submit" :disabled="loading || input.trim() === ''" class="flex shrink-0 items-center justify-center w-10 h-10 mr-2 rounded-full bg-primary text-white hover:bg-primary/90 disabled:opacity-50 transition">
                        <svg class="w-4 h-4 ml-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 19V5m-7 7l7-7 7 7"></path></svg>
                    </button>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/styles/github-dark.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/highlight.min.js"></script>
    <script>
        function aiChat(initialChatId) {
            return {
                chatId: initialChatId,
                input: '',
                loading: false,
                isListening: false,
                recognition: null,
                dynamicMessages: [],
                scrollToBottom() {
                    setTimeout(() => {
                        const container = document.getElementById('messages-container');
                        container.scrollTop = container.scrollHeight;
                        
                        // Apply syntax highlighting to new code blocks
                        if (typeof hljs !== 'undefined') {
                            document.querySelectorAll('#messages-container pre code').forEach((block) => {
                                if (!block.classList.contains('hljs')) {
                                    hljs.highlightElement(block);
                                }
                            });
                        }
                    }, 50);
                },
                initSpeech() {
                    const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
                    if (SpeechRecognition) {
                        this.recognition = new SpeechRecognition();
                        this.recognition.continuous = false;
                        this.recognition.lang = 'id-ID'; // Indonesian Language
                        this.recognition.interimResults = false;

                        this.recognition.onresult = (event) => {
                            const transcript = event.results[0][0].transcript;
                            this.input += (this.input.length > 0 ? ' ' : '') + transcript;
                            this.isListening = false;
                        };

                        this.recognition.onerror = (event) => {
                            console.error('Speech recognition error', event.error);
                            this.isListening = false;
                        };

                        this.recognition.onend = () => {
                            this.isListening = false;
                        };
                    }
                },
                toggleListen() {
                    if (!this.recognition) {
                        alert('Your browser does not support Voice Input. Please try Google Chrome or Edge.');
                        return;
                    }
                    
                    if (this.isListening) {
                        this.recognition.stop();
                        this.isListening = false;
                    } else {
                        this.recognition.start();
                        this.isListening = true;
                    }
                },
                async sendMessage() {
                    if (this.input.trim() === '' || this.loading) return;
                    
                    const content = this.input;
                    this.input = '';
                    this.loading = true;

                    // Optimistic UI for user
                    this.dynamicMessages.push({
                        role: 'user',
                        html: '<p>' + content + '</p>'
                    });
                    this.scrollToBottom();

                    try {
                        const response = await fetch('{{ route("ai.store") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                content: content,
                                chat_id: this.chatId
                            })
                        });

                        const data = await response.json();
                        
                        this.dynamicMessages[this.dynamicMessages.length - 1].html = data.user_html;

                        this.dynamicMessages.push({
                            role: 'assistant',
                            html: data.ai_html
                        });
                        
                        if (!this.chatId) {
                            window.location.href = '/ai/' + data.chat_id;
                        } else {
                            this.scrollToBottom();
                        }
                    } catch (e) {
                        console.error("Error sending message", e);
                    } finally {
                        this.loading = false;
                    }
                },
                init() {
                    this.initSpeech();
                    this.scrollToBottom();
                }
            }
        }
    </script>
    @endpush
</x-app-layout>
