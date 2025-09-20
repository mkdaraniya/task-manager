<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\BoardController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\StatusController;
use App\Http\Controllers\TeamsController;
use App\Http\Controllers\UserController;
use App\Models\Task;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

// Auth routes (from Breeze/Fortify)
require __DIR__ . '/auth.php';

// Email verification
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect('/dashboard');
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('message', 'Verification link sent!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

// Guest routes
Route::middleware('guest')->group(function () {
    Route::get('/', function () {
        return view('welcome');
    });
});

// Protected routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/live-data', [DashboardController::class, 'liveData'])->name('dashboard.live-data');

    // Profile
    Route::get('/profile', [UserController::class, 'show'])->name('users.profile');
    Route::put('/profile', [UserController::class, 'update'])->name('users.update');
    Route::post('/logout-all', [UserController::class, 'logoutAllDevices'])->name('users.logout-all');

    // Projects
    Route::resource('projects', ProjectController::class);
    Route::put('projects/{project}', [ProjectController::class, 'update'])->name('projects.update.ajax');
    Route::delete('projects/{project}', [ProjectController::class, 'destroy'])->name('projects.destroy.ajax');

    // Boards
    Route::get('projects/{project}/boards', [BoardController::class, 'index'])->name('projects.boards.index');
    Route::resource('projects.boards', BoardController::class)->parameters(['boards' => 'board'])->shallow();

    // Tasks
    // nested resource for boards -> tasks (create, store, index, show, update, destroy)
    Route::resource('boards.tasks', TaskController::class);
    Route::get('boards/{board}/kanban', [BoardController::class, 'kanban'])->name('boards.kanban');
    Route::post('/tasks', [TaskController::class, 'storeGlobal'])->name('tasks.store.global');

    Route::get('tasks', [TaskController::class, 'index'])->name('tasks.index'); // all tasks view

    // API endpoints used by Kanban / AJAX UI
    Route::get('api/boards/{board}/tasks', [TaskController::class, 'index'])->name('api.boards.tasks.index'); // index returns JSON when AJAX
    Route::get('api/tasks/{task}', [TaskController::class, 'apiShow'])->name('api.tasks.show'); // add method below

    // Additional endpoints referenced by JS (ensure these are present — many already are in your controller)
    Route::post('tasks/{task}/subtasks', [TaskController::class, 'addSubtask'])->name('tasks.subtasks.store');
    Route::patch('subtasks/{subtask}', [TaskController::class, 'toggleSubtask'])->name('subtasks.toggle');
    Route::post('tasks/{task}/comments', [TaskController::class, 'addComment'])->name('tasks.comments.store');
    Route::post('attachments/{attachable}/{type?}', [TaskController::class, 'uploadAttachment'])->name('attachments.store');
    Route::post('tasks/{task}/time-log', [TaskController::class, 'logTime'])->name('tasks.time-log');
    Route::post('tasks/{task}/time-log', [\App\Http\Controllers\TaskController::class, 'logTime'])->name('tasks.time-log');

    // Teams
    Route::resource('teams', \App\Http\Controllers\TeamsController::class);
    Route::post('teams/{team}/members', [TeamsController::class, 'addMember'])->name('teams.addMember');
    Route::put('teams/{team}/members/{userId}', [TeamsController::class, 'updateMember'])->name('teams.updateMember');
    Route::delete('teams/{team}/members/{userId}', [TeamsController::class, 'removeMember'])->name('teams.removeMember');

    // Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::post('/reports/generate', [ReportController::class, 'generate'])->name('reports.generate');

    // Calendar
    Route::get('/calendar', [CalendarController::class, 'index'])->name('calendar.index');
    Route::get('/calendar/events', [CalendarController::class, 'events'])->name('calendar.events');
    Route::get('/calendar/task/{id}', [CalendarController::class, 'showTask'])->name('calendar.task.show');
    Route::get('/boards/{board}/tasks/{task}', [TaskController::class, 'show'])->name('boards.tasks.show'); // Assuming TaskController exists


    // Admin
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/', [AdminController::class, 'index'])->name('index');
        Route::post('users/{user}/role', [AdminController::class, 'assignRole'])->name('assign-role');
        Route::get('/logs', [AdminController::class, 'logs'])->name('logs');
    });

    Route::resource('boards.statuses', StatusController::class);
});

// API routes for AJAX
Route::middleware('auth')->prefix('api')->group(function () {
    Route::get('/tasks/search', [TaskController::class, 'index']);  // With filters
    Route::get('/global-search', function (Request $request) {
        // Implement global search across tasks, comments, chat, tags, users
        $query = $request->q;
        $tasks = Task::where('title', 'like', "%$query%")->get();
        // Add more models
        return response()->json(['tasks' => $tasks]);
    })->name('global.search');
});
