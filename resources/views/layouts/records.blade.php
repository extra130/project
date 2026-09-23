<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }} - {{ $title ?? '首頁' }}</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-100 dark:bg-gray-700">

<div class="min-h-screen">

    {{-- ========= Navbar ========= --}}
    <nav class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between h-14">

            <div class="flex items-center space-x-6">
                <a href="{{ route('records.index') }}" class="font-bold text-indigo-600 text-base">
                    📋 專案紀錄系統
                </a>
                <a href="{{ route('projects.index') }}"
                   class="text-sm text-gray-600 dark:text-gray-400 hover:text-indigo-600">專案</a>
                <a href="{{ route('records.index') }}"
                   class="text-sm text-gray-600 dark:text-gray-400 hover:text-indigo-600">紀錄</a>
                @if(Auth::user()->isEditor())
                    <a href="{{ route('records.create') }}"
                       class="text-sm bg-indigo-600 text-white px-3 py-1 rounded hover:bg-indigo-700">
                        + 新增紀錄
                    </a>
                @endif
            </div>

            <div class="flex items-center space-x-4 text-sm text-gray-600 dark:text-gray-400">
                <span>{{ Auth::user()->name }}</span>
                <span class="text-xs bg-gray-100 dark:bg-gray-700 px-2 py-0.5 rounded">{{ Auth::user()->role }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-red-500 hover:underline text-xs">登出</button>
                </form>
            </div>
        </div>
    </nav>

    {{-- ========= Page Heading ========= --}}
    @isset($header)
        <header class="bg-white dark:bg-gray-800 shadow-sm">
            <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                {{ $header }}
            </div>
        </header>
    @endisset

    {{-- ========= Flash Messages ========= --}}
    @if(session('success'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <div class="bg-green-50 dark:bg-green-900/50 border border-green-300 text-green-800 px-4 py-2 rounded text-sm">
                ✓ {{ session('success') }}
            </div>
        </div>
    @endif
    @if(session('error'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <div class="bg-red-50 dark:bg-red-900/50 border border-red-300 text-red-800 px-4 py-2 rounded text-sm">
                ✗ {{ session('error') }}
            </div>
        </div>
    @endif

    {{-- ========= Main Content ========= --}}
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        {{ $slot }}
    </main>

</div>

</body>
</html>

