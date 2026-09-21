<x-records-layout title="行事曆">
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <h2 class="text-xl font-semibold">📅 行事曆</h2>
                <a href="{{ route('records.index') }}" class="text-sm bg-white border border-gray-300 text-gray-700 px-3 py-1.5 rounded hover:bg-gray-50 flex items-center">
                    <span class="mr-1">📄</span> 切換列表模式
                </a>
            </div>
            <div class="flex items-center space-x-4">
                <a href="{{ route('calendar.index', ['year' => $prevMonth->year, 'month' => $prevMonth->month]) }}" class="text-gray-500 hover:text-indigo-600">
                    &laquo; 上個月
                </a>
                <span class="text-lg font-bold text-gray-800">{{ $year }} 年 {{ $month }} 月</span>
                <a href="{{ route('calendar.index', ['year' => $nextMonth->year, 'month' => $nextMonth->month]) }}" class="text-gray-500 hover:text-indigo-600">
                    下個月 &raquo;
                </a>
            </div>
            <a href="{{ route('calendar.index') }}" class="text-sm text-indigo-600 hover:underline">回到本月</a>
        </div>
    </x-slot>

    <div class="bg-white rounded shadow-sm overflow-hidden">
        {{-- 星期標題 --}}
        <div class="grid grid-cols-7 border-b border-gray-200 bg-gray-50 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider py-2">
            <div class="text-red-500">週日</div>
            <div>週一</div>
            <div>週二</div>
            <div>週三</div>
            <div>週四</div>
            <div>週五</div>
            <div class="text-red-500">週六</div>
        </div>

        {{-- 日曆格 --}}
        <div class="grid grid-cols-7 border-l border-gray-200 auto-rows-fr" style="grid-auto-rows: minmax(120px, auto);">
            @foreach($days as $day)
                <div class="border-b border-r border-gray-200 p-2 flex flex-col {{ $day['isCurrentMonth'] ? 'bg-white' : 'bg-gray-50' }} {{ $day['isToday'] ? 'bg-indigo-50 border-indigo-200' : '' }}">
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-xs font-medium {{ $day['isCurrentMonth'] ? 'text-gray-700' : 'text-gray-400' }} {{ $day['isToday'] ? 'text-indigo-700 font-bold' : '' }}">
                            {{ $day['day'] }}
                        </span>
                        @if($day['records']->count() > 0)
                            <span class="text-[10px] bg-gray-200 text-gray-600 px-1.5 py-0.5 rounded-full">{{ $day['records']->count() }}</span>
                        @endif
                    </div>
                    
                    <div class="flex-1 space-y-1 overflow-y-auto" style="max-height: 180px;">
                        @php
                            $typeColors = [
                                'development' => 'bg-blue-100 text-blue-700 border-blue-200',
                                'test'        => 'bg-green-100 text-green-700 border-green-200',
                                'issue'       => 'bg-red-100 text-red-700 border-red-200',
                                'note'        => 'bg-gray-100 text-gray-600 border-gray-200',
                            ];
                        @endphp
                        @foreach($day['records'] as $record)
                            <a href="{{ route('records.show', $record) }}" class="block text-left text-xs p-1 border rounded truncate hover:shadow-md transition-shadow {{ $typeColors[$record->type] ?? 'bg-gray-100' }}" title="{{ $record->title }}">
                                <span class="font-bold opacity-75 mr-1">[{{ Str::limit($record->project->name, 6, '') }}]</span>
                                {{ $record->title }}
                            </a>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</x-records-layout>
