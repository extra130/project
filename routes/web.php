<?php

use App\Http\Controllers\ModuleController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\RecordController;
use App\Http\Controllers\RecordFileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// 未登入時 middleware 會導到 /login；登入後跳轉到儀表板
Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware(['auth'])->group(function () {
    // ---------- Dashboard ----------
    Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');

    // ---------- Profile (Breeze) ----------
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ---------- Calendar & Daily Log ----------
    Route::get('/calendar', [\App\Http\Controllers\CalendarController::class, 'index'])->name('calendar.index');
    Route::get('/daily-log', [\App\Http\Controllers\DailyLogController::class, 'index'])->name('daily-log.index');

    // ---------- Projects (Task 02) ----------
    Route::post('projects/reorder', [ProjectController::class, 'reorder'])->name('projects.reorder');
    Route::resource('projects', ProjectController::class)->except(['destroy']);
    Route::post('projects/{project}/archive', [ProjectController::class, 'archive'])->name('projects.archive');

    // ---------- Project Files ----------
    Route::post('/projects/{project}/files', [\App\Http\Controllers\ProjectFileController::class, 'store'])->name('project-files.store');
    Route::get('project-files/{projectFile}/preview', [\App\Http\Controllers\ProjectFileController::class, 'preview'])
        ->name('project-files.preview');
    Route::get('project-files/{projectFile}/download', [\App\Http\Controllers\ProjectFileController::class, 'download'])
        ->name('project-files.download');
    Route::delete('/project-files/{projectFile}', [\App\Http\Controllers\ProjectFileController::class, 'destroy'])->name('project-files.destroy');

    // ---------- Module Files ----------
    Route::post('/modules/{module}/files', [\App\Http\Controllers\ModuleFileController::class, 'store'])->name('module-files.store');
    Route::get('module-files/{moduleFile}/preview', [\App\Http\Controllers\ModuleFileController::class, 'preview'])
        ->name('module-files.preview');
    Route::get('module-files/{moduleFile}/download', [\App\Http\Controllers\ModuleFileController::class, 'download'])
        ->name('module-files.download');
    Route::delete('/module-files/{moduleFile}', [\App\Http\Controllers\ModuleFileController::class, 'destroy'])->name('module-files.destroy');

    // AJAX 端點：取得專案的模組列表 (前端動態選單使用)
    Route::get('ajax/projects/{project}/modules', [ProjectController::class, 'getModulesJson'])
        ->name('ajax.projects.modules');

    // ---------- Modules (Task 03) ----------
    Route::post('modules/reorder', [\App\Http\Controllers\ModuleController::class, 'reorder'])->name('modules.reorder');
    Route::get('projects/{project}/modules', [\App\Http\Controllers\ModuleController::class, 'index'])
        ->name('projects.modules.index');
    Route::get('projects/{project}/modules/create', [\App\Http\Controllers\ModuleController::class, 'create'])
        ->name('projects.modules.create');
    Route::post('projects/{project}/modules', [\App\Http\Controllers\ModuleController::class, 'store'])
        ->name('projects.modules.store');
    Route::get('modules/{module}', [\App\Http\Controllers\ModuleController::class, 'show'])
        ->name('modules.show');
    Route::get('modules/{module}/edit', [\App\Http\Controllers\ModuleController::class, 'edit'])
        ->name('modules.edit');
    Route::put('modules/{module}', [ModuleController::class, 'update'])
        ->name('modules.update');

    // ---------- Records (Task 04 + 05) ----------
    Route::resource('records', RecordController::class)->except(['destroy']);

    // ---------- Record Files (Task 06 + 07 + 08) ----------
    Route::post('records/{record}/files', [RecordFileController::class, 'store'])
        ->name('record-files.store');
    Route::get('record-files/{recordFile}/edit', [RecordFileController::class, 'edit'])
        ->name('record-files.edit');
    Route::put('record-files/{recordFile}', [RecordFileController::class, 'update'])
        ->name('record-files.update');
    Route::get('record-files/{recordFile}/preview', [RecordFileController::class, 'preview'])
        ->name('record-files.preview');
    Route::get('record-files/{recordFile}/download', [RecordFileController::class, 'download'])
        ->name('record-files.download');
    Route::delete('record-files/{recordFile}', [RecordFileController::class, 'destroy'])
        ->name('record-files.destroy');
});

require __DIR__.'/auth.php';
