<x-records-layout title="所有專案">
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold">所有專案</h2>
            @if(Auth::user()->isEditor())
                <a href="{{ route('projects.create') }}"
                   class="bg-indigo-600 text-white px-4 py-2 rounded text-sm hover:bg-indigo-700">
                    + 新增專案
                </a>
            @endif
        </div>
    </x-slot>

    <div class="bg-white dark:bg-gray-800 rounded shadow-sm overflow-hidden">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50 dark:bg-gray-900 border-b">
                <tr>
                    <th class="w-12 px-4 py-3 text-center text-gray-500 dark:text-gray-400"></th>
                    <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">名稱</th>
                    <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">描述</th>
                    <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">狀態</th>
                    <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">操作</th>
                </tr>
            </thead>
            <tbody id="sortable-projects" class="divide-y divide-gray-100 dark:divide-gray-800">
                @forelse($projects as $project)
                    <tr data-id="{{ $project->id }}" class="hover:bg-gray-50 dark:bg-gray-900 bg-white dark:bg-gray-800">
                        <td class="px-4 py-3 text-center cursor-move text-gray-400 hover:text-gray-600 dark:text-gray-400">
                            <svg class="w-5 h-5 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"></path></svg>
                        </td>
                        <td class="px-4 py-3 font-medium">
                            <a href="{{ route('projects.show', $project) }}" class="text-indigo-600 hover:underline">
                                {{ $project->name }}
                            </a>
                        </td>
                        <td class="px-4 py-3 text-gray-500 dark:text-gray-400">{{ Str::limit($project->description, 60) }}</td>
                        <td class="px-4 py-3">
                            @if($project->status === 'active')
                                <span class="text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded">啟用</span>
                            @else
                                <span class="text-xs bg-gray-200 text-gray-600 dark:text-gray-400 px-2 py-0.5 rounded">封存</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 space-x-2">
                            <a href="{{ route('projects.modules.index', $project) }}" class="text-gray-500 dark:text-gray-400 hover:text-indigo-600">模組</a>
                            @if(Auth::user()->isEditor())
                                <a href="{{ route('projects.edit', $project) }}" class="text-gray-500 dark:text-gray-400 hover:text-indigo-600">編輯</a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <div class="w-16 h-16 bg-gray-50 dark:bg-gray-900 rounded-full flex items-center justify-center mb-4">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path></svg>
                                </div>
                                <h3 class="text-lg font-medium text-gray-800 dark:text-gray-200 mb-1">尚無專案</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">目前系統中還沒有任何專案資料。</p>
                                @if(Auth::user()->isEditor())
                                    <a href="{{ route('projects.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-medium text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                                        + 建立第一筆專案
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const el = document.getElementById('sortable-projects');
            if (el) {
                new Sortable(el, {
                    animation: 150,
                    handle: '.cursor-move',
                    ghostClass: 'bg-indigo-50 dark:bg-indigo-900/50',
                    onEnd: function (evt) {
                        const order = Array.from(el.children).map(row => row.dataset.id).filter(id => id);
                        
                        fetch('{{ route('projects.reorder') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({ order: order })
                        }).then(response => {
                            if(response.ok) {
                                window.dispatchEvent(new CustomEvent('flash-toast', { detail: { type: 'success', message: '專案排序已更新' } }));
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

