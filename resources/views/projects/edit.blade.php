<x-records-layout :title="'編輯：' . $project->name">
    <x-slot name="header">
        <h2 class="text-xl font-semibold">編輯專案</h2>
    </x-slot>

    <div class="bg-white dark:bg-gray-800 rounded shadow-sm p-6 max-w-2xl">
        <form method="POST" action="{{ route('projects.update', $project) }}">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">專案名稱 *</label>
                <input type="text" name="name" value="{{ old('name', $project->name) }}"
                       class="w-full border border-gray-300 dark:border-gray-600 rounded px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500"
                       required maxlength="150">
                @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">說明</label>
                <textarea name="description" rows="3"
                          class="w-full border border-gray-300 dark:border-gray-600 rounded px-3 py-2 text-sm">{{ old('description', $project->description) }}</textarea>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">專案代表色</label>
                <div class="flex items-center space-x-2">
                    <input type="color" name="color" value="{{ old('color', $project->color ?? '#4f46e5') }}"
                           class="h-8 w-8 border border-gray-300 dark:border-gray-600 rounded cursor-pointer p-0">
                    <span class="text-xs text-gray-500 dark:text-gray-400">選擇一個能代表此專案的顏色</span>
                </div>
                @error('color')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">狀態</label>
                <select name="status" class="border border-gray-300 dark:border-gray-600 rounded px-3 py-2 text-sm">
                    <option value="active" {{ old('status',$project->status)==='active'?'selected':'' }}>啟用</option>
                    <option value="archived" {{ old('status',$project->status)==='archived'?'selected':'' }}>封存</option>
                </select>
                <p class="text-xs text-gray-400 mt-1">封存後不代表刪除，仍可查詢。</p>
            </div>

            <div class="flex items-center space-x-3">
                <button type="submit"
                        class="bg-indigo-600 text-white px-5 py-2 rounded text-sm hover:bg-indigo-700">
                    儲存
                </button>
                <a href="{{ route('projects.show', $project) }}" class="text-gray-500 dark:text-gray-400 text-sm hover:underline">取消</a>
            </div>
        </form>
    </div>
</x-records-layout>



