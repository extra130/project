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

                <div id="sortable-modules" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @forelse($project->modules as $module)
                            <div data-id="{{ $module->id }}" class="bg-white rounded-lg shadow-sm p-5 border-l-4 transition-all duration-200 hover:-translate-y-0.5 hover:shadow-md relative
                                {{ $module->status === 'active' ? 'border-indigo-400' : 'border-gray-300' }}">
                                @if(Auth::user()->isEditor())
                                    <div class="absolute top-3 right-3 cursor-move text-gray-300 hover:text-gray-500">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"></path></svg>
                                    </div>
                                @endif
                                <a href="{{ route('modules.show', $module) }}" class="font-medium text-lg text-indigo-700 hover:text-indigo-900 flex items-center group pr-6">
                                    <svg class="w-5 h-5 mr-2 text-indigo-500 group-hover:text-indigo-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                    {{ $module->name }}
                                </a>
                                <div class="text-xs text-gray-500 mt-2 truncate">{{ $module->description ?? '無描述' }}</div>
                                <div class="mt-4 flex items-center space-x-3 text-xs">
                                    <a href="{{ route('modules.show', $module) }}"
                                       class="text-indigo-600 hover:text-indigo-800 font-medium">附件總覽</a>
                                    <span class="text-gray-300">|</span>
                                    <a href="{{ route('records.index', ['project_id' => $project->id, 'module_id' => $module->id]) }}"
                                       class="text-indigo-600 hover:text-indigo-800 font-medium">所有紀錄</a>
                                    @if(Auth::user()->isEditor())
                                        <span class="text-gray-300">|</span>
                                        <a href="{{ route('modules.edit', $module) }}" class="text-gray-400 hover:text-gray-600">編輯</a>
                                    @endif
                                </div>
                            </div>
                    @empty
                        <div class="col-span-2 text-center text-gray-400 py-12 bg-white rounded shadow-sm flex flex-col items-center">
                            <svg class="w-10 h-10 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                            尚無模組
                        </div>
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
                                  @if(in_array(strtolower($file->extension), ['pdf', 'html', 'htm', 'md', 'markdown', 'jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'drawio']))
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
    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const el = document.getElementById('sortable-modules');
            if (el) {
                new Sortable(el, {
                    animation: 150,
                    handle: '.cursor-move',
                    ghostClass: 'bg-indigo-50',
                    onEnd: function (evt) {
                        const order = Array.from(el.children).map(card => card.dataset.id).filter(id => id);
                        
                        fetch('{{ route('modules.reorder') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({ order: order })
                        }).then(response => {
                            if(response.ok) {
                                window.dispatchEvent(new CustomEvent('flash-toast', { detail: { type: 'success', message: '模組排序已更新' } }));
                            } else {
                                window.dispatchEvent(new CustomEvent('flash-toast', { detail: { type: 'error', message: '權限不足或更新失敗' } }));
                            }
                        });
                    },
                });
            }
        });
    </script>
    @endpush
</x-records-layout>
