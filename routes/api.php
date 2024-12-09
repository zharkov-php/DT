<?php

use App\Http\Controllers\AnalyticController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::get('/health-check', static function (){

    return true;
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::prefix('projects')->group(function () {
        Route::post('/', [ProjectController::class, 'store']);
        Route::get('{project}/analytics', [AnalyticController::class, 'index'])->middleware(['auth', 'check.project.role:Owner|Editor']);
        Route::post('{project}/invite', [ProjectController::class, 'invite'])->middleware(['auth', 'check.project.role:Owner']);
        Route::post('join', [ProjectController::class, 'joinTeam']);
        Route::delete('{project}', [ProjectController::class, 'destroy'])->middleware(['auth', 'check.project.role:Owner']);
        Route::post('{project}/restore', [ProjectController::class, 'restore'])->withTrashed()->middleware(['auth', 'check.project.role:Owner']);
        Route::get('{project}/tasks', [TaskController::class, 'index'])->middleware(['auth', 'check.project.role:Owner|Editor|Viewer']);
    });

    Route::prefix('tasks')->group(function () {
        Route::post('/', [TaskController::class, 'store']);//check policy role
        Route::put('{task}', [TaskController::class, 'update'])->middleware('check.task.role:Owner|Editor');
        Route::delete('{task}', [TaskController::class, 'destroy'])->middleware('check.task.role:Owner');
    });

    Route::prefix('comments')->group(function () {
        Route::post('/', [CommentController::class, 'store']);//check policy role
        Route::put('{comment}', [CommentController::class, 'update'])->middleware('check.comment.role:Owner|Editor');
        Route::delete('{comment}', [CommentController::class, 'destroy'])->middleware('check.comment.role:Owner');
    });
});

