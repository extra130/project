<x-records-layout :title="$module->name">
    <x-slot name="header">
        <div class="flex items-center space-x-4">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">
                ?? 璅∠?嚗{ $module->name }}
            </h2>
            <span class="text-sm text-gray-500 dark:text-gray-400">
                撠?嚗?a href="{{ route('projects.show', $module->project_id) }}" class="text-indigo-600 hover:underline">{{ $module->project->name }}</a>
            </span>
        </div>
        @if($module->description)
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">{{ $module->description }}</p>
        @endif
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Main content: Record Attachments --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="flex justify-between items-center">
                <h3 class="text-lg font-bold text-gray-800 dark:text-gray-200">?? 蝝??隞嗥蜇閬?({{ $files->count() }})</h3>
                <div class="space-x-3">
                    <a href="{{ route('records.index', ['project_id' => $module->project_id, 'module_id' => $module->id]) }}" class="text-sm border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 px-3 py-1.5 rounded hover:bg-gray-50 dark:bg-gray-900">
                        ???????                    </a>
                    <a href="{{ route('records.create', ['project_id' => $module->project_id, 'module_id' => $module->id]) }}" class="text-sm bg-indigo-600 text-white px-3 py-1.5 rounded hover:bg-indigo-700">
                        + ?啣?蝝??                    </a>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded shadow-sm overflow-hidden">
                @if($files->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm whitespace-nowrap">
                            <thead class="bg-gray-50 dark:bg-gray-900 border-b border-gray-100 text-gray-500 dark:text-gray-400">
                                <tr>
                                    <th class="px-6 py-3 font-medium">瑼??迂</th>
                                    <th class="px-6 py-3 font-medium">憭批? / 憿?</th>
                                    <th class="px-6 py-3 font-medium">?撅祉???/th>
                                    <th class="px-6 py-3 font-medium">銝??</th>
                                    <th class="px-6 py-3 font-medium text-right">??</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                                @foreach($files as $file)
                                    <tr class="hover:bg-gray-50 dark:bg-gray-900">
                                        <td class="px-6 py-3">
                                            <div class="font-medium text-gray-800 dark:text-gray-200">{{ $file->display_name }}</div>
                                            <div class="text-xs text-gray-400 mt-0.5">{{ $file->original_name }}</div>
                                        </td>
                                        <td class="px-6 py-3">
                                            <span class="text-gray-600 dark:text-gray-400">{{ number_format($file->file_size / 1024, 1) }} KB</span>
                                            <span class="text-xs text-gray-400 ml-1">({{ strtoupper($file->extension) }})</span>
                                        </td>
                                        <td class="px-6 py-3">
                                            <a href="{{ route('records.show', $file->record_id) }}" class="text-indigo-600 hover:underline truncate max-w-xs inline-block" title="{{ $file->record->title }}">
                                                {{ $file->record->title }}
                                            </a>
                                        </td>
                                        <td class="px-6 py-3 text-gray-500 dark:text-gray-400 text-xs">
                                            {{ $file->created_at->format('Y/m/d H:i') }}
                                        </td>
                                        <td class="px-6 py-3 text-right space-x-3">
                                            @if(in_array(strtolower($file->extension), ['pdf', 'html', 'htm', 'md', 'markdown', 'jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'drawio']))
                                                <a href="{{ route('record-files.preview', $file) }}" target="_blank" class="text-xs text-blue-600 hover:underline">?? ?汗</a>
                                            @endif
                                            <a href="{{ route('record-files.download', $file) }}" class="text-xs text-indigo-600 hover:underline">漎?銝?</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="p-8 text-center text-gray-400">
                        ?桀?瘝?隞颱??辣
                    </div>
                @endif
            </div>
        </div>

        {{-- Sidebar: Module Files --}}
        <div>
            <div class="bg-white dark:bg-gray-800 rounded shadow-sm p-4">
                <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                    璅∠??游惇?辣 ({{ $moduleFiles->count() }})
                </h3>

                @if(Auth::user()->isEditor())
                    <form method="POST"
                          action="{{ route('module-files.store', $module) }}"
                          enctype="multipart/form-data"
                          class="mb-4">
                        @csrf
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">
                            ?啣??辣 (?臬??訾???
                        </label>
                        <input type="file" name="files[]" multiple
                               class="block w-full text-sm text-gray-600 dark:text-gray-400
                                      file:mr-3 file:py-1 file:px-3
                                      file:rounded file:border-0
                                      file:text-xs file:font-medium
                                      file:bg-indigo-50 dark:bg-indigo-900/50 file:text-indigo-700
                                      hover:file:bg-indigo-100 mb-2">
                        <button type="submit"
                                class="w-full bg-indigo-600 text-white text-xs px-3 py-1.5 rounded hover:bg-indigo-700">
                            銝
                        </button>
                    </form>
                    <hr class="border-gray-100 mb-3">
                @endif

                <div class="space-y-3 max-h-[60vh] overflow-y-auto pr-1">
                    @forelse($moduleFiles as $file)
                        <div class="border border-gray-100 rounded p-3 hover:bg-gray-50 dark:bg-gray-900">
                            <div class="font-medium text-sm text-gray-800 dark:text-gray-200 break-words">
                                {{ $file->original_name }}
                            </div>
                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                {{ strtoupper($file->extension) }}
                                繚 {{ number_format($file->file_size / 1024, 1) }} KB
                            </div>

                            <div class="flex items-center space-x-3 mt-2 pt-2 border-t border-gray-50">
                                @if(in_array(strtolower($file->extension), ['pdf', 'html', 'htm', 'md', 'markdown', 'jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'drawio']))
                                    <a href="{{ route('module-files.preview', $file) }}"
                                       target="_blank"
                                       class="text-xs text-blue-600 hover:underline">?? ?汗</a>
                                @endif
                                <a href="{{ route('module-files.download', $file) }}"
                                   class="text-xs text-indigo-600 hover:underline">漎?銝?</a>
                                @if(Auth::user()->isEditor())
                                    <form method="POST" action="{{ route('module-files.destroy', $file) }}"
                                          onsubmit="return confirm('蝣箏??芷甇斗?隞塚?')"
                                          class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-xs text-red-500 hover:underline">
                                            ???芷
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="text-sm text-gray-400 text-center py-4">
                            撠銝隞颱?璅∠??辣
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-records-layout>


