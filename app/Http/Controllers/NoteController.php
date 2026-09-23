<?php

namespace App\Http\Controllers;

use App\Models\Note;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NoteController extends Controller
{
    public function index(Request $request)
    {
        $query = $this->user()->notes()->with('tags')->orderBy('updated_at', 'desc');

        if ($request->has('folder')) {
            $query->where('folder', $request->folder);
        }
        
        if ($request->has('tag')) {
            $query->whereHas('tags', function ($q) use ($request) {
                $q->where('name', $request->tag);
            });
        }

        if ($request->has('favorites')) {
            $query->where('is_favorite', true);
        }

        $notes = $query->get();
        $tags = $this->user()->tags()->orderBy('name')->get();
        $folders = $this->user()->notes()->whereNotNull('folder')->distinct()->pluck('folder');

        return view('notes.index', compact('notes', 'tags', 'folders'));
    }

    public function create()
    {
        $tags = $this->user()->tags()->orderBy('name')->get();
        return view('notes.create', compact('tags'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'folder' => 'nullable|string|max:255',
            'is_favorite' => 'boolean',
        ]);

        $note = $this->user()->notes()->create([
            'title' => $validated['title'],
            'content' => $validated['content'] ?? null,
            'folder' => $validated['folder'] ?? null,
            'is_favorite' => $request->has('is_favorite'),
        ]);

        if ($request->has('tags')) {
            $tagIds = [];
            foreach (explode(',', $request->tags) as $tagName) {
                $tagName = trim($tagName);
                if (!empty($tagName)) {
                    $tag = $this->user()->tags()->firstOrCreate(['name' => $tagName]);
                    $tagIds[] = $tag->id;
                }
            }
            $note->tags()->sync($tagIds);
        }

        return redirect()->route('notes.index')->with('success', 'Note created successfully.');
    }

    public function edit(Note $note)
    {
        if ($note->user_id !== Auth::id()) {
            abort(403);
        }
        $tags = $this->user()->tags()->orderBy('name')->get();
        return view('notes.edit', compact('note', 'tags'));
    }

    public function update(Request $request, Note $note)
    {
        if ($note->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'folder' => 'nullable|string|max:255',
        ]);

        $note->update([
            'title' => $validated['title'],
            'content' => $validated['content'] ?? null,
            'folder' => $validated['folder'] ?? null,
            'is_favorite' => $request->has('is_favorite'),
        ]);

        if ($request->has('tags')) {
            $tagIds = [];
            foreach (explode(',', $request->tags) as $tagName) {
                $tagName = trim($tagName);
                if (!empty($tagName)) {
                    $tag = $this->user()->tags()->firstOrCreate(['name' => $tagName]);
                    $tagIds[] = $tag->id;
                }
            }
            $note->tags()->sync($tagIds);
        } else {
            $note->tags()->detach();
        }

        return redirect()->route('notes.index')->with('success', 'Note updated successfully.');
    }

    public function destroy(Note $note)
    {
        if ($note->user_id !== Auth::id()) {
            abort(403);
        }

        $note->delete();

        return redirect()->route('notes.index')->with('success', 'Note deleted successfully.');
    }
}
