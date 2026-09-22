<x-records-layout :title="$module->name">
    <x-slot name="header">
        <div class="flex items-center space-x-4">
            <h2 class="text-xl font-semibold text-gray-800">
                🗂 模組：{{ $module->name }}
            </h2>
            <span class="text-sm text-gray-500">
                專案：<a href="{{ route('projects.show', $module->project_id) }}" class="text-indigo-600 hover:underline">{{ $module->project->name }}</a>
            </span>
        </div>
        @if($module->description)
            <p class="text-sm text-gray-500 mt-2">{{ $module->description }}</p>
        @endif
    </x-slot>

    <div class="space-y-6">
        <div class="flex justify-between items-center">
            <h3 class="text-lg font-bold text-gray-800">📎 附件總覽 ({{ $files->count() }})</h3>
            <div class="space-x-3">
                <a href="{{ route('records.index', ['project_id' => $module->project_id, 'module_id' => $module->id]) }}" class="text-sm border border-gray-300 text-gray-700 px-3 py-1.5 rounded hover:bg-gray-50">
                    看所有相關紀錄
                </a>
                <a href="{{ route('records.create', ['project_id' => $module->project_id, 'module_id' => $module->id]) }}" class="text-sm bg-indigo-600 text-white px-3 py-1.5 rounded hover:bg-indigo-700">
                    + 新增紀錄
                </a>
            </div>
        </div>

        <div class="bg-white rounded shadow-sm overflow-hidden">
            @if($files->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm whitespace-nowrap">
                        <thead class="bg-gray-50 border-b border-gray-100 text-gray-500">
                            <tr>
                                <th class="px-6 py-3 font-medium">檔案名稱</th>
                                <th class="px-6 py-3 font-medium">大小 / 類型</th>
                                <th class="px-6 py-3 font-medium">所屬紀錄</th>
                                <th class="px-6 py-3 font-medium">上傳時間</th>
                                <th class="px-6 py-3 font-medium text-right">操作</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($files as $file)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-3">
                                        <div class="font-medium text-gray-800">{{ $file->display_name }}</div>
                                        <div class="text-xs text-gray-400 mt-0.5">{{ $file->original_name }}</div>
                                    </td>
                                    <td class="px-6 py-3">
                                        <span class="text-gray-600">{{ number_format($file->file_size / 1024, 1) }} KB</span>
                                        <span class="text-xs text-gray-400 ml-1">({{ strtoupper($file->extension) }})</span>
                                    </td>
                                    <td class="px-6 py-3">
                                        <a href="{{ route('records.show', $file->record_id) }}" class="text-indigo-600 hover:underline truncate max-w-xs inline-block" title="{{ $file->record->title }}">
                                            {{ $file->record->title }}
                                        </a>
                                    </td>
                                    <td class="px-6 py-3 text-gray-500 text-xs">
                                        {{ $file->created_at->format('Y/m/d H:i') }}
                                    </td>
                                    <td class="px-6 py-3 text-right space-x-3">
                                        @if(in_array(strtolower($file->extension), ['pdf', 'html', 'htm', 'md', 'markdown', 'jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'drawio']))
                                            <a href="{{ route('record-files.preview', $file) }}" target="_blank" class="text-xs text-blue-600 hover:underline">👁 預覽</a>
                                        @endif
                                        <a href="{{ route('record-files.download', $file) }}" class="text-xs text-indigo-600 hover:underline">⬇ 下載</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="p-8 text-center text-gray-400">
                    目前沒有任何附件
                </div>
            @endif
        </div>
    </div>
</x-records-layout>
