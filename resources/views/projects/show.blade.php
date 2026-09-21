<x-records-layout :title="$project->name">
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold">{{ $project->name }}</h2>
                <p class="text-sm text-gray-500 mt-0.5">{{ $project->description }}</p>
            </div>
            @if(Auth::user()->isEditor())
                <a href="{{ route('projects.edit', $project) }}"
                   class="border border-gray-300 px-3 py-1.5 rounded text-sm hover:bg-gray-50">
                    編輯
                </a>
            @endif
        </div>
    </x-slot>

    {{-- Modules --}}
    <div class="flex items-center justify-between mb-4">
        <h3 class="font-semibold text-gray-700">模組列表</h3>
        @if(Auth::user()->isEditor())
            <a href="{{ route('projects.modules.create', $project) }}"
               class="text-sm bg-indigo-600 text-white px-3 py-1 rounded hover:bg-indigo-700">
                + 新增模組
            </a>
        @endif
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($project->modules as $module)
            <div class="bg-white rounded shadow-sm p-4 border-l-4
                {{ $module->status === 'active' ? 'border-indigo-400' : 'border-gray-300' }}">
                <div class="font-medium">{{ $module->name }}</div>
                <div class="text-xs text-gray-400 mt-0.5">{{ $module->description }}</div>
                <div class="mt-3 flex items-center space-x-3 text-xs">
                    <a href="{{ route('records.index', ['project_id' => $project->id, 'module_id' => $module->id]) }}"
                       class="text-indigo-600 hover:underline">查看紀錄</a>
                    @if(Auth::user()->isEditor())
                        <a href="{{ route('modules.edit', $module) }}" class="text-gray-400 hover:text-gray-600">編輯</a>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-span-3 text-center text-gray-400 py-8">尚無模組</div>
        @endforelse
    </div>

    {{-- Recent records --}}
    <div class="mt-8">
        <h3 class="font-semibold text-gray-700 mb-3">最近紀錄</h3>
        <a href="{{ route('records.index', ['project_id' => $project->id]) }}"
           class="inline-block text-sm text-indigo-600 hover:underline">
            查看此專案所有紀錄 →
        </a>
    </div>
</x-records-layout>
