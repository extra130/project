<x-records-layout title="每日工作日誌">
    <x-slot name="header">
        <div class="flex items-center justify-between print:hidden">
            <h2 class="text-xl font-semibold">📝 每日工作日誌</h2>
            <div class="flex items-center space-x-4">
                <label class="inline-flex items-center cursor-pointer print:hidden">
                    <input type="checkbox" class="sr-only peer" :checked="$store.dailyLog.showRemarks" @change="$store.dailyLog.toggleRemarks()">
                    <div class="relative w-9 h-5 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-indigo-300 dark:peer-focus:ring-indigo-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white dark:bg-gray-800 after:border-gray-300 dark:border-gray-600 after:border after:rounded-full after:h-4 after:w-4 after:transition-all dark:border-gray-600 peer-checked:bg-indigo-600"></div>
                    <span class="ms-2 text-sm font-medium text-gray-700 dark:text-gray-300">顯示備註</span>
                </label>
                <form method="GET" action="{{ route('daily-log.index') }}" class="flex items-center space-x-2">
                    <input type="date" name="date" value="{{ $targetDate->format('Y-m-d') }}" 
                           class="border border-gray-300 dark:border-gray-600 rounded px-3 py-1.5 text-sm"
                           onchange="this.form.submit()">
                </form>
                <button onclick="window.print()" class="text-sm bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 px-3 py-1.5 rounded hover:bg-gray-50 dark:bg-gray-900 flex items-center">
                    <span class="mr-1">🖨️</span> 列印 / 匯出 PDF
                </button>
            </div>
        </div>
    </x-slot>

    {{-- 列印時顯示的大標題 (平常隱藏) --}}
    <div class="hidden print:block mb-8 border-b-2 border-gray-800 pb-4">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">工作日誌 - {{ $targetDate->format('Y 年 m 月 d 日') }}</h1>
        <p class="text-gray-500 dark:text-gray-400 mt-2">共完成 {{ $totalRecords }} 項紀錄</p>
    </div>

    @if($totalRecords === 0)
        <div class="bg-white dark:bg-gray-800 rounded shadow-sm p-12 text-center text-gray-400 print:shadow-none print:border">
            <p class="text-lg">這一天沒有任何紀錄</p>
        </div>
    @else
        <div class="space-y-8">
            @foreach($groupedRecords as $projectName => $records)
                <div class="bg-white dark:bg-gray-800 rounded shadow-sm overflow-hidden print:shadow-none print:border print:break-inside-avoid" x-data x-show="$store.dailyLog.showRemarks || {{ $records->where('type', '!=', 'note')->count() > 0 ? 'true' : 'false' }}">
                    {{-- 專案標題列 --}}
                    @php $pColor = $records->first()->project->color ?? '#4f46e5'; @endphp
                    <div class="px-6 py-3 print:bg-gray-100 dark:bg-gray-700 print:border-gray-300 dark:border-gray-600 border-b"
                         style="background-color: {{ $pColor }}15; border-color: {{ $pColor }}30;">
                        <h3 class="text-lg font-bold print:text-black flex items-center" style="color: {{ $pColor }};">
                            <span class="mr-2" style="color: {{ $pColor }};">📁</span> {{ $projectName }}
                        </h3>
                    </div>
                    
                    {{-- 該專案底下的紀錄 --}}
                    <div class="divide-y divide-gray-100 dark:divide-gray-800">
                        @foreach($records as $record)
                            <div class="p-6" x-data x-show="$store.dailyLog.showRemarks || '{{ $record->type }}' !== 'note'">
                                <div class="flex items-start justify-between mb-3">
                                    <div>
                                        <div class="flex items-center space-x-2 mb-2">
                                            @php
                                                $typeColors = [
                                                    'development' => 'bg-blue-100 dark:bg-blue-900/40 text-blue-800 dark:text-blue-300 print:border-blue-800',
                                                    'test'        => 'bg-green-100 dark:bg-green-900/40 text-green-800 dark:text-green-300 print:border-green-800',
                                                    'issue'       => 'bg-red-100 dark:bg-red-900/40 text-red-800 dark:text-red-300 print:border-red-800',
                                                    'note'        => 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 print:border-gray-800',
                                                ];
                                                $typeLabels = [
                                                    'development' => '開發',
                                                    'test'        => '測試',
                                                    'issue'       => '問題',
                                                    'note'        => '備註',
                                                ];
                                            @endphp
                                            {{-- 類型徽章 --}}
                                            <span class="text-xs px-2 py-0.5 rounded print:border {{ $typeColors[$record->type] ?? 'bg-gray-100 dark:bg-gray-700' }}">
                                                {{ $typeLabels[$record->type] ?? $record->type }}
                                            </span>
                                            
                                            {{-- 模組名稱 --}}
                                            @if($record->module)
                                                <span class="text-xs text-gray-600 dark:text-gray-400 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 px-2 py-0.5 rounded">
                                                    🗂 {{ $record->module->name }}
                                                </span>
                                            @endif

                                            {{-- 使用 AI --}}
                                            @if($record->ai_tool)
                                                <span class="text-xs text-purple-700 dark:text-purple-400 bg-purple-50 dark:bg-purple-900/30 border border-purple-100 dark:border-purple-800 px-2 py-0.5 rounded font-medium">
                                                    🤖 {{ $record->ai_tool }}
                                                </span>
                                            @endif
                                        </div>
                                        <h4 class="text-base font-bold text-gray-900 dark:text-gray-100">{{ $record->title }}</h4>
                                    </div>
                                    <div class="text-sm text-gray-400">
                                        {{ $record->created_at->format('H:i') }}
                                    </div>
                                </div>
                                
                                {{-- 紀錄內容 (Markdown 渲染) --}}
                                <div class="prose max-w-none prose-sm prose-indigo prose-pre:bg-gray-800 prose-pre:text-gray-100 mt-3 print:prose-pre:bg-gray-100 dark:bg-gray-700 print:prose-pre:text-black print:prose-pre:border">
                                    {!! Str::markdown($record->content, ['html_input' => 'escape']) !!}
                                </div>

                                {{-- 標籤 --}}
                                @if($record->tags->count() > 0)
                                    <div class="mt-4 flex flex-wrap gap-2">
                                        @foreach($record->tags as $tag)
                                            <span class="text-xs text-gray-500 dark:text-gray-400">#{{ $tag->name }}</span>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    @endif
    
    @push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/styles/github-dark.min.css" media="screen">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/styles/github.min.css" media="print">
    <style>
        /* 隱藏側邊欄等非主要內容區塊，僅限列印模式 */
        @media print {
            aside { display: none !important; }
            body, html { background: white !important; height: auto !important; overflow: auto !important; }
            .overflow-hidden { overflow: visible !important; }
            .h-screen { height: auto !important; }
        }
    </style>
    @endpush

    @push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/highlight.min.js"></script>
    <script>hljs.highlightAll();</script>
        <script>
        document.addEventListener('alpine:init', () => {
            Alpine.store('dailyLog', {
                showRemarks: {{ auth()->user()->show_remarks_in_daily_log ?? true ? 'true' : 'false' }},
                toggleRemarks() {
                    this.showRemarks = !this.showRemarks;
                    fetch('{{ route('profile.preferences') }}', {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                        },
                        body: JSON.stringify({ show_remarks_in_daily_log: this.showRemarks })
                    }).then(() => {
                        window.dispatchEvent(new CustomEvent('flash-toast', { detail: { type: 'success', message: '設定已儲存' } }));
                    });
                }
            });
        });
    </script>
    @endpush
</x-records-layout>






