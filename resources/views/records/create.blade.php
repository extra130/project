<x-records-layout title="新增紀錄">
    <x-slot name="header">
        <h2 class="text-xl font-semibold">新增紀錄</h2>
    </x-slot>

    <div class="bg-white rounded shadow-sm p-6 max-w-3xl">
        <form method="POST" action="{{ route('records.store') }}" enctype="multipart/form-data">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">

                {{-- Project --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">專案 *</label>
                    <select name="project_id" required
                            class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
                        <option value="">— 選擇專案 —</option>
                        @foreach($projects as $p)
                            <option value="{{ $p->id }}"
                                {{ (old('project_id', $selectedProject?->id) == $p->id) ? 'selected' : '' }}>
                                {{ $p->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('project_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                {{-- Module --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">模組</label>
                    <select name="module_id" class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
                        <option value="">— 不指定模組 —</option>
                        @foreach($modules as $m)
                            <option value="{{ $m->id }}" {{ old('module_id') == $m->id ? 'selected' : '' }}>
                                {{ $m->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Type --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">類型 *</label>
                    <select name="type" required class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
                        <option value="development" {{ old('type','development')==='development'?'selected':'' }}>開發紀錄</option>
                        <option value="test" {{ old('type')==='test'?'selected':'' }}>測試紀錄</option>
                        <option value="issue" {{ old('type')==='issue'?'selected':'' }}>問題紀錄</option>
                        <option value="note" {{ old('type')==='note'?'selected':'' }}>人工備註</option>
                    </select>
                </div>

                {{-- Source --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">來源 *</label>
                    <select name="source" required class="w-full border border-gray-300 rounded px-3 py-2 text-sm">
                        <option value="manual" {{ old('source','manual')==='manual'?'selected':'' }}>人工</option>
                        <option value="codex" {{ old('source')==='codex'?'selected':'' }}>Codex</option>
                        <option value="agent" {{ old('source')==='agent'?'selected':'' }}>Agent</option>
                        <option value="api" {{ old('source')==='api'?'selected':'' }}>API</option>
                    </select>
                </div>

                {{-- AI Tool --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">使用 AI</label>
                    <input type="text" name="ai_tool" value="{{ old('ai_tool') }}"
                           class="w-full border border-gray-300 rounded px-3 py-2 text-sm" placeholder="例如：ChatGPT, Gemini">
                </div>

                {{-- Git Branch --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Git Branch</label>
                    <input type="text" name="git_branch" value="{{ old('git_branch') }}"
                           class="w-full border border-gray-300 rounded px-3 py-2 text-sm" placeholder="develop">
                </div>

                {{-- Git Commit --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Git Commit</label>
                    <input type="text" name="git_commit" value="{{ old('git_commit') }}"
                           class="w-full border border-gray-300 rounded px-3 py-2 text-sm" placeholder="abc123">
                </div>

                {{-- Tags --}}
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">標籤 (Tags)</label>
                    <input type="text" name="tags_input" value="{{ old('tags_input') }}"
                           class="w-full border border-gray-300 rounded px-3 py-2 text-sm" placeholder="API, 資安, 資料庫優化 (多個請用逗號分隔)">
                </div>
            </div>

            {{-- Title --}}
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">標題 *</label>
                <input type="text" name="title" value="{{ old('title') }}"
                       class="w-full border border-gray-300 rounded px-3 py-2 text-sm" required maxlength="255">
                @error('title')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            {{-- Content --}}
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">內容 *</label>
                <textarea name="content" rows="12" required
                          class="w-full border border-gray-300 rounded px-3 py-2 text-sm font-mono"
                          placeholder="修改原因：&#10;...&#10;&#10;修改內容：&#10;...&#10;&#10;影響範圍：&#10;...">{{ old('content') }}</textarea>
                @error('content')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            {{-- Upload Files --}}
            <div class="mb-6 p-4 bg-gray-50 border border-gray-100 rounded">
                <label class="block text-sm font-medium text-gray-700 mb-2">附件上傳 (可選)</label>
                <input type="file" name="files[]" multiple
                       class="block w-full text-sm text-gray-600
                              file:mr-3 file:py-1.5 file:px-3
                              file:rounded file:border-0
                              file:text-sm file:font-medium
                              file:bg-indigo-50 file:text-indigo-700
                              hover:file:bg-indigo-100">
                <p class="text-xs text-gray-500 mt-2">可一次選擇多個檔案上傳（建立後也可在專屬頁面無限追加）</p>
                @error('files.*')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="flex items-center space-x-3">
                <button type="submit" class="bg-indigo-600 text-white px-5 py-2 rounded text-sm hover:bg-indigo-700">
                    建立
                </button>
                <a href="{{ route('records.index') }}" class="text-gray-500 text-sm hover:underline">取消</a>
            </div>
        </form>
    </div>
</x-records-layout>
