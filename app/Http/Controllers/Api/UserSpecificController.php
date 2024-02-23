<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Task;
use App\Models\User;
use App\Models\UserTask;

class UserSpecificController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api')->except('updateStatus', 'userTasks');
    }

    public function updateStatus(Request $request)
    {
        if (!Auth::check()) {
            return response()->json([
                'status' => 'error',
                'error' => 'Unauthorized'
            ], 401);
        }
        $request->validate([
            'status' => 'integer|in:0,1,2',
            'task_id' => 'required|integer'
        ]);

        $task = Task::find($request->task_id);
        if (!$task) {
            return response()->json([
                'status' => 'error',
                'message' => 'Task not found'
            ]);
        }

        $userTask = UserTask::where('task_id', $request->task_id)
            ->where('user_id', auth()->user()->id)
            ->first();

        if (!$userTask) {
            return response()->json([
                'status' => 'error',
                'message' => 'Task not assigned to user'
            ]);
        }

        $task->status = $request->status;
        $task->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Task status updated'
        ]);
    }

    public function userTasks(Request $request)
    {
        if (!Auth::check()) {
            return response()->json([
                'status' => 'error',
                'error' => 'Unauthorized'
            ], 401);
        }

        $tasks = Task::join('user_tasks', 'tasks.id', '=', 'user_tasks.task_id')
            ->where('user_tasks.user_id', auth()->user()->id)
            ->get();

        $user = User::find(auth()->user()->id);
        $tasks->map(function ($task) use ($user) {
            $task->user = $user;
            return $task;
        });
        $tasks = $tasks->map(function ($task) {
            $task->status = $task->status == 0 ? 'pending' : ($task->status == 1 ? 'in process' : 'completed');
            return $task;
        });
        return response()->json([
            'status' => 'success',
            'task' => $tasks
        ]);
    }
}
