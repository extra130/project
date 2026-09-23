<x-records-layout title="行事曆">
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <h2 class="text-xl font-semibold">📅 行事曆</h2>
                <a href="{{ route('records.index') }}" class="text-sm bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 px-3 py-1.5 rounded hover:bg-gray-50 dark:bg-gray-900 flex items-center transition-colors">
                    <span class="mr-1">📋</span> 切換列表模式
                </a>
            </div>
            <div class="flex items-center space-x-4">
                <a href="{{ route('calendar.index', ['year' => $prevMonth->year, 'month' => $prevMonth->month]) }}" class="text-gray-500 dark:text-gray-400 hover:text-indigo-600 p-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                </a>
                <span class="text-lg font-bold text-gray-800 dark:text-gray-200 w-24 text-center">{{ $year }} 年 {{ $month }} 月</span>
                <a href="{{ route('calendar.index', ['year' => $nextMonth->year, 'month' => $nextMonth->month]) }}" class="text-gray-500 dark:text-gray-400 hover:text-indigo-600 p-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </a>
            </div>
            <div class="flex items-center space-x-3">
                <a href="{{ route('calendar.index') }}" class="text-sm text-indigo-600 hover:underline">回到本月</a>
                @if(Auth::user()->isEditor())
                    <form action="{{ route('calendar.sync-holidays') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="text-sm bg-indigo-50 dark:bg-indigo-900/50 text-indigo-600 border border-indigo-200 px-3 py-1.5 rounded hover:bg-indigo-100 flex items-center transition-colors" title="從政府公開資料同步國定假日">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                            同步假日
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="bg-white dark:bg-gray-800 rounded shadow-sm overflow-hidden">
        {{-- 星期標題 --}}
        <div class="grid grid-cols-7 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider py-2">
            <div class="text-red-500">週日</div>
            <div>週一</div>
            <div>週二</div>
            <div>週三</div>
            <div>週四</div>
            <div>週五</div>
            <div class="text-red-500">週六</div>
        </div>

        {{-- 日曆內容 --}}
        <div class="grid grid-cols-7 border-l border-gray-200 dark:border-gray-700 auto-rows-fr" style="grid-auto-rows: minmax(120px, auto);">
            @foreach($days as $day)
                <div class="border-b border-r border-gray-200 dark:border-gray-700 p-2 flex flex-col 
                    {{ $day['isCurrentMonth'] ? 'bg-white dark:bg-gray-800' : 'bg-gray-50 dark:bg-gray-900' }} 
                    {{ $day['isToday'] ? 'bg-indigo-50 dark:bg-indigo-900/50 border-indigo-200' : '' }}
                    {{ isset($day['holiday']) ? 'bg-red-50 dark:bg-red-900/50/30' : '' }}">
                    
                    <div class="flex items-center justify-between mb-1">
                        <div class="flex items-center">
                            <span class="text-xs font-medium 
                                {{ $day['isCurrentMonth'] ? 'text-gray-700 dark:text-gray-300' : 'text-gray-400' }} 
                                {{ $day['isToday'] ? 'text-indigo-700 font-bold' : '' }}
                                {{ isset($day['holiday']) ? 'text-red-500 font-bold' : '' }}">
                                {{ $day['day'] }}
                            </span>
                            @if(isset($day['holiday']))
                                <span class="ml-2 text-[10px] bg-red-100 text-red-700 px-1.5 py-0.5 rounded shadow-sm border border-red-200" title="{{ $day['holiday']->name }}">
                                    {{ Str::limit($day['holiday']->name, 10) }}
                                </span>
                            @endif
                        </div>
                        
                        @if($day['records']->count() > 0)
                            <span class="text-[10px] bg-gray-200 text-gray-600 dark:text-gray-400 px-1.5 py-0.5 rounded-full">{{ $day['records']->count() }}</span>
                        @endif
                    </div>
                    
                    <div class="flex-1 space-y-1 overflow-y-auto" style="max-height: 180px;">
                        @php
                            $typeColors = [
                                'development' => 'bg-blue-100 text-blue-700 border-blue-200',
                                'test'        => 'bg-green-100 text-green-700 border-green-200',
                                'issue'       => 'bg-red-100 text-red-700 border-red-200',
                                'note'        => 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 border-gray-200 dark:border-gray-700',
                            ];
                        @endphp
                        @foreach($day['records'] as $record)
                            <a href="{{ route('records.show', $record) }}" class="block text-left text-xs p-1 border rounded truncate hover:shadow-md transition-shadow {{ $typeColors[$record->type] ?? 'bg-gray-100 dark:bg-gray-700' }}" title="{{ $record->title }}">
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

