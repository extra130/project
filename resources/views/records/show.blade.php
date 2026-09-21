{{-- Record show - spec §17 UI --}}
<x-records-layout :title="$record->title">
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold">{{ $record->title }}</h2>
                <div class="flex items-center space-x-2 mt-1 text-sm text-gray-500">
                    @php
                        $typeLabels = [
                            'development' => '開發紀錄',
                            'test'        => '測試紀錄',
                            'issue'       => '問題紀錄',
                            'note'        => '人工備註',
                        ];
                        $sourceLabels = [
                            'manual' => '人工',
                            'codex'  => 'Codex',
                            'agent'  => 'Agent',
                            'api'    => 'API',
                        ];
                    @endphp
                    <span>{{ $typeLabels[$record->type] ?? $record->type }}</span>
                    <span>·</span>
                    <span>{{ $sourceLabels[$record->source] ?? $record->source }}</span>
                    <span>·</span>
                    <span>{{ $record->created_at->format('Y/m/d H:i') }}</span>
                </div>
            </div>
            @if(Auth::user()->isEditor())
                <a href="{{ route('records.edit', $record) }}"
                   class="border border-gray-300 px-3 py-1.5 rounded text-sm hover:bg-gray-50">
                    編輯
                </a>
            @endif
        </div>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ========== Main content ========== --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Metadata --}}
            <div class="bg-white rounded shadow-sm p-4 text-sm grid grid-cols-2 gap-3">
                <div>
                    <span class="text-gray-400">專案：</span>
                    <a href="{{ route('projects.show', $record->project) }}"
                       class="text-indigo-600 hover:underline">{{ $record->project->name }}</a>
                </div>
                <div>
                    <span class="text-gray-400">模組：</span>
                    {{ $record->module?->name ?? '—' }}
                </div>
                @if($record->git_branch)
                    <div>
                        <span class="text-gray-400">Branch：</span>
                        <code class="bg-gray-100 px-1 py-0.5 rounded text-xs">{{ $record->git_branch }}</code>
                    </div>
                @endif
                @if($record->git_commit)
                    <div>
                        <span class="text-gray-400">Commit：</span>
                        <code class="bg-gray-100 px-1 py-0.5 rounded text-xs">{{ $record->git_commit }}</code>
                    </div>
                @endif
            </div>

            {{-- Content --}}
            <div class="bg-white rounded shadow-sm p-4">
                <h3 class="text-sm font-medium text-gray-500 mb-3">內容</h3>
                <div class="whitespace-pre-wrap text-sm text-gray-700 leading-relaxed">{{ $record->content }}</div>
            </div>
        </div>

        {{-- ========== Attachments sidebar (spec §17) ========== --}}
        <div class="space-y-4">
            <div class="bg-white rounded shadow-sm p-4">

                <h3 class="text-sm font-medium text-gray-700 mb-3">
                    附件（{{ $record->files->count() }}）
                </h3>

                {{-- ── 上傳區：常顯示，Editor 以上可見 ── --}}
                @if(Auth::user()->isEditor())
                    <form method="POST"
                          action="{{ route('record-files.store', $record) }}"
                          enctype="multipart/form-data"
                          class="mb-4">
                        @csrf
                        <label class="block text-xs font-medium text-gray-500 mb-1">
                            新增附件（可多選）
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

                {{-- ── 已上傳檔案列表 ── --}}
                <div class="space-y-3">
                    @forelse($record->files->sortBy('sort_order') as $file)
                        <div class="border border-gray-100 rounded p-3 text-sm">
                            <div class="font-medium text-gray-800 truncate" title="{{ $file->display_name }}">
                                {{ $file->display_name }}
                            </div>
                            <div class="text-xs text-gray-400 mt-0.5 truncate" title="{{ $file->original_name }}">
                                {{ $file->original_name }}
                            </div>
                            @if($file->note)
                                <div class="text-xs text-gray-500 mt-1 italic">{{ $file->note }}</div>
                            @endif
                            <div class="text-xs text-gray-400 mt-1">
                                {{ strtoupper($file->extension) }}
                                · {{ number_format($file->file_size / 1024, 1) }} KB
                            </div>

                            <div class="flex items-center space-x-3 mt-2 pt-2 border-t border-gray-50">
                                <a href="{{ route('record-files.download', $file) }}"
                                   class="text-xs text-indigo-600 hover:underline">⬇ 下載</a>
                                @if(Auth::user()->isEditor())
                                    <a href="{{ route('record-files.edit', $file) }}"
                                       class="text-xs text-gray-400 hover:text-indigo-600">✎ 修改</a>
                                    <form method="POST" action="{{ route('record-files.destroy', $file) }}"
                                          onsubmit="return confirm('確定刪除此附件？')"
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
                        <p class="text-xs text-gray-400 text-center py-4">尚無附件</p>
                    @endforelse
                </div>
            </div>

            <div>
                <a href="{{ route('records.index') }}" class="text-sm text-gray-400 hover:underline">← 返回列表</a>
            </div>
        </div>
    </div>
</x-records-layout>
