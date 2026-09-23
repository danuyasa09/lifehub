<?php

namespace App\Http\Controllers;

use App\Models\Attachment;
use Illuminate\Http\Request;

class AttachmentController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'file_name' => 'required|string',
            'file' => 'required|file',
            'attachable_type' => 'required|string',
            'attachable_id' => 'required|integer',
        ]);

        $file = $request->file('file');
        $path = $file->store('attachments', 'public');
        $size = $file->getSize();

        Attachment::create([
            'attachable_type' => $request->attachable_type,
            'attachable_id' => $request->attachable_id,
            'file_name' => $request->file_name,
            'file_path' => '/storage/' . $path,
            'file_size' => $size,
        ]);

        return redirect()->back()->with('success', 'File attached successfully.');
    }

    public function destroy(Attachment $attachment)
    {
        $attachment->delete();
        return redirect()->back()->with('success', 'File removed successfully.');
    }
}
