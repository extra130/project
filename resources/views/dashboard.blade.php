<x-records-layout title="儀表板">
    <x-slot name="header">
        <h2 class="text-xl font-semibold">儀表板 (Dashboard)</h2>
    </x-slot>

    {{-- 頂部總覽 --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white rounded shadow-sm p-5 border-l-4 border-indigo-500">
            <div class="text-sm text-gray-500 font-medium">總專案數</div>
            <div class="text-3xl font-bold text-gray-800 mt-1">{{ number_format($stats['projects']) }}</div>
        </div>
        <div class="bg-white rounded shadow-sm p-5 border-l-4 border-green-500">
            <div class="text-sm text-gray-500 font-medium">總紀錄數</div>
            <div class="text-3xl font-bold text-gray-800 mt-1">{{ number_format($stats['records']) }}</div>
        </div>
        <div class="bg-white rounded shadow-sm p-5 border-l-4 border-yellow-500">
            <div class="text-sm text-gray-500 font-medium">總附件數</div>
            <div class="text-3xl font-bold text-gray-800 mt-1">{{ number_format($stats['files']) }}</div>
        </div>
    </div>

    {{-- 圖表區塊 --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        
        {{-- 左側：圓餅圖 --}}
        <div class="bg-white rounded shadow-sm p-5">
            <h3 class="text-sm font-semibold text-gray-600 mb-4">紀錄類型佔比</h3>
            <div class="relative h-64">
                <canvas id="typeChart"></canvas>
            </div>
        </div>

        {{-- 右側：折線圖 --}}
        <div class="bg-white rounded shadow-sm p-5 lg:col-span-2">
            <h3 class="text-sm font-semibold text-gray-600 mb-4">近 30 天活動趨勢</h3>
            <div class="relative h-64">
                <canvas id="trendChart"></canvas>
            </div>
        </div>
    </div>

    {{-- 最新紀錄 --}}
    <div class="bg-white rounded shadow-sm p-5">
        <h3 class="text-sm font-semibold text-gray-600 mb-4">最新活動</h3>
        <div class="space-y-3">
            @forelse($latestRecords as $record)
                <a href="{{ route('records.show', $record) }}" class="block p-3 border border-gray-100 rounded hover:bg-gray-50 transition-colors">
                    <div class="flex items-center justify-between">
                        <div>
                            <span class="text-xs font-semibold text-indigo-600 mr-2">[{{ $record->project->name }}]</span>
                            <span class="text-sm font-medium text-gray-800">{{ $record->title }}</span>
                        </div>
                        <div class="text-xs text-gray-400">{{ $record->created_at->diffForHumans() }}</div>
                    </div>
                </a>
            @empty
                <div class="text-sm text-gray-400 text-center py-4">尚無紀錄</div>
            @endforelse
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // 類型圓餅圖
        const ctxType = document.getElementById('typeChart').getContext('2d');
        new Chart(ctxType, {
            type: 'doughnut',
            data: {
                labels: ['開發 (Dev)', '測試 (Test)', '問題 (Issue)', '備註 (Note)'],
                datasets: [{
                    data: [{{ $types['development'] }}, {{ $types['test'] }}, {{ $types['issue'] }}, {{ $types['note'] }}],
                    backgroundColor: ['#3b82f6', '#22c55e', '#ef4444', '#9ca3af'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom' } }
            }
        });

        // 趨勢折線圖
        const ctxTrend = document.getElementById('trendChart').getContext('2d');
        new Chart(ctxTrend, {
            type: 'line',
            data: {
                labels: {!! json_encode($trendLabels) !!},
                datasets: [{
                    label: '新增紀錄數',
                    data: {!! json_encode($trendData) !!},
                    borderColor: '#6366f1',
                    backgroundColor: 'rgba(99, 102, 241, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.3,
                    pointRadius: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, ticks: { stepSize: 1 } }
                }
            }
        });
    </script>
    @endpush
</x-records-layout>
