{{-- Attachment edit form (Task 07) - spec §18 --}}
<x-records-layout title="修改附件資訊">
    <x-slot name="header">
        <h2 class="text-xl font-semibold">修改附件資訊</h2>
    </x-slot>

    <div class="bg-white dark:bg-gray-800 rounded shadow-sm p-6 max-w-xl">

        {{-- original_name shown but not editable (spec §18) --}}
        <div class="mb-4 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded p-3 text-sm">
            <div class="text-xs text-gray-400 mb-0.5">原始檔名（不可修改）</div>
            <div class="font-mono text-gray-700 dark:text-gray-300">{{ $recordFile->original_name }}</div>
        </div>

        <form method="POST" action="{{ route('record-files.update', $recordFile) }}">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">顯示名稱</label>
                <input type="text" name="display_name"
                       value="{{ old('display_name', $recordFile->display_name) }}"
                       class="w-full border border-gray-300 dark:border-gray-600 rounded px-3 py-2 text-sm"
                       maxlength="255">
                @error('display_name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">備註</label>
                <textarea name="note" rows="3"
                          class="w-full border border-gray-300 dark:border-gray-600 rounded px-3 py-2 text-sm"
                          placeholder="說明附件內容、用途或版本資訊">{{ old('note', $recordFile->note) }}</textarea>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">排序</label>
                <input type="number" name="sort_order"
                       value="{{ old('sort_order', $recordFile->sort_order) }}"
                       class="w-24 border border-gray-300 dark:border-gray-600 rounded px-3 py-2 text-sm">
                <p class="text-xs text-gray-400 mt-1">數字越小越前面</p>
            </div>

            <div class="flex items-center space-x-3">
                <button type="submit"
                        class="bg-indigo-600 text-white px-5 py-2 rounded text-sm hover:bg-indigo-700">
                    儲存
                </button>
                <a href="{{ route('records.show', $recordFile->record_id) }}"
                   class="text-gray-500 dark:text-gray-400 text-sm hover:underline">取消</a>
            </div>
        </form>
    </div>
</x-records-layout>



