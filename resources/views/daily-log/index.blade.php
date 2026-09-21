<x-records-layout title="每日工作日誌">
    <x-slot name="header">
        <div class="flex items-center justify-between print:hidden">
            <h2 class="text-xl font-semibold">📝 每日工作日誌</h2>
            <div class="flex items-center space-x-4">
                <form method="GET" action="{{ route('daily-log.index') }}" class="flex items-center space-x-2">
                    <input type="date" name="date" value="{{ $targetDate->format('Y-m-d') }}" 
                           class="border border-gray-300 rounded px-3 py-1.5 text-sm"
                           onchange="this.form.submit()">
                </form>
                <button onclick="window.print()" class="text-sm bg-white border border-gray-300 text-gray-700 px-3 py-1.5 rounded hover:bg-gray-50 flex items-center">
                    <span class="mr-1">🖨️</span> 列印 / 匯出 PDF
                </button>
            </div>
        </div>
    </x-slot>

    {{-- 列印時顯示的大標題 (平常隱藏) --}}
    <div class="hidden print:block mb-8 border-b-2 border-gray-800 pb-4">
        <h1 class="text-3xl font-bold text-gray-900">工作日誌 - {{ $targetDate->format('Y 年 m 月 d 日') }}</h1>
        <p class="text-gray-500 mt-2">共完成 {{ $totalRecords }} 項紀錄</p>
    </div>

    @if($totalRecords === 0)
        <div class="bg-white rounded shadow-sm p-12 text-center text-gray-400 print:shadow-none print:border">
            <p class="text-lg">這一天沒有任何紀錄</p>
        </div>
    @else
        <div class="space-y-8">
            @foreach($groupedRecords as $projectName => $records)
                <div class="bg-white rounded shadow-sm overflow-hidden print:shadow-none print:border print:break-inside-avoid">
                    {{-- 專案標題列 --}}
                    <div class="bg-indigo-50 border-b border-indigo-100 px-6 py-3 print:bg-gray-100 print:border-gray-300">
                        <h3 class="text-lg font-bold text-indigo-900 print:text-black">
                            📁 專案：{{ $projectName }}
                        </h3>
                    </div>
                    
                    {{-- 該專案底下的紀錄 --}}
                    <div class="divide-y divide-gray-100">
                        @foreach($records as $record)
                            <div class="p-6">
                                <div class="flex items-start justify-between mb-3">
                                    <div>
                                        <div class="flex items-center space-x-2 mb-2">
                                            @php
                                                $typeColors = [
                                                    'development' => 'bg-blue-100 text-blue-800 print:border-blue-800',
                                                    'test'        => 'bg-green-100 text-green-800 print:border-green-800',
                                                    'issue'       => 'bg-red-100 text-red-800 print:border-red-800',
                                                    'note'        => 'bg-gray-100 text-gray-800 print:border-gray-800',
                                                ];
                                                $typeLabels = [
                                                    'development' => '開發',
                                                    'test'        => '測試',
                                                    'issue'       => '問題',
                                                    'note'        => '備註',
                                                ];
                                            @endphp
                                            {{-- 類型徽章 --}}
                                            <span class="text-xs px-2 py-0.5 rounded print:border {{ $typeColors[$record->type] ?? 'bg-gray-100' }}">
                                                {{ $typeLabels[$record->type] ?? $record->type }}
                                            </span>
                                            
                                            {{-- 模組名稱 --}}
                                            @if($record->module)
                                                <span class="text-xs text-gray-500 bg-gray-50 border border-gray-200 px-2 py-0.5 rounded">
                                                    🗂 {{ $record->module->name }}
                                                </span>
                                            @endif

                                            {{-- 使用 AI --}}
                                            @if($record->ai_tool)
                                                <span class="text-xs text-purple-700 bg-purple-50 border border-purple-100 px-2 py-0.5 rounded font-medium">
                                                    🤖 {{ $record->ai_tool }}
                                                </span>
                                            @endif
                                        </div>
                                        <h4 class="text-base font-bold text-gray-900">{{ $record->title }}</h4>
                                    </div>
                                    <div class="text-sm text-gray-400">
                                        {{ $record->created_at->format('H:i') }}
                                    </div>
                                </div>
                                
                                {{-- 紀錄內容 (Markdown 渲染) --}}
                                <div class="prose max-w-none prose-sm prose-indigo prose-pre:bg-gray-800 prose-pre:text-gray-100 mt-3 print:prose-pre:bg-gray-100 print:prose-pre:text-black print:prose-pre:border">
                                    {!! Str::markdown($record->content, ['html_input' => 'escape']) !!}
                                </div>

                                {{-- 標籤 --}}
                                @if($record->tags->count() > 0)
                                    <div class="mt-4 flex flex-wrap gap-2">
                                        @foreach($record->tags as $tag)
                                            <span class="text-xs text-gray-500">#{{ $tag->name }}</span>
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
    @endpush
</x-records-layout>
