<x-records-layout title="?啣?蝝??>
    <x-slot name="header">
        <h2 class="text-xl font-semibold">?啣?蝝??/h2>
    </x-slot>

    <div class="bg-white dark:bg-gray-800 rounded shadow-sm p-6 max-w-3xl">
        <form method="POST" action="{{ route('records.store') }}" enctype="multipart/form-data">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">

                {{-- Project --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">撠? *</label>
                    <select name="project_id" required
                            class="w-full border border-gray-300 dark:border-gray-600 rounded px-3 py-2 text-sm">
                        <option value="">???豢?撠? ??/option>
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
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">璅∠?</label>
                    <select name="module_id" class="w-full border border-gray-300 dark:border-gray-600 rounded px-3 py-2 text-sm">
                        <option value="">??銝?摰芋蝯???/option>
                        @foreach($modules as $m)
                            <option value="{{ $m->id }}" {{ old('module_id') == $m->id ? 'selected' : '' }}>
                                {{ $m->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Type --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">憿? *</label>
                    <select name="type" required class="w-full border border-gray-300 dark:border-gray-600 rounded px-3 py-2 text-sm">
                        <option value="development" {{ old('type','development')==='development'?'selected':'' }}>?蝝??/option>
                        <option value="test" {{ old('type')==='test'?'selected':'' }}>皜祈岫蝝??/option>
                        <option value="issue" {{ old('type')==='issue'?'selected':'' }}>??蝝??/option>
                        <option value="note" {{ old('type')==='note'?'selected':'' }}>鈭箏極?酉</option>
                    </select>
                </div>

                {{-- Source --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">靘? *</label>
                    <select name="source" required class="w-full border border-gray-300 dark:border-gray-600 rounded px-3 py-2 text-sm">
                        <option value="manual" {{ old('source','manual')==='manual'?'selected':'' }}>鈭箏極</option>
                        <option value="codex" {{ old('source')==='codex'?'selected':'' }}>Codex</option>
                        <option value="agent" {{ old('source')==='agent'?'selected':'' }}>Agent</option>
                        <option value="api" {{ old('source')==='api'?'selected':'' }}>API</option>
                    </select>
                </div>

                {{-- AI Tool --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">雿輻 AI</label>
                    <input type="text" name="ai_tool" value="{{ old('ai_tool') }}"
                           class="w-full border border-gray-300 dark:border-gray-600 rounded px-3 py-2 text-sm" placeholder="靘?嚗hatGPT, Gemini">
                </div>

                {{-- Git Branch --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Git Branch</label>
                    <input type="text" name="git_branch" value="{{ old('git_branch') }}"
                           class="w-full border border-gray-300 dark:border-gray-600 rounded px-3 py-2 text-sm" placeholder="develop">
                </div>

                {{-- Git Commit --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Git Commit</label>
                    <input type="text" name="git_commit" value="{{ old('git_commit') }}"
                           class="w-full border border-gray-300 dark:border-gray-600 rounded px-3 py-2 text-sm" placeholder="abc123">
                </div>

                {{-- Tags --}}
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">璅惜 (Tags)</label>
                    <input type="text" name="tags_input" value="{{ old('tags_input') }}"
                           class="w-full border border-gray-300 dark:border-gray-600 rounded px-3 py-2 text-sm" placeholder="API, 鞈?, 鞈?摨怠??(憭??券???)">
                </div>
            </div>

            {{-- Title --}}
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">璅? *</label>
                <input type="text" name="title" value="{{ old('title') }}"
                       class="w-full border border-gray-300 dark:border-gray-600 rounded px-3 py-2 text-sm" required maxlength="255">
                @error('title')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            {{-- Content --}}
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">?批捆 *</label>
                <textarea name="content" rows="12" required
                          class="w-full border border-gray-300 dark:border-gray-600 rounded px-3 py-2 text-sm font-mono"
                          placeholder="靽格??嚗?#10;...&#10;&#10;靽格?批捆嚗?#10;...&#10;&#10;敶梢蝭?嚗?#10;...">{{ old('content') }}</textarea>
                @error('content')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            {{-- Upload Files --}}
            <div class="mb-6 p-4 bg-gray-50 dark:bg-gray-900 border border-gray-100 rounded">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">?辣銝 (?舫)</label>
                <input type="file" name="files[]" multiple
                       class="block w-full text-sm text-gray-600 dark:text-gray-400
                              file:mr-3 file:py-1.5 file:px-3
                              file:rounded file:border-0
                              file:text-sm file:font-medium
                              file:bg-indigo-50 dark:bg-indigo-900/50 file:text-indigo-700
                              hover:file:bg-indigo-100">
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">?臭?甈⊿????獢??喉?撱箇?敺??臬撠惇??⊿?餈賢?嚗?/p>
                @error('files.*')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="flex items-center space-x-3">
                <button type="submit" class="bg-indigo-600 text-white px-5 py-2 rounded text-sm hover:bg-indigo-700">
                    撱箇?
                </button>
                <a href="{{ route('records.index') }}" class="text-gray-500 dark:text-gray-400 text-sm hover:underline">??</a>
            </div>
        </form>
    </div>
</x-records-layout>


