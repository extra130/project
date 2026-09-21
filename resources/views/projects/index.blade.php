<x-records-layout title="專案列表">
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold">專案列表</h2>
            @if(Auth::user()->isEditor())
                <a href="{{ route('projects.create') }}"
                   class="bg-indigo-600 text-white px-4 py-2 rounded text-sm hover:bg-indigo-700">
                    + 新增專案
                </a>
            @endif
        </div>
    </x-slot>

    <div class="bg-white rounded shadow-sm overflow-hidden">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-4 py-3 text-left font-medium text-gray-500">名稱</th>
                    <th class="px-4 py-3 text-left font-medium text-gray-500">說明</th>
                    <th class="px-4 py-3 text-left font-medium text-gray-500">狀態</th>
                    <th class="px-4 py-3 text-left font-medium text-gray-500">操作</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($projects as $project)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium">
                            <a href="{{ route('projects.show', $project) }}" class="text-indigo-600 hover:underline">
                                {{ $project->name }}
                            </a>
                        </td>
                        <td class="px-4 py-3 text-gray-500">{{ Str::limit($project->description, 60) }}</td>
                        <td class="px-4 py-3">
                            @if($project->status === 'active')
                                <span class="text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded">啟用</span>
                            @else
                                <span class="text-xs bg-gray-200 text-gray-600 px-2 py-0.5 rounded">封存</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 space-x-2">
                            <a href="{{ route('projects.modules.index', $project) }}" class="text-gray-500 hover:text-indigo-600">模組</a>
                            @if(Auth::user()->isEditor())
                                <a href="{{ route('projects.edit', $project) }}" class="text-gray-500 hover:text-indigo-600">編輯</a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-8 text-center text-gray-400">尚無專案</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-records-layout>
