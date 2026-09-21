<?php

namespace App\Http\Controllers;

use App\Models\Record;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DailyLogController extends Controller
{
    public function index(Request $request)
    {
        // 取得選擇的日期，預設為今天
        $dateInput = $request->input('date', Carbon::today()->format('Y-m-d'));
        
        try {
            $targetDate = Carbon::parse($dateInput);
        } catch (\Exception $e) {
            $targetDate = Carbon::today();
        }

        // 撈取該日期的所有紀錄，並載入關聯
        $records = Record::with(['project', 'module', 'tags'])
            ->whereDate('created_at', $targetDate)
            ->orderBy('created_at', 'asc')
            ->get();

        // 依據專案名稱分組
        $groupedRecords = $records->groupBy(function ($record) {
            return $record->project->name;
        });

        // 統計數據
        $totalRecords = $records->count();

        return view('daily-log.index', compact('targetDate', 'groupedRecords', 'totalRecords'));
    }
}
