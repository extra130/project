<x-records-layout :title="'蝺刻摩蝝??' . $record->title">
    <x-slot name="header">
        <h2 class="text-xl font-semibold">蝺刻摩蝝??/h2>
    </x-slot>

    <div class="bg-white dark:bg-gray-800 rounded shadow-sm p-6 max-w-3xl">
        <form method="POST" action="{{ route('records.update', $record) }}">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">撠? *</label>
                    <select name="project_id" required class="w-full border border-gray-300 dark:border-gray-600 rounded px-3 py-2 text-sm">
                        @foreach($projects as $p)
                            <option value="{{ $p->id }}" {{ old('project_id',$record->project_id)==$p->id?'selected':'' }}>
                                {{ $p->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">璅∠?</label>
                    <select name="module_id" class="w-full border border-gray-300 dark:border-gray-600 rounded px-3 py-2 text-sm">
                        <option value="">??銝?摰???/option>
                        @foreach($modules as $m)
                            <option value="{{ $m->id }}" {{ old('module_id',$record->module_id)==$m->id?'selected':'' }}>
                                {{ $m->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">憿? *</label>
                    <select name="type" required class="w-full border border-gray-300 dark:border-gray-600 rounded px-3 py-2 text-sm">
                        @foreach(['development'=>'?蝝??,'test'=>'皜祈岫蝝??,'issue'=>'??蝝??,'note'=>'鈭箏極?酉'] as $val=>$label)
                            <option value="{{ $val }}" {{ old('type',$record->type)===$val?'selected':'' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                {{-- Source --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">靘? *</label>
                    <select name="source" required class="w-full border border-gray-300 dark:border-gray-600 rounded px-3 py-2 text-sm">
                        <option value="manual" {{ old('source',$record->source)==='manual'?'selected':'' }}>鈭箏極</option>
                        <option value="codex" {{ old('source',$record->source)==='codex'?'selected':'' }}>Codex</option>
                        <option value="agent" {{ old('source',$record->source)==='agent'?'selected':'' }}>Agent</option>
                        <option value="api" {{ old('source',$record->source)==='api'?'selected':'' }}>API</option>
                    </select>
                </div>

                {{-- AI Tool --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">雿輻 AI</label>
                    <input type="text" name="ai_tool" value="{{ old('ai_tool', $record->ai_tool) }}"
                           class="w-full border border-gray-300 dark:border-gray-600 rounded px-3 py-2 text-sm" placeholder="靘?嚗hatGPT, Gemini">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Git Branch</label>
                    <input type="text" name="git_branch" value="{{ old('git_branch',$record->git_branch) }}"
                           class="w-full border border-gray-300 dark:border-gray-600 rounded px-3 py-2 text-sm">
                </div>
                {{-- Git Commit --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Git Commit</label>
                    <input type="text" name="git_commit" value="{{ old('git_commit', $record->git_commit) }}"
                           class="w-full border border-gray-300 dark:border-gray-600 rounded px-3 py-2 text-sm" placeholder="abc123">
                </div>

                {{-- Tags --}}
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">璅惜 (Tags)</label>
                    <input type="text" name="tags_input" value="{{ old('tags_input', $tagsString ?? '') }}"
                           class="w-full border border-gray-300 dark:border-gray-600 rounded px-3 py-2 text-sm" placeholder="API, 鞈?, 鞈?摨怠??(憭??券???)">
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">璅? *</label>
                <input type="text" name="title" value="{{ old('title',$record->title) }}"
                       class="w-full border border-gray-300 dark:border-gray-600 rounded px-3 py-2 text-sm" required maxlength="255">
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">?批捆 *</label>
                <textarea name="content" rows="12" required
                          class="w-full border border-gray-300 dark:border-gray-600 rounded px-3 py-2 text-sm font-mono">{{ old('content',$record->content) }}</textarea>
            </div>

            <div class="flex items-center space-x-3">
                <button type="submit" class="bg-indigo-600 text-white px-5 py-2 rounded text-sm hover:bg-indigo-700">
                    ?脣?
                </button>
                <a href="{{ route('records.show', $record) }}" class="text-gray-500 dark:text-gray-400 text-sm hover:underline">??</a>
            </div>
        </form>
    </div>
</x-records-layout>


