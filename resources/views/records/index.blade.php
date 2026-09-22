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
        @if(!empty($filters['tag']))
            <input type="hidden" name="tag" value="{{ $filters['tag'] }}">
            <div class="mb-3 flex items-center">
                <span class="text-sm text-gray-500 mr-2">標籤篩選：</span>
                <span class="bg-indigo-100 text-indigo-700 px-2 py-1 rounded text-sm font-medium">#{{ $filters['tag'] }}</span>
                <a href="{{ route('records.index', \Illuminate\Support\Arr::except($filters, ['tag'])) }}" class="ml-2 text-red-500 hover:text-red-700 text-xs">✕ 移除篩選</a>
            </div>
        @endif

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
            <div class="bg-white rounded shadow-sm p-4 hover:shadow-md transition-shadow border-l-4" style="border-left-color: {{ $record->project->color ?? '#4f46e5' }};">
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

                            {{-- Source / AI --}}
                            <div class="flex items-center space-x-1">
                                @if($record->source !== 'manual')
                                    <span class="text-xs px-2 py-0.5 rounded bg-yellow-50 text-yellow-700 border border-yellow-100">
                                        {{ strtoupper($record->source) }}
                                    </span>
                                @endif
                                
                                @if($record->ai_tool)
                                    <span class="text-xs px-1.5 py-0.5 rounded bg-purple-50 text-purple-700 border border-purple-100 flex items-center">
                                        🤖 {{ $record->ai_tool }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        <a href="{{ route('records.show', $record) }}"
                           class="font-medium text-gray-900 hover:text-indigo-600 text-lg">
                            {{ $record->title }}
                        </a>

                        <div class="mt-2 flex items-center space-x-2">
                            {{-- Project Name prominently colored --}}
                            <span class="text-xs font-bold px-2 py-0.5 rounded shadow-sm text-white" style="background-color: {{ $record->project->color ?? '#4f46e5' }};">
                                {{ $record->project->name ?? '' }}
                            </span>
                            
                            @if($record->module)
                                <span class="text-xs text-gray-500 font-medium">/ {{ $record->module->name }}</span>
                            @endif
                            <span class="text-xs text-gray-400 ml-2">{{ $record->created_at->format('Y/m/d') }}</span>
                            @if($record->files->count() > 0)
                                @php
                                    $attachmentData = [
                                        'title' => $record->title,
                                        'files' => $record->files->map(function($file) {
                                            return [
                                                'id' => $file->id,
                                                'name' => $file->display_name,
                                                'ext' => strtolower($file->extension),
                                                'size' => number_format($file->file_size / 1024, 1) . ' KB',
                                                'download_url' => route('record-files.download', $file),
                                                'preview_url' => route('record-files.preview', $file),
                                                'previewable' => in_array(strtolower($file->extension), ['pdf', 'html', 'htm', 'md', 'markdown', 'jpg', 'jpeg', 'png', 'gif']),
                                            ];
                                        })
                                    ];
                                @endphp
                                <button type="button" 
                                        data-attachments="{{ json_encode($attachmentData) }}"
                                        @click="$dispatch('open-attachments', JSON.parse($el.dataset.attachments))"
                                        class="text-xs text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded hover:bg-indigo-100 hover:underline">
                                    📎 {{ $record->files->count() }} 個附件
                                </button>
                            @endif
                        </div>
                        
                        @if($record->tags->count() > 0)
                            <div class="mt-2 flex flex-wrap gap-1">
                                @foreach($record->tags as $tag)
                                    <span class="bg-gray-100 text-gray-500 px-1.5 py-0.5 rounded text-[10px]">
                                        #{{ $tag->name }}
                                    </span>
                                @endforeach
                            </div>
                        @endif
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

    {{-- Attachment Modal --}}
    <div x-data="{
             isOpen: false,
             title: '',
             files: []
         }"
         @open-attachments.window="
             title = $event.detail.title;
             files = $event.detail.files;
             isOpen = true;
         "
         x-show="isOpen"
         class="fixed inset-0 z-50 overflow-y-auto"
         style="display: none;">
         
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div x-show="isOpen" 
                 x-transition.opacity
                 class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" 
                 @click="isOpen = false"></div>

            <div x-show="isOpen"
                 x-transition
                 class="relative inline-block px-4 pt-5 pb-4 overflow-hidden text-left align-bottom transition-all transform bg-white rounded-lg shadow-xl sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full sm:p-6">
                
                <div class="flex justify-between items-start mb-5">
                    <h3 class="text-lg font-medium leading-6 text-gray-900 truncate pr-4" x-text="'附件清單：' + title"></h3>
                    <button @click="isOpen = false" class="text-gray-400 hover:text-gray-500">
                        <span class="text-2xl">&times;</span>
                    </button>
                </div>

                <div class="space-y-3 max-h-[60vh] overflow-y-auto pr-2">
                    <template x-for="file in files" :key="file.id">
                        <div class="flex items-center justify-between p-3 border border-gray-100 rounded bg-gray-50 hover:bg-gray-100">
                            <div class="flex items-center flex-1 min-w-0">
                                <span class="text-xl mr-3" x-text="file.ext === 'pdf' ? '📄' : (file.ext === 'md' ? '📝' : '📎')"></span>
                                <div class="truncate">
                                    <div class="text-sm font-medium text-gray-900 truncate" x-text="file.name + '.' + file.ext"></div>
                                    <div class="text-xs text-gray-500" x-text="file.size"></div>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2 ml-4">
                                <template x-if="file.previewable">
                                    <a :href="file.preview_url" 
                                       target="_blank"
                                       class="px-3 py-1.5 text-xs font-medium text-indigo-600 bg-indigo-50 border border-indigo-200 rounded hover:bg-indigo-100">
                                        線上預覽
                                    </a>
                                </template>
                                <a :href="file.download_url"
                                   class="px-3 py-1.5 text-xs font-medium text-white bg-indigo-600 rounded hover:bg-indigo-700">
                                    下載
                                </a>
                            </div>
                        </div>
                    </template>
                    <template x-if="files.length === 0">
                        <p class="text-sm text-center text-gray-500">無附件</p>
                    </template>
                </div>
            </div>
        </div>
    </div>
</x-records-layout>
