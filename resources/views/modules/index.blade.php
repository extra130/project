<x-records-layout title="模組列表">
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold">{{ $project->name }} - 模組管理</h2>
            </div>
            @if(Auth::user()->isEditor())
                <a href="{{ route('projects.modules.create', $project) }}"
                   class="bg-indigo-600 text-white px-4 py-2 rounded text-sm hover:bg-indigo-700">
                    + 新增模組
                </a>
            @endif
        </div>
    </x-slot>

    <div class="bg-white dark:bg-gray-800 rounded shadow-sm overflow-hidden">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50 dark:bg-gray-900 border-b">
                <tr>
                    <th class="w-12 px-4 py-3 text-center text-gray-500 dark:text-gray-400 font-medium"></th>
                    <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400 font-medium">名稱</th>
                    <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400 font-medium">描述</th>
                    <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400 font-medium">狀態</th>
                    <th class="px-4 py-3 text-left text-gray-500 dark:text-gray-400 font-medium">操作</th>
                </tr>
            </thead>
            <tbody id="sortable-modules-table" class="divide-y divide-gray-100 dark:divide-gray-800">
                @forelse($modules as $module)
                    <tr data-id="{{ $module->id }}" class="hover:bg-gray-50 dark:bg-gray-900 bg-white dark:bg-gray-800">
                        <td class="px-4 py-3 text-center cursor-move text-gray-400 hover:text-gray-600 dark:text-gray-400">
                            <svg class="w-5 h-5 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"></path></svg>
                        </td>
                        <td class="px-4 py-3 font-medium">{{ $module->name }}</td>
                        <td class="px-4 py-3 text-gray-500 dark:text-gray-400">{{ Str::limit($module->description, 60) }}</td>
                        <td class="px-4 py-3">
                            @if($module->status === 'active')
                                <span class="text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded">啟用</span>
                            @else
                                <span class="text-xs bg-gray-200 text-gray-600 dark:text-gray-400 px-2 py-0.5 rounded">封存</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 space-x-2">
                            <a href="{{ route('modules.show', $module) }}" class="text-indigo-600 hover:underline">附件</a>
                            <a href="{{ route('records.index', ['project_id' => $project->id, 'module_id' => $module->id]) }}"
                               class="text-indigo-600 hover:underline">紀錄</a>
                            @if(Auth::user()->isEditor())
                                <a href="{{ route('modules.edit', $module) }}" class="text-gray-500 dark:text-gray-400 hover:text-indigo-600">編輯</a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-gray-400">尚無模組</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        <a href="{{ route('projects.show', $project) }}" class="text-sm text-gray-500 dark:text-gray-400 hover:underline">← 返回專案</a>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const el = document.getElementById('sortable-modules-table');
            if (el) {
                new Sortable(el, {
                    animation: 150,
                    handle: '.cursor-move',
                    ghostClass: 'bg-indigo-50 dark:bg-indigo-900/50',
                    onEnd: function (evt) {
                        const order = Array.from(el.children).map(row => row.dataset.id).filter(id => id);
                        
                        fetch('{{ route('modules.reorder') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({ order: order })
                        }).then(response => {
                            if(response.ok) {
                                window.dispatchEvent(new CustomEvent('flash-toast', { detail: { type: 'success', message: '模組排序已更新' } }));
                            } else {
                                window.dispatchEvent(new CustomEvent('flash-toast', { detail: { type: 'error', message: '權限不足或更新失敗' } }));
                            }
                        });
                    },
                });
            }
        });
    </script>
    @endpush
</x-records-layout>



