{{-- Records index - spec §16 UI, Task 04+05 --}}
<x-records-layout title="紀錄列表">
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold">紀錄列表</h2>
            <a href="{{ route('calendar.index') }}" class="text-sm bg-white border border-gray-300 text-gray-700 px-3 py-1.5 rounded hover:bg-gray-50 flex items-center">
                <span class="mr-1">📅</span> 切換月曆模式
            </a>
        </div>
    </x-slot>

    {{-- ========== Search / Filter bar ========== --}}
    <form method="GET" action="{{ route('records.index') }}" class="bg-white rounded shadow-sm p-4 mb-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">

            {{-- Keyword --}}
            <div class="lg:col-span-2">
                <input type="text" name="keyword" placeholder="關鍵字搜尋..."
                       value="{{ $filters['keyword'] ?? '' }}"
                       class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
            </div>

            {{-- Project --}}
            <div>
                <select name="project_id" id="filter-project"
                        class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
                    <option value="">— 所有專案 —</option>
                    @foreach($projects as $p)
                        <option value="{{ $p->id }}"
                                {{ ($filters['project_id'] ?? '') == $p->id ? 'selected' : '' }}>
                            {{ $p->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Module — 動態載入 --}}
            <div>
                <select name="module_id" id="filter-module"
                        class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
                    <option value="">— 所有模組 —</option>
                    @foreach($modules as $m)
                        <option value="{{ $m->id }}"
                                {{ ($filters['module_id'] ?? '') == $m->id ? 'selected' : '' }}>
                            {{ $m->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Type --}}
            <div>
                <select name="type" class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
                    <option value="">— 所有類型 —</option>
                    <option value="development" {{ ($filters['type'] ?? '') === 'development' ? 'selected' : '' }}>開發紀錄</option>
                    <option value="test"        {{ ($filters['type'] ?? '') === 'test'        ? 'selected' : '' }}>測試紀錄</option>
                    <option value="issue"       {{ ($filters['type'] ?? '') === 'issue'       ? 'selected' : '' }}>問題紀錄</option>
                    <option value="note"        {{ ($filters['type'] ?? '') === 'note'        ? 'selected' : '' }}>人工備註</option>
                </select>
            </div>

            {{-- Date range --}}
            <div>
                <input type="date" name="date_from" value="{{ $filters['date_from'] ?? '' }}"
                       class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
            </div>
            <div>
                <input type="date" name="date_to" value="{{ $filters['date_to'] ?? '' }}"
                       class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
            </div>

            {{-- Submit --}}
            <div class="flex items-center space-x-2">
                <button type="submit"
                        class="bg-indigo-600 text-white px-4 py-2 rounded text-sm hover:bg-indigo-700">
                    搜尋
                </button>
                <a href="{{ route('records.index') }}" class="text-gray-400 text-sm hover:underline">清除</a>
            </div>
        </div>
    </form>

    <script>
    (function () {
        const projectSel = document.getElementById('filter-project');
        const moduleSel  = document.getElementById('filter-module');
        const savedModuleId = "{{ $filters['module_id'] ?? '' }}";

        async function loadModules(projectId) {
            moduleSel.innerHTML = '<option value="">— 所有模組 —</option>';
            if (!projectId) return;

            try {
                const res  = await fetch(`/project/public/ajax/projects/${projectId}/modules`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    }
                });

                if (!res.ok) return;
                const data = await res.json();

                data.forEach(m => {
                    const opt = document.createElement('option');
                    opt.value = m.id;
                    opt.textContent = m.name;
                    if (String(m.id) === savedModuleId) opt.selected = true;
                    moduleSel.appendChild(opt);
                });
            } catch (e) {
                // fallback: 靜默失敗，不影響功能
            }
        }

        projectSel.addEventListener('change', () => loadModules(projectSel.value));

        // 初始化：若已有 project_id，載入對應模組
        if (projectSel.value) loadModules(projectSel.value);
    })();
    </script>

    {{-- ========== Records list ========== --}}
    <div class="space-y-3">
        @forelse($records as $record)
            <div class="bg-white rounded shadow-sm p-4 hover:shadow-md transition-shadow">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <div class="flex items-center space-x-2 mb-1">
                            {{-- Type badge --}}
                            @php
                                $typeColors = [
                                    'development' => 'bg-blue-100 text-blue-700',
                                    'test'        => 'bg-green-100 text-green-700',
                                    'issue'       => 'bg-red-100 text-red-700',
                                    'note'        => 'bg-gray-100 text-gray-600',
                                ];
                                $typeLabels = [
                                    'development' => '開發',
                                    'test'        => '測試',
                                    'issue'       => '問題',
                                    'note'        => '備註',
                                ];
                            @endphp
                            <span class="text-xs px-2 py-0.5 rounded {{ $typeColors[$record->type] ?? 'bg-gray-100 text-gray-600' }}">
                                {{ $typeLabels[$record->type] ?? $record->type }}
                            </span>

                            {{-- Source --}}
                            @if($record->source !== 'manual')
                                <span class="text-xs px-2 py-0.5 rounded bg-yellow-50 text-yellow-700">
                                    {{ strtoupper($record->source) }}
                                </span>
                            @endif
                        </div>

                        <a href="{{ route('records.show', $record) }}"
                           class="font-medium text-gray-900 hover:text-indigo-600">
                            {{ $record->title }}
                        </a>

                        <div class="text-xs text-gray-400 mt-1 space-x-2">
                            <span>{{ $record->project->name ?? '' }}</span>
                            @if($record->module)
                                <span>/ {{ $record->module->name }}</span>
                            @endif
                            <span>{{ $record->created_at->format('Y/m/d') }}</span>
                            @if($record->files->count() > 0)
                                <span>📎 {{ $record->files->count() }} 個附件</span>
                            @endif
                        </div>
                    </div>

                    <div class="flex items-center space-x-2 ml-4">
                        @if(Auth::user()->isEditor())
                            <a href="{{ route('records.edit', $record) }}"
                               class="text-xs text-gray-400 hover:text-indigo-600">編輯</a>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white rounded shadow-sm p-12 text-center text-gray-400">
                <p class="text-lg">尚無紀錄</p>
                @if(Auth::user()->isEditor())
                    <a href="{{ route('records.create') }}" class="text-indigo-600 hover:underline text-sm mt-2 inline-block">
                        + 建立第一筆紀錄
                    </a>
                @endif
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    <div class="mt-6">
        {{ $records->links() }}
    </div>
</x-records-layout>
