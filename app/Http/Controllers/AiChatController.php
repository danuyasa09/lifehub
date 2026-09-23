<?php

namespace App\Http\Controllers;

use App\Models\AiChat;
use App\Models\AiMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiChatController extends Controller
{
    public function index()
    {
        $chats = auth()->user()->aiChats()->latest()->get();
        return view('ai.index', compact('chats'));
    }

    public function show(AiChat $ai_chat)
    {
        if ($ai_chat->user_id !== auth()->id()) abort(403);
        $chats = auth()->user()->aiChats()->latest()->get();
        $ai_chat->load('messages');
        return view('ai.index', compact('chats', 'ai_chat'));
    }

    public function store(Request $request)
    {
        $request->validate(['content' => 'required|string']);

        $chat = null;
        if ($request->has('chat_id') && $request->chat_id) {
            $chat = auth()->user()->aiChats()->findOrFail($request->chat_id);
        } else {
            $chat = auth()->user()->aiChats()->create([
                'title' => Str::limit($request->content, 30)
            ]);
        }

        // Store user message
        $userMsg = $chat->messages()->create([
            'role' => 'user',
            'content' => $request->content
        ]);

        // Build history for Gemini
        $history = [];
        $messages = $chat->messages()->orderBy('created_at', 'asc')->get();
        foreach ($messages as $msg) {
            $role = $msg->role === 'assistant' ? 'model' : 'user';
            $history[] = [
                'role' => $role,
                'parts' => [
                    ['text' => $msg->content]
                ]
            ];
        }

        // Call Gemini API
        $apiKey = env('GEMINI_API_KEY');
        if (!$apiKey) {
            $aiMsg = $chat->messages()->create([
                'role' => 'assistant',
                'content' => '⚠️ **Error:** `GEMINI_API_KEY` is not set in your `.env` file. Please add it to start using the AI.'
            ]);
            return response()->json([
                'chat_id' => $chat->id,
                'user_html' => Str::markdown($userMsg->content),
                'ai_html' => Str::markdown($aiMsg->content)
            ]);
        }

        $url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=' . $apiKey;
        
        $payload = [
            'contents' => $history,
            'systemInstruction' => [
                'parts' => [
                    ['text' => 'You are the LifeHub AI Assistant. You help the user manage their tasks, habits, and finances. Be helpful, concise, friendly, and format your answers using markdown when necessary.']
                ]
            ]
        ];

        try {
            $response = Http::timeout(30)->retry(2, 2000, function ($exception, $request) {
                // Hanya retry jika 503 (overload)
                return $exception instanceof \Illuminate\Http\Client\RequestException &&
                       $exception->response->status() === 503;
            })->post($url, $payload);
            $result = $response->json();

            if ($response->successful() && isset($result['candidates'][0]['content']['parts'][0]['text'])) {
                $aiResponseText = $result['candidates'][0]['content']['parts'][0]['text'];
            } else {
                $errorCode = $result['error']['code'] ?? $response->status();
                $errorMsg  = $result['error']['message'] ?? 'Unknown error';
                Log::error('Gemini API Error: ' . $response->body());

                if ($errorCode === 401 || $errorCode === 403) {
                    $aiResponseText = '⚠️ **API Key tidak valid.** Pastikan `GEMINI_API_KEY` di `.env` sudah benar (format: `AIzaSy...`).';
                } elseif ($errorCode === 503) {
                    $aiResponseText = '⏳ **Server AI sedang ramai.** Silakan coba lagi dalam beberapa detik.';
                } else {
                    $aiResponseText = "❌ **Terjadi kesalahan** (kode $errorCode): $errorMsg";
                }
            }
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('Gemini API Timeout: ' . $e->getMessage());
            $aiResponseText = '⏱️ **Request timeout.** Koneksi ke server AI terlalu lama. Coba lagi.';
        } catch (\Exception $e) {
            Log::error('Gemini API Exception: ' . $e->getMessage());
            $aiResponseText = '❌ **Terjadi kesalahan koneksi** saat menghubungi server AI.';
        }
        
        $aiMsg = $chat->messages()->create([
            'role' => 'assistant',
            'content' => $aiResponseText
        ]);

        return response()->json([
            'chat_id' => $chat->id,
            'user_html' => Str::markdown($userMsg->content),
            'ai_html' => Str::markdown($aiMsg->content)
        ]);
    }
}
