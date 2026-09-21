<x-records-layout title="模組列表">
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold">{{ $project->name }} — 模組</h2>
            </div>
            @if(Auth::user()->isEditor())
                <a href="{{ route('projects.modules.create', $project) }}"
                   class="bg-indigo-600 text-white px-4 py-2 rounded text-sm hover:bg-indigo-700">
                    + 新增模組
                </a>
            @endif
        </div>
    </x-slot>

    <div class="bg-white rounded shadow-sm overflow-hidden">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-4 py-3 text-left text-gray-500 font-medium">排序</th>
                    <th class="px-4 py-3 text-left text-gray-500 font-medium">名稱</th>
                    <th class="px-4 py-3 text-left text-gray-500 font-medium">說明</th>
                    <th class="px-4 py-3 text-left text-gray-500 font-medium">狀態</th>
                    <th class="px-4 py-3 text-left text-gray-500 font-medium">操作</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($modules as $module)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-gray-400">{{ $module->sort_order }}</td>
                        <td class="px-4 py-3 font-medium">{{ $module->name }}</td>
                        <td class="px-4 py-3 text-gray-500">{{ Str::limit($module->description, 60) }}</td>
                        <td class="px-4 py-3">
                            @if($module->status === 'active')
                                <span class="text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded">啟用</span>
                            @else
                                <span class="text-xs bg-gray-200 text-gray-600 px-2 py-0.5 rounded">封存</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 space-x-2">
                            <a href="{{ route('records.index', ['project_id' => $project->id, 'module_id' => $module->id]) }}"
                               class="text-indigo-600 hover:underline">紀錄</a>
                            @if(Auth::user()->isEditor())
                                <a href="{{ route('modules.edit', $module) }}" class="text-gray-500 hover:text-indigo-600">編輯</a>
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
        <a href="{{ route('projects.show', $project) }}" class="text-sm text-gray-500 hover:underline">← 回專案</a>
    </div>
</x-records-layout>
