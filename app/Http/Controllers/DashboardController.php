<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Record;
use App\Models\RecordFile;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. 基本統計
        $stats = [
            'projects' => Project::count(),
            'records'  => Record::count(),
            'files'    => RecordFile::count() + \App\Models\ProjectFile::count() + \App\Models\ModuleFile::count(),
        ];

        // 2. 紀錄類型圓餅圖數據
        $typeDistribution = Record::select('type')
            ->get()
            ->groupBy('type')
            ->map->count();

        // 補齊可能沒有的類型
        $types = ['development' => 0, 'test' => 0, 'issue' => 0, 'note' => 0];
        foreach ($typeDistribution as $type => $count) {
            $types[$type] = $count;
        }

        // 3. 近 30 天活動趨勢 (為了相容 SQLite 測試與 MySQL，在 PHP 端 Group)
        $thirtyDaysAgo = Carbon::now()->subDays(29)->startOfDay();
        $recentRecords = Record::where('created_at', '>=', $thirtyDaysAgo)
            ->get(['created_at']);

        $dailyGroups = $recentRecords->groupBy(function ($record) {
            return $record->created_at->format('m/d');
        })->map->count();

        $trendLabels = [];
        $trendData = [];
        
        for ($i = 29; $i >= 0; $i--) {
            $dateLabel = Carbon::now()->subDays($i)->format('m/d');
            $trendLabels[] = $dateLabel;
            $trendData[] = $dailyGroups->get($dateLabel, 0);
        }

        // 4. 最近更新的 5 筆紀錄
        $latestRecords = Record::with('project')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('dashboard', compact('stats', 'types', 'trendLabels', 'trendData', 'latestRecords'));
    }
}
