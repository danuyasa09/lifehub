<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;

class ProjectApiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // For now, we'll return all projects to test the API connection.
        // Once Auth is ready, this can be filtered by: auth()->user()->projects
        $projects = Project::orderBy('created_at', 'desc')->get();
        return response()->json([
            'status' => 'success',
            'data' => $projects
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $project = Project::with('tasks', 'attachments')->find($id);
        if (!$project) {
            return response()->json([
                'status' => 'error',
                'message' => 'Project not found'
            ], 404);
        }
        return response()->json([
            'status' => 'success',
            'data' => $project
        ]);
    }
}
