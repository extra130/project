<?php

use App\Http\Controllers\Api\AgentRecordController;
use App\Http\Controllers\Api\RecordApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes  (Codex / Agent API — spec §21–28)
| Auth: Laravel Sanctum Bearer Token
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {

    // ---------- Current user ----------
    Route::get('/user', fn(Request $request) => $request->user());

    // ---------- Projects (spec §21) ----------
    Route::get('/projects', [AgentRecordController::class, 'projectIndex']);
    Route::post('/projects', [AgentRecordController::class, 'projectStore']);
    Route::get('/projects/{project}', [AgentRecordController::class, 'projectShow']);
    Route::put('/projects/{project}', [AgentRecordController::class, 'projectUpdate']);

    // ---------- Modules (spec §22) ----------
    Route::get('/projects/{project}/modules', [AgentRecordController::class, 'moduleIndex']);
    Route::post('/projects/{project}/modules', [AgentRecordController::class, 'moduleStore']);
    Route::get('/modules/{module}', [AgentRecordController::class, 'moduleShow']);
    Route::put('/modules/{module}', [AgentRecordController::class, 'moduleUpdate']);

    // ---------- Records (spec §23 / Task 10, 11) ----------
    Route::get('/records', [RecordApiController::class, 'index']);
    Route::post('/records', [RecordApiController::class, 'store']);
    Route::get('/records/{record}', [RecordApiController::class, 'show']);
    Route::put('/records/{record}', [RecordApiController::class, 'update']);

    // ---------- Attachments (spec §24 / Task 12) ----------
    Route::post('/records/{record}/files', [RecordApiController::class, 'fileStore']);
    Route::put('/record-files/{recordFile}', [RecordApiController::class, 'fileUpdate']);
    Route::get('/record-files/{recordFile}/download', [RecordApiController::class, 'fileDownload']);
    Route::delete('/record-files/{recordFile}', [RecordApiController::class, 'fileDestroy']);
});
