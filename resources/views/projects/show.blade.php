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

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- ========== Main content (Modules & Records) ========== --}}
        <div class="lg:col-span-2 space-y-8">
            {{-- Modules --}}
            <div>
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-semibold text-gray-700">模組列表</h3>
                    @if(Auth::user()->isEditor())
                        <a href="{{ route('projects.modules.create', $project) }}"
                           class="text-sm bg-indigo-600 text-white px-3 py-1 rounded hover:bg-indigo-700">
                            + 新增模組
                        </a>
                    @endif
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @forelse($project->modules as $module)
                            <div class="bg-white rounded shadow-sm p-4 border-l-4
                                {{ $module->status === 'active' ? 'border-indigo-400' : 'border-gray-300' }}">
                                <a href="{{ route('modules.show', $module) }}" class="font-medium text-lg text-indigo-700 hover:underline flex items-center">
                                    <span class="mr-1">🗂</span> {{ $module->name }}
                                </a>
                                <div class="text-xs text-gray-400 mt-1 truncate">{{ $module->description }}</div>
                                <div class="mt-3 flex items-center space-x-3 text-xs">
                                    <a href="{{ route('modules.show', $module) }}"
                                       class="text-indigo-600 hover:underline">附件總覽</a>
                                    <span class="text-gray-300">|</span>
                                    <a href="{{ route('records.index', ['project_id' => $project->id, 'module_id' => $module->id]) }}"
                                       class="text-indigo-600 hover:underline">所有紀錄</a>
                                    @if(Auth::user()->isEditor())
                                        <span class="text-gray-300">|</span>
                                        <a href="{{ route('modules.edit', $module) }}" class="text-gray-400 hover:text-gray-600">編輯</a>
                                    @endif
                                </div>
                            </div>
                    @empty
                        <div class="col-span-2 text-center text-gray-400 py-8 bg-white rounded shadow-sm">尚無模組</div>
                    @endforelse
                </div>
            </div>

            {{-- Recent records --}}
            <div>
                <h3 class="font-semibold text-gray-700 mb-3">最近紀錄</h3>
                <a href="{{ route('records.index', ['project_id' => $project->id]) }}"
                   class="inline-block text-sm text-indigo-600 hover:underline">
                    查看此專案所有紀錄 →
                </a>
            </div>
        </div>

        {{-- ========== Project Attachments Sidebar ========== --}}
        <div class="space-y-4">
            <div class="bg-white rounded shadow-sm p-4">
                <h3 class="text-sm font-medium text-gray-700 mb-3">
                    專案文件（{{ $project->files->count() }}）
                </h3>

                @if(Auth::user()->isEditor())
                    <form method="POST"
                          action="{{ route('project-files.store', $project) }}"
                          enctype="multipart/form-data"
                          class="mb-4">
                        @csrf
                        <label class="block text-xs font-medium text-gray-500 mb-1">
                            新增文件（可多選上傳）
                        </label>
                        <input type="file" name="files[]" multiple
                               class="block w-full text-sm text-gray-600
                                      file:mr-3 file:py-1 file:px-3
                                      file:rounded file:border-0
                                      file:text-xs file:font-medium
                                      file:bg-indigo-50 file:text-indigo-700
                                      hover:file:bg-indigo-100 mb-2">
                        <button type="submit"
                                class="w-full bg-indigo-600 text-white text-xs px-3 py-1.5 rounded hover:bg-indigo-700">
                            ↑ 上傳
                        </button>
                    </form>
                    <hr class="border-gray-100 mb-3">
                @endif

                <div class="space-y-3">
                    @forelse($project->files as $file)
                        <div class="border border-gray-100 rounded p-3 text-sm">
                            <div class="font-medium text-gray-800 truncate" title="{{ $file->display_name }}">
                                {{ $file->display_name }}
                            </div>
                            <div class="text-xs text-gray-400 mt-1">
                                {{ strtoupper($file->extension) }}
                                · {{ number_format($file->file_size / 1024, 1) }} KB
                            </div>

                              <div class="flex items-center space-x-3 mt-2 pt-2 border-t border-gray-50">
                                  @if(in_array(strtolower($file->extension), ['pdf', 'html', 'htm', 'md', 'markdown', 'jpg', 'jpeg', 'png', 'gif', 'webp', 'svg']))
                                      <a href="{{ route('project-files.preview', $file) }}"
                                         target="_blank"
                                         class="text-xs text-blue-600 hover:underline">👁 預覽</a>
                                  @endif
                                  <a href="{{ route('project-files.download', $file) }}"
                                     class="text-xs text-indigo-600 hover:underline">⬇ 下載</a>
                                @if(Auth::user()->isEditor())
                                    <form method="POST" action="{{ route('project-files.destroy', $file) }}"
                                          onsubmit="return confirm('確定刪除此文件？')"
                                          class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="text-xs text-red-400 hover:text-red-600">✕ 刪除</button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-gray-400 text-center py-4">尚無專案文件</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-records-layout>
