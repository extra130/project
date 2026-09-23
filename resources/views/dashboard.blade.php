<x-records-layout title="儀表板">
    <x-slot name="header">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-200 tracking-tight">儀表板</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">掌握專案進度與近期活動數據</p>
        </div>
    </x-slot>

    {{-- 頂部總覽卡片 (使用漸層與圖示感設計) --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        {{-- 專案卡片 --}}
        <div class="bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-xl shadow-md p-6 text-white relative overflow-hidden transition hover:shadow-lg">
            <div class="relative z-10">
                <div class="text-indigo-100 text-sm font-medium mb-1">總專案數</div>
                <div class="text-4xl font-extrabold">{{ number_format($stats['projects']) }}</div>
            </div>
            <div class="absolute right-4 bottom-[-10px] opacity-20 text-7xl">📁</div>
        </div>

        {{-- 紀錄卡片 --}}
        <div class="bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl shadow-md p-6 text-white relative overflow-hidden transition hover:shadow-lg">
            <div class="relative z-10">
                <div class="text-emerald-100 text-sm font-medium mb-1">總紀錄數</div>
                <div class="text-4xl font-extrabold">{{ number_format($stats['records']) }}</div>
            </div>
            <div class="absolute right-4 bottom-[-10px] opacity-20 text-7xl">📄</div>
        </div>

        {{-- 附件卡片 --}}
        <div class="bg-gradient-to-br from-amber-400 to-amber-500 rounded-xl shadow-md p-6 text-white relative overflow-hidden transition hover:shadow-lg">
            <div class="relative z-10">
                <div class="text-amber-50 text-sm font-medium mb-1">總附件數</div>
                <div class="text-4xl font-extrabold">{{ number_format($stats['files']) }}</div>
            </div>
            <div class="absolute right-4 bottom-[-10px] opacity-20 text-7xl">📎</div>
        </div>
    </div>

    {{-- 圖表區塊 --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        {{-- 左側：圓餅圖 --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 p-6 flex flex-col">
            <h3 class="text-base font-bold text-gray-700 dark:text-gray-300 mb-6 flex items-center">
                <span class="w-1.5 h-4 bg-indigo-50 dark:bg-indigo-900/500 rounded-full mr-2"></span>
                紀錄類型分佈
            </h3>
            <div class="relative flex-1 min-h-[260px]">
                <canvas id="typeChart"></canvas>
            </div>
        </div>

        {{-- 右側：折線圖 --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 p-6 lg:col-span-2 flex flex-col">
            <h3 class="text-base font-bold text-gray-700 dark:text-gray-300 mb-6 flex items-center">
                <span class="w-1.5 h-4 bg-emerald-500 rounded-full mr-2"></span>
                近 30 天活躍趨勢
            </h3>
            <div class="relative flex-1 min-h-[260px]">
                <canvas id="trendChart"></canvas>
            </div>
        </div>
    </div>

    {{-- 最新紀錄 --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 p-6 mb-8">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-base font-bold text-gray-700 dark:text-gray-300 flex items-center">
                <span class="w-1.5 h-4 bg-amber-400 rounded-full mr-2"></span>
                最新活動紀錄
            </h3>
            <a href="{{ route('records.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium hover:underline">查看全部 &rarr;</a>
        </div>
        
        <div class="space-y-4">
            @forelse($latestRecords as $record)
                <a href="{{ route('records.show', $record) }}" class="group block bg-gray-50 dark:bg-gray-900 rounded-lg p-4 hover:bg-indigo-50 dark:bg-indigo-900/50 hover:shadow-sm transition-all duration-200 border border-transparent hover:border-indigo-100">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="flex-shrink-0 w-2 h-2 rounded-full 
                                {{ $record->type === 'development' ? 'bg-blue-500' : '' }}
                                {{ $record->type === 'test' ? 'bg-green-50 dark:bg-green-900/500' : '' }}
                                {{ $record->type === 'issue' ? 'bg-red-50 dark:bg-red-900/500' : '' }}
                                {{ $record->type === 'note' ? 'bg-gray-50 dark:bg-gray-9000' : '' }}">
                            </div>
                            <div>
                                <span class="text-xs font-bold text-indigo-600 mr-2 bg-indigo-100 px-2 py-0.5 rounded-full">{{ $record->project->name }}</span>
                                <span class="text-sm font-semibold text-gray-800 dark:text-gray-200 group-hover:text-indigo-700">{{ $record->title }}</span>
                            </div>
                        </div>
                        <div class="text-xs text-gray-400 font-medium whitespace-nowrap ml-4">
                            {{ $record->created_at->diffForHumans() }}
                        </div>
                    </div>
                </a>
            @empty
                <div class="text-sm text-gray-400 text-center py-6 bg-gray-50 dark:bg-gray-900 rounded-lg border border-dashed border-gray-200 dark:border-gray-700">
                    尚無任何紀錄
                </div>
            @endforelse
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // 類型圓餅圖 (甜甜圈風格)
        const ctxType = document.getElementById('typeChart').getContext('2d');
        new Chart(ctxType, {
            type: 'doughnut',
            data: {
                labels: ['開發 (Dev)', '測試 (Test)', '問題 (Issue)', '備註 (Note)'],
                datasets: [{
                    data: [{{ $types['development'] }}, {{ $types['test'] }}, {{ $types['issue'] }}, {{ $types['note'] }}],
                    backgroundColor: ['#3b82f6', '#10b981', '#ef4444', '#9ca3af'],
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '75%', // 讓中間空心更大，看起來更現代
                plugins: { 
                    legend: { 
                        position: 'bottom',
                        labels: { padding: 20, usePointStyle: true, boxWidth: 8 }
                    } 
                }
            }
        });

        // 趨勢折線圖 (平滑曲線)
        const ctxTrend = document.getElementById('trendChart').getContext('2d');
        new Chart(ctxTrend, {
            type: 'line',
            data: {
                labels: {!! json_encode($trendLabels) !!},
                datasets: [{
                    label: '新增紀錄數',
                    data: {!! json_encode($trendData) !!},
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4, // 平滑曲線
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#10b981',
                    pointBorderWidth: 2,
                    pointRadius: 3,
                    pointHoverRadius: 5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { maxTicksLimit: 10, color: '#9ca3af' }
                    },
                    y: { 
                        beginAtZero: true, 
                        border: { display: false },
                        grid: { color: '#f3f4f6' },
                        ticks: { stepSize: 1, color: '#9ca3af' } 
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index',
                },
            }
        });
    </script>
    @endpush
</x-records-layout>

