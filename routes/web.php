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

// 未登入時 middleware 會導到 /login；登入後 HOME='/records'
Route::get('/', function () {
    return redirect()->route('records.index');
});

Route::middleware(['auth'])->group(function () {

    // ---------- Profile (Breeze) ----------
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ---------- Calendar ----------
    Route::get('/calendar', [\App\Http\Controllers\CalendarController::class, 'index'])->name('calendar.index');

    // ---------- Projects (Task 02) ----------
    Route::resource('projects', ProjectController::class)->except(['destroy']);
    
    // AJAX 端點：取得專案的模組列表 (前端動態選單使用)
    Route::get('ajax/projects/{project}/modules', [ProjectController::class, 'getModulesJson'])
        ->name('ajax.projects.modules');

    // ---------- Modules nested under projects (Task 03) ----------
    Route::get('projects/{project}/modules', [ModuleController::class, 'index'])
        ->name('projects.modules.index');
    Route::get('projects/{project}/modules/create', [ModuleController::class, 'create'])
        ->name('projects.modules.create');
    Route::post('projects/{project}/modules', [ModuleController::class, 'store'])
        ->name('projects.modules.store');
    Route::get('modules/{module}/edit', [ModuleController::class, 'edit'])
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
    Route::get('record-files/{recordFile}/download', [RecordFileController::class, 'download'])
        ->name('record-files.download');
    Route::delete('record-files/{recordFile}', [RecordFileController::class, 'destroy'])
        ->name('record-files.destroy');
});

require __DIR__.'/auth.php';
