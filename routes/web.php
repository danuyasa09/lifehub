<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/preferences', [ProfileController::class, 'updatePreferences'])->name('profile.preferences.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Tasks
    Route::get('/tasks', [App\Http\Controllers\TaskController::class, 'index'])->name('tasks.index');
    Route::post('/tasks', [App\Http\Controllers\TaskController::class, 'store'])->name('tasks.store');
    Route::put('/tasks/{task}', [App\Http\Controllers\TaskController::class, 'update'])->name('tasks.update');
    Route::delete('/tasks/{task}', [App\Http\Controllers\TaskController::class, 'destroy'])->name('tasks.destroy');

    // Calendar
    Route::get('/calendar', [App\Http\Controllers\CalendarController::class, 'index'])->name('calendar.index');

    // Notes
    Route::resource('notes', App\Http\Controllers\NoteController::class);

    // Habits
    Route::resource('habits', App\Http\Controllers\HabitController::class);
    Route::post('/habits/{habit}/toggle', [App\Http\Controllers\HabitController::class, 'toggle'])->name('habits.toggle');

    // Journals
    Route::get('/journals/history', [App\Http\Controllers\JournalController::class, 'history'])->name('journals.history');
    Route::resource('journals', App\Http\Controllers\JournalController::class);

    // Projects
    Route::resource('projects', App\Http\Controllers\ProjectController::class);
    
    // Project Tasks
    Route::post('/projects/{project}/tasks', [App\Http\Controllers\ProjectTaskController::class, 'store'])->name('project-tasks.store');
    Route::patch('/project-tasks/{task}/status', [App\Http\Controllers\ProjectTaskController::class, 'updateStatus'])->name('project-tasks.update-status');
    Route::delete('/project-tasks/{task}', [App\Http\Controllers\ProjectTaskController::class, 'destroy'])->name('project-tasks.destroy');

    // Attachments
    Route::post('/attachments', [App\Http\Controllers\AttachmentController::class, 'store'])->name('attachments.store');
    Route::delete('/attachments/{attachment}', [App\Http\Controllers\AttachmentController::class, 'destroy'])->name('attachments.destroy');

    // Finances
    Route::resource('finances', App\Http\Controllers\FinanceController::class)->only(['index', 'store']);
    
    // Budgets
    Route::post('/budgets', [App\Http\Controllers\BudgetController::class, 'store'])->name('budgets.store');

    // Analytics
    Route::get('/analytics', [App\Http\Controllers\AnalyticsController::class, 'index'])->name('analytics.index');

    // Search
    Route::get('/search', [App\Http\Controllers\SearchController::class, 'search'])->name('search');
    
    // Notifications
    Route::patch('/notifications/{id}/read', [App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.read');

    // AI Chat
    Route::get('/ai', [App\Http\Controllers\AiChatController::class, 'index'])->name('ai.index');
    Route::get('/ai/{ai_chat}', [App\Http\Controllers\AiChatController::class, 'show'])->name('ai.show');
    Route::post('/ai', [App\Http\Controllers\AiChatController::class, 'store'])->name('ai.store');
});

// PWA Offline Route
Route::view('/offline', 'pwa.offline')->name('offline');

require __DIR__.'/auth.php';
