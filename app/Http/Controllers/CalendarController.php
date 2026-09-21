<?php

namespace App\Http\Controllers;

use App\Models\Record;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CalendarController extends Controller
{
    /**
     * Display a calendar view of records based on created_at.
     */
    public function index(Request $request)
    {
        // 取得請求的年月，預設為當前年月
        $year = intval($request->input('year', date('Y')));
        $month = intval($request->input('month', date('m')));

        if ($month < 1 || $month > 12) {
            $month = intval(date('m'));
        }

        $currentDate = Carbon::createFromDate($year, $month, 1);
        
        // 日曆第一天（補齊當月第一週的週日）
        $startOfCalendar = $currentDate->copy()->startOfMonth()->startOfWeek(Carbon::SUNDAY);
        // 日曆最後一天（補齊當月最後一週的週六）
        $endOfCalendar = $currentDate->copy()->endOfMonth()->endOfWeek(Carbon::SATURDAY);

        // 取得該區間內的所有紀錄，包含專案與模組
        $records = Record::with(['project', 'module'])
            ->whereBetween('created_at', [$startOfCalendar->startOfDay(), $endOfCalendar->endOfDay()])
            ->orderBy('created_at', 'asc')
            ->get();

        // 將紀錄依據日期（Y-m-d）進行分組
        $recordsByDate = $records->groupBy(function ($record) {
            return $record->created_at->format('Y-m-d');
        });

        // 產生給 View 的日期陣列
        $days = [];
        $date = $startOfCalendar->copy();
        
        while ($date <= $endOfCalendar) {
            $dateString = $date->format('Y-m-d');
            $days[] = [
                'date'           => $dateString,
                'day'            => $date->day,
                'isCurrentMonth' => $date->month === $month,
                'isToday'        => $date->isToday(),
                'records'        => $recordsByDate->get($dateString, collect()),
            ];
            $date->addDay();
        }

        // 上下個月的參數
        $prevMonth = $currentDate->copy()->subMonth();
        $nextMonth = $currentDate->copy()->addMonth();

        return view('calendar.index', compact(
            'year', 'month', 'currentDate', 'days', 'prevMonth', 'nextMonth'
        ));
    }
}
