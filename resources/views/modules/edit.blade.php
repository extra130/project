<x-records-layout :title="'編輯模組：' . $module->name">
    <x-slot name="header">
        <h2 class="text-xl font-semibold">編輯模組 — {{ $module->project->name }}</h2>
    </x-slot>

    <div class="bg-white dark:bg-gray-800 rounded shadow-sm p-6 max-w-2xl">
        <form method="POST" action="{{ route('modules.update', $module) }}">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">模組名稱 *</label>
                <input type="text" name="name" value="{{ old('name', $module->name) }}"
                       class="w-full border border-gray-300 dark:border-gray-600 rounded px-3 py-2 text-sm" required maxlength="150">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">說明</label>
                <textarea name="description" rows="3"
                          class="w-full border border-gray-300 dark:border-gray-600 rounded px-3 py-2 text-sm">{{ old('description', $module->description) }}</textarea>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">排序</label>
                <input type="number" name="sort_order" value="{{ old('sort_order', $module->sort_order) }}"
                       class="w-24 border border-gray-300 dark:border-gray-600 rounded px-3 py-2 text-sm">
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">狀態</label>
                <select name="status" class="border border-gray-300 dark:border-gray-600 rounded px-3 py-2 text-sm">
                    <option value="active" {{ old('status',$module->status)==='active'?'selected':'' }}>啟用</option>
                    <option value="archived" {{ old('status',$module->status)==='archived'?'selected':'' }}>封存</option>
                </select>
            </div>

            <div class="flex items-center space-x-3">
                <button type="submit" class="bg-indigo-600 text-white px-5 py-2 rounded text-sm hover:bg-indigo-700">
                    儲存
                </button>
                <a href="{{ route('projects.modules.index', $module->project_id) }}" class="text-gray-500 dark:text-gray-400 text-sm hover:underline">取消</a>
            </div>
        </form>
    </div>
</x-records-layout>

