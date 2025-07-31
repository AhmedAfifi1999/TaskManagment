<?php

use App\Http\Controllers\Admin\DashboardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProjectsController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\TasksController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\Admin\ProjectTaskController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Website\MainController;

// Route::get('/', [HomeController::class, 'index'])->name('home.index');


Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/login', [AuthController::class, 'loginPage'])->name('login.form');
Route::get('/', [MainController::class, 'index'])->name('main');

Route::prefix('admin')->name('admin.')->middleware('auth')->group(function () {
    // Route::prefix('admin')->name('admin.')->group(function () {
    // 
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/management', [HomeController::class, 'index'])->name('management');

    // users
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('create', [UserController::class, 'create'])->name('create');
        Route::post('/', [UserController::class, 'store'])->name('store');
        Route::get('{id}/edit', [UserController::class, 'edit'])->name('edit');
        Route::put('{id}', [UserController::class, 'update'])->name('update');
        Route::delete('{id}', [UserController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('projects')->name('projects.')->group(function () {
        Route::get('/', [ProjectController::class, 'index'])->name('index');
        Route::get('create', [ProjectController::class, 'create'])->name('create');
        Route::post('/', [ProjectController::class, 'store'])->name('store');
        Route::get('{id}/edit', [ProjectController::class, 'edit'])->name('edit');
        Route::put('{id}', [ProjectController::class, 'update'])->name('update');
        Route::delete('{id}', [ProjectController::class, 'destroy'])->name('destroy');

        Route::prefix('{project}/tasks')->name('tasks.')->group(function () {
            Route::get('/', [ProjectTaskController::class, 'index'])->name('index'); // عرض جميع المهام
            Route::get('create', [ProjectTaskController::class, 'create'])->name('create'); // نموذج إضافة مهمة
            Route::post('/', [ProjectTaskController::class, 'store'])->name('store'); // حفظ المهمة
            Route::get('{task}/edit', [ProjectTaskController::class, 'edit'])->name('edit'); // تعديل المهمة
            Route::put('{task}', [ProjectTaskController::class, 'update'])->name('update'); // تحديث المهمة
            Route::delete('{task}', [ProjectTaskController::class, 'destroy'])->name('destroy'); // حذف المهمة
            Route::post('reorder', [ProjectTaskController::class, 'reorder'])->name('reorder');
            Route::post('{task}/status', [ProjectTaskController::class, 'status'])->name('status');
        });
    });

    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/edit', [SettingsController::class, 'edit'])->name('edit');
        Route::post('/update', [SettingsController::class, 'update'])->name('update');
        Route::get('/account', [SettingsController::class, 'account'])->name('account');
        Route::post('/account', [SettingsController::class, 'accountUpdate'])->name('accountUpdate');
//---
        Route::get('/security', [SettingsController::class, 'security'])->name('security');
        Route::post('/changePassword', [SettingsController::class, 'changePassword'])->name('changePassword');

    });
});

// PROJECTS ROUTES
Route::prefix('projects')->group(function () {
    Route::post('insert', [ProjectsController::class, 'insert'])->name('projects.insert');
    Route::post('update/{id}', [ProjectsController::class, 'update'])->name('projects.update');
    Route::get('delete/{id}', [ProjectsController::class, 'delete'])->name('projects.delete');
});

// TASKS ROUTES
Route::prefix('tasks')->group(function () {
    Route::post('insert', [TasksController::class, 'insert'])->name('tasks.insert');
    Route::post('update/{id}', [TasksController::class, 'update'])->name('tasks.update');
    Route::get('delete/{id}', [TasksController::class, 'delete'])->name('tasks.delete');
    Route::post('toggle-completion/{id?}', [TasksController::class, 'toggleCompletion'])->name('tasks.toggleCompletion');
    Route::get('filter', [TasksController::class, 'filter'])->name('tasks.filter');
    Route::post('update-priority', [TasksController::class, 'updatePriority'])->name('tasks.updatePriority');
});
