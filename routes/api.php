<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\Api\UserSpecificController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::controller(AuthController::class)->group(function () {
    Route::post('login', 'login');
    Route::get('me', 'me');
    Route::post('refresh', 'refresh');
    Route::post('logout', 'logout');
});


Route::controller(TaskController::class)->group(function () {
    Route::get('tasks', 'listTask');
    Route::get('task/{id}', 'task');
    Route::post('add_task', 'addTask');
    Route::post('update_task/{id}', 'updateTask');
    Route::delete('delete_task/{id}', 'deleteTask');
    Route::post('assign_task', 'assignTask');
    Route::post('unassign_task', 'unassignTask');
    Route::get('user_task/{id}', 'userTask');
});

Route::controller(UserSpecificController::class)->group(function () {
    Route::post('update_status', 'updateStatus');
    Route::get('user_tasks', 'userTasks');
});
