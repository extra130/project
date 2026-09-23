<x-records-layout title="?啣?撠?">
    <x-slot name="header">
        <h2 class="text-xl font-semibold">?啣?撠?</h2>
    </x-slot>

    <div class="bg-white dark:bg-gray-800 rounded shadow-sm p-6 max-w-2xl">
        <form method="POST" action="{{ route('projects.store') }}">
            @csrf

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">撠??迂 *</label>
                <input type="text" name="name" value="{{ old('name') }}"
                       class="w-full border border-gray-300 dark:border-gray-600 rounded px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500"
                       required maxlength="150">
                @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">隤芣?</label>
                <textarea name="description" rows="3"
                          class="w-full border border-gray-300 dark:border-gray-600 rounded px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500">{{ old('description') }}</textarea>
                @error('description')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">撠?隞?”??/label>
                <div class="flex items-center space-x-2">
                    <input type="color" name="color" value="{{ old('color', '#4f46e5') }}"
                           class="h-8 w-8 border border-gray-300 dark:border-gray-600 rounded cursor-pointer p-0">
                    <span class="text-xs text-gray-500 dark:text-gray-400">?豢?銝?隞?”甇文?獢?憿</span>
                </div>
                @error('color')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">???/label>
                <select name="status" class="border border-gray-300 dark:border-gray-600 rounded px-3 py-2 text-sm">
                    <option value="active" {{ old('status','active')==='active'?'selected':'' }}>?</option>
                    <option value="archived" {{ old('status')==='archived'?'selected':'' }}>撠?</option>
                </select>
            </div>

            <div class="flex items-center space-x-3">
                <button type="submit"
                        class="bg-indigo-600 text-white px-5 py-2 rounded text-sm hover:bg-indigo-700">
                    撱箇?
                </button>
                <a href="{{ route('projects.index') }}" class="text-gray-500 dark:text-gray-400 text-sm hover:underline">??</a>
            </div>
        </form>
    </div>
</x-records-layout>


