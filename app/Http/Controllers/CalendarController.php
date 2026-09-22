<?php

namespace App\Http\Controllers;

use App\Models\Record;
use App\Models\Holiday;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CalendarController extends Controller
{
    /**
     * Display a calendar view of records based on created_at.
     */
    public function index(Request $request)
    {
        $year = intval($request->input('year', date('Y')));
        $month = intval($request->input('month', date('m')));

        if ($month < 1 || $month > 12) {
            $month = intval(date('m'));
        }

        $currentDate = Carbon::createFromDate($year, $month, 1);
        
        $startOfCalendar = $currentDate->copy()->startOfMonth()->startOfWeek(Carbon::SUNDAY);
        $endOfCalendar = $currentDate->copy()->endOfMonth()->endOfWeek(Carbon::SATURDAY);

        $records = Record::with(['project', 'module'])
            ->whereBetween('created_at', [$startOfCalendar->startOfDay(), $endOfCalendar->endOfDay()])
            ->orderBy('created_at', 'asc')
            ->get();

        $recordsByDate = $records->groupBy(function ($record) {
            return $record->created_at->format('Y-m-d');
        });

        // Fetch holidays
        $holidays = Holiday::whereBetween('date', [$startOfCalendar->format('Y-m-d'), $endOfCalendar->format('Y-m-d')])
            ->get()->keyBy(function($h) {
                return $h->date->format('Y-m-d');
            });

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
                'holiday'        => $holidays->get($dateString),
            ];
            $date->addDay();
        }

        $prevMonth = $currentDate->copy()->subMonth();
        $nextMonth = $currentDate->copy()->addMonth();

        return view('calendar.index', compact(
            'year', 'month', 'currentDate', 'days', 'prevMonth', 'nextMonth'
        ));
    }

    /**
     * Sync Taiwan holidays from open data (GitHub TaiwanCalendar).
     */
    public function syncHolidays(Request $request)
    {
        $this->authorizeRole('editor');

        $year = date('Y');
        $yearsToSync = [$year - 1, $year, $year + 1]; // Sync last, current, and next year

        $syncedCount = 0;

        foreach ($yearsToSync as $y) {
            try {
                $response = Http::timeout(10)->withHeaders(['User-Agent' => 'Mozilla/5.0'])
                    ->get("https://cdn.jsdelivr.net/gh/ruyut/TaiwanCalendar/data/{$y}.json");
                
                if ($response->successful()) {
                    $data = $response->json();
                    
                    foreach ($data as $day) {
                        if ($day['isHoliday'] && !empty($day['description'])) {
                            Holiday::updateOrCreate(
                                ['date' => Carbon::parse($day['date'])->format('Y-m-d')],
                                [
                                    'name' => $day['description'],
                                    'is_holiday' => true,
                                ]
                            );
                            $syncedCount++;
                        }
                    }
                }
            } catch (\Exception $e) {
                continue;
            }
        }

        return redirect()->back()->with('success', "國定假日同步完成！共更新 {$syncedCount} 筆資料。");
    }
}
