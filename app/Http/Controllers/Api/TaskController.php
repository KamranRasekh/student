<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    // 🟢 دیدن task ها
    public function index(Request $request)
    {
        $user = $request->user();

        // اگر admin بود → همه task ها
        if ($user->role === 'admin') {
            $tasks = Task::all();
        } else {
            // user → فقط task خودش
            $tasks = Task::where('user_id', $user->id)->get();
        }

        return response()->json($tasks);
    }

    // 🟢 ساخت task
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'description' => 'nullable'
        ]);

        $task = Task::create([
            'title' => $request->title,
            'description' => $request->description,
            'status' => 'pending',
            'user_id' => $request->user()->id
        ]);

        return response()->json([
            'message' => 'Task created',
            'task' => $task
        ]);
    }

    // 🟢 اپدیت
    public function update(Request $request, $id)
    {
        $task = Task::findOrFail($id);

        // فقط صاحب task یا admin
        if ($request->user()->id !== $task->user_id &&
            $request->user()->role !== 'admin') {

            return response()->json([
                'message' => 'Access denied'
            ], 403);
        }

        $task->update($request->all());

        return response()->json([
            'message' => 'Task updated',
            'task' => $task
        ]);
    }

    // 🔴 حذف
    public function destroy(Request $request, $id)
    {
        $task = Task::findOrFail($id);

        if ($request->user()->id !== $task->user_id &&
            $request->user()->role !== 'admin') {

            return response()->json([
                'message' => 'Access denied'
            ], 403);
        }

        $task->delete();

        return response()->json([
            'message' => 'Task deleted'
        ]);
    }
}