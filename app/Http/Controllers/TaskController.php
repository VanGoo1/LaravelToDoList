<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Couchbase\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    public function index()
    {
        return Task::query()->where('user_id', Auth::id())->get();
    }

    public function Update(Request $request, $id)
    {
        $task = Task::find($id);
        if (!$task)
            return response()->json(['message' => 'Task is not found', "success" => false], 404);
        $task->update($request->all());
        return response()->json([$task, "success" => true]);
    }

    public function Add(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'deadline' => 'required|date',
            'description' => 'nullable|string',
        ]);
        try {
            $task = new Task();
            $task->title = $request->title;
            $task->description = $request->description;
            $task->user_id = Auth::id();
            $task->deadline = $request->deadline;
            $task->save();
        } catch (QueryException) {
            return response()->json(["message" => 'failed'], 501);
        }

        return response()->json(['task' => $task], 201);
    }

    public function Delete($id)
    {
        $task = Task::find($id);
        if ($task != null)
            $task->delete();
        else
            return response()->json(["message" => "not found", "success" => false], 404);
        return response()->json(["message" => "task is successfully deleted", "success" => true], 201);
    }
}
