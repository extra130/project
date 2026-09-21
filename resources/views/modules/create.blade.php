<x-records-layout title="新增模組">
    <x-slot name="header">
        <h2 class="text-xl font-semibold">{{ $project->name }} — 新增模組</h2>
    </x-slot>

    <div class="bg-white rounded shadow-sm p-6 max-w-2xl">
        <form method="POST" action="{{ route('projects.modules.store', $project) }}">
            @csrf

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">模組名稱 *</label>
                <input type="text" name="name" value="{{ old('name') }}"
                       class="w-full border border-gray-300 rounded px-3 py-2 text-sm" required maxlength="150">
                @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">說明</label>
                <textarea name="description" rows="3"
                          class="w-full border border-gray-300 rounded px-3 py-2 text-sm">{{ old('description') }}</textarea>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">排序</label>
                <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}"
                       class="w-24 border border-gray-300 rounded px-3 py-2 text-sm">
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">狀態</label>
                <select name="status" class="border border-gray-300 rounded px-3 py-2 text-sm">
                    <option value="active">啟用</option>
                    <option value="archived">封存</option>
                </select>
            </div>

            <div class="flex items-center space-x-3">
                <button type="submit" class="bg-indigo-600 text-white px-5 py-2 rounded text-sm hover:bg-indigo-700">
                    建立
                </button>
                <a href="{{ route('projects.modules.index', $project) }}" class="text-gray-500 text-sm hover:underline">取消</a>
            </div>
        </form>
    </div>
</x-records-layout>
