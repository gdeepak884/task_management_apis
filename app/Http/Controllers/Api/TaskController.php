<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\User;
use App\Models\UserTask;

use function PHPSTORM_META\map;

class TaskController extends Controller
{

    // get all tasks
    public function listTask(Request $request)
    {
        $tasks = Task::join('user_tasks', 'tasks.id', '=', 'user_tasks.task_id')
            ->join('users', 'user_tasks.user_id', '=', 'users.id')
            ->get();

        $tasks = $tasks->map(function ($task) {
            $task->status = $task->status == 0 ? 'pending' : ($task->status == 1 ? 'in process' : 'completed');
            return $task;
        });

        $request->validate([
            'status' => 'string|in:pending,in process,completed',
            'name' => 'string|max:255',
            'date' => 'date'
        ]);

        if ($request->status) {
            $tasks = $tasks->filter(function ($task) use ($request) {
                return $task->status == $request->status;
            });
        }

        if ($request->name) {
            $tasks = $tasks->filter(function ($task) use ($request) {
                return $task->name == $request->name;
            });
        }

        if ($request->date) {
            $tasks = $tasks->filter(function ($task) use ($request) {
                return $task->due_date == $request->date;
            });
        }

        return response()->json([
            'status' => 'success',
            'tasks' => $tasks
        ]);
    }

    // get a single task
    public function task($id)
    {
        $task = Task::join('user_tasks', 'tasks.id', '=', 'user_tasks.task_id')
            ->join('users', 'user_tasks.user_id', '=', 'users.id')
            ->where('tasks.id', $id)
            ->get();

        $task = $task->map(function ($task) {
            $task->status = $task->status == 0 ? 'pending' : ($task->status == 1 ? 'in process' : 'completed');
            return $task;
        });

        if (!$task) {
            return response()->json([
                'status' => 'error',
                'message' => 'Task not found'
            ]);
        }
        return response()->json([
            'status' => 'success',
            'data' => $task
        ]);
    }

    // create a new task
    public function addTask(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'string|max:255'
        ]);

        $task = Task::where('title', $request->title)->first();
        if ($task) {
            return response()->json([
                'status' => 'error',
                'message' => 'Task title already exists'
            ]);
        }

        $task = Task::create([
            'title' => $request->title,
            'description' => $request->description,
            'due_date' => $request->due_date,
            'status' => $request->status ? $request->status : 0
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Task created successfully'
        ]);
    }

    // update a task
    public function updateTask(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'string|max:255',
            'due_date' => 'date'
        ]);

        $task = Task::where('title', $request->title)->where('id', '!=', $id)->first();
        if ($task) {
            return response()->json([
                'status' => 'error',
                'message' => 'Task title already exists'
            ]);
        }

        $task = Task::find($id);
        if (!$task) {
            return response()->json([
                'status' => 'error',
                'message' => 'Task not found'
            ]);
        }
        $task->title = $request->title;
        if ($request->description) {
            $task->description = $request->description;
        }
        if ($request->due_date) {
            $task->due_date = $request->due_date;
        }
        if ($request->status) {
            $task->status = $request->status;
        }
        $task->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Task updated successfully',
            'data' => $task
        ]);
    }

    // delete a task
    public function deleteTask($id)
    {
        $task = Task::find($id);
        if (!$task) {
            return response()->json([
                'status' => 'error',
                'message' => 'Task not found'
            ]);
        }
        $task->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'Task deleted successfully'
        ]);
    }

    // assign a task to a user
    public function assignTask(Request $request)
    {
        $request->validate([
            'task_id' => 'required|integer',
            'user_id' => 'required|integer'
        ]);

        $task = Task::find($request->task_id);
        if (!$task) {
            return response()->json([
                'status' => 'error',
                'message' => 'Task not found'
            ]);
        }

        $user = User::find($request->user_id);
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found'
            ]);
        }

        $userTask = UserTask::where('task_id', $task->id)
            ->where('user_id', $user->id)
            ->first();

        if ($userTask) {
            return response()->json([
                'status' => 'error',
                'message' => 'Task already assigned to user'
            ]);
        }
        $userTask = UserTask::create([
            'task_id' => $task->id,
            'user_id' => $user->id
        ]);

        return response()->json([
            'status' => 'success',
            'data' => "Task assigned to user successfully",
            "task" => $task,
            "user" => $user
        ]);
    }

    // unassign a task from a user
    public function unassignTask(Request $request)
    {
        $request->validate([
            'task_id' => 'required|integer',
            'user_id' => 'required|integer'
        ]);

        $task = Task::find($request->task_id);
        if (!$task) {
            return response()->json([
                'status' => 'error',
                'message' => 'Task not found'
            ]);
        }

        $user = User::find($request->user_id);
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found'
            ]);
        }

        $userTask = UserTask::where('task_id', $task->id)
            ->where('user_id', $user->id)
            ->first();

        if (!$userTask) {
            return response()->json([
                'status' => 'error',
                'message' => 'Task not assigned to user'
            ]);
        }

        $userTask->delete();
        return response()->json([
            'status' => 'success',
            'data' => "Task unassigned from user successfully",
            "task" => $task,
            "user" => $user
        ]);
    }

    // get all tasks assigned to a user
    public function userTask(Request $request, $id)
    {
        $tasks = Task::join('user_tasks', 'tasks.id', '=', 'user_tasks.task_id')
            ->where('user_tasks.user_id', $id)
            ->get();

        $tasks = $tasks->map(function ($task) {
            $task->status = $task->status == 0 ? 'pending' : ($task->status == 1 ? 'in process' : 'completed');
            return $task;
        });

        $user = User::find($id);

        return response()->json([
            'status' => 'success',
            'user' => $user,
            'tasks' => $tasks
        ]);
    }
}
