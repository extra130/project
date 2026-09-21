{{--
    records-layout.blade.php — 側邊欄版本
    左側固定側邊欄 + 右側主內容區
--}}
<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }} — {{ $title ?? '首頁' }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet"/>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="font-sans antialiased bg-gray-100 h-screen overflow-hidden">
<!-- (略) -->

<div class="flex h-screen">

    {{-- ===================== 側邊欄 ===================== --}}
    <aside class="w-56 bg-white border-r border-gray-200 flex flex-col flex-shrink-0 h-full overflow-y-auto">

        {{-- Logo --}}
        <div class="px-4 py-4 border-b border-gray-100">
            <a href="{{ route('records.index') }}" class="text-indigo-600 font-bold text-sm leading-tight">
                📋 專案紀錄系統
            </a>
        </div>

        {{-- 使用者資訊 --}}
        <div class="px-4 py-3 border-b border-gray-100 text-xs text-gray-500">
            <div class="font-medium text-gray-700 truncate">{{ Auth::user()->name }}</div>
            <div class="flex items-center space-x-1 mt-0.5">
                <span class="bg-gray-100 px-1.5 py-0.5 rounded text-gray-500">{{ Auth::user()->role }}</span>
            </div>
        </div>

        {{-- 導覽選單 --}}
        <nav class="flex-1 px-2 py-3 space-y-0.5 text-sm">

            {{-- 儀表板 --}}
            <div class="mb-1">
                <div class="px-2 py-1 text-xs font-semibold text-gray-400 uppercase tracking-wider">概覽</div>
                <a href="{{ route('dashboard') }}"
                   class="flex items-center px-2 py-1.5 rounded hover:bg-gray-100
                          {{ request()->routeIs('dashboard') ? 'bg-indigo-50 text-indigo-700 font-medium' : 'text-gray-700' }}">
                    <span class="mr-2">📊</span> 儀表板
                </a>
            </div>

            {{-- 紀錄 --}}
            <div class="mb-1">
                <div class="px-2 py-1 text-xs font-semibold text-gray-400 uppercase tracking-wider">紀錄</div>
                <a href="{{ route('records.index') }}"
                   class="flex items-center px-2 py-1.5 rounded hover:bg-gray-100
                          {{ request()->routeIs('records.index') ? 'bg-indigo-50 text-indigo-700 font-medium' : 'text-gray-700' }}">
                    <span class="mr-2">📄</span> 所有紀錄
                </a>
                <a href="{{ route('calendar.index') }}"
                   class="flex items-center px-2 py-1.5 rounded hover:bg-gray-100
                          {{ request()->routeIs('calendar.index') ? 'bg-indigo-50 text-indigo-700 font-medium' : 'text-gray-700' }}">
                    <span class="mr-2">📅</span> 行事曆
                </a>
                <a href="{{ route('daily-log.index') }}"
                   class="flex items-center px-2 py-1.5 rounded hover:bg-gray-100
                          {{ request()->routeIs('daily-log.index') ? 'bg-indigo-50 text-indigo-700 font-medium' : 'text-gray-700' }}">
                    <span class="mr-2">📝</span> 工作日誌
                </a>
                @if(Auth::user()->isEditor())
                    <a href="{{ route('records.create') }}"
                       class="flex items-center px-2 py-1.5 rounded hover:bg-gray-100
                              {{ request()->routeIs('records.create') ? 'bg-indigo-50 text-indigo-700 font-medium' : 'text-gray-700' }}">
                        <span class="mr-2">➕</span> 新增紀錄
                    </a>
                @endif
            </div>

            {{-- 專案 --}}
            <div class="mb-1">
                <div class="px-2 py-1 text-xs font-semibold text-gray-400 uppercase tracking-wider">專案</div>
                <a href="{{ route('projects.index') }}"
                   class="flex items-center px-2 py-1.5 rounded hover:bg-gray-100
                          {{ request()->routeIs('projects.index') ? 'bg-indigo-50 text-indigo-700 font-medium' : 'text-gray-700' }}">
                    <span class="mr-2">📁</span> 所有專案
                </a>
                @if(Auth::user()->isEditor())
                    <a href="{{ route('projects.create') }}"
                       class="flex items-center px-2 py-1.5 rounded hover:bg-gray-100
                              {{ request()->routeIs('projects.create') ? 'bg-indigo-50 text-indigo-700 font-medium' : 'text-gray-700' }}">
                        <span class="mr-2">➕</span> 新增專案
                    </a>
                @endif
            </div>

            {{-- 模組（動態：如果目前在某個專案底下就顯示） --}}
            @if(isset($currentProject) || request()->route('project'))
                @php $proj = $currentProject ?? request()->route('project'); @endphp
                @if($proj)
                <div class="mb-1">
                    <div class="px-2 py-1 text-xs font-semibold text-gray-400 uppercase tracking-wider">
                        模組 <span class="text-gray-300 font-normal">{{ Str::limit($proj->name, 12) }}</span>
                    </div>
                    <a href="{{ route('projects.modules.index', $proj) }}"
                       class="flex items-center px-2 py-1.5 rounded hover:bg-gray-100
                              {{ request()->routeIs('projects.modules.index') ? 'bg-indigo-50 text-indigo-700 font-medium' : 'text-gray-700' }}">
                        <span class="mr-2">🗂</span> 模組列表
                    </a>
                    @if(Auth::user()->isEditor())
                        <a href="{{ route('projects.modules.create', $proj) }}"
                           class="flex items-center px-2 py-1.5 rounded hover:bg-gray-100
                                  {{ request()->routeIs('projects.modules.create') ? 'bg-indigo-50 text-indigo-700 font-medium' : 'text-gray-700' }}">
                            <span class="mr-2">➕</span> 新增模組
                        </a>
                    @endif
                </div>
                @endif
            @endif

        </nav>

        {{-- 底部：設定 / 登出 --}}
        <div class="border-t border-gray-100 px-4 py-3 space-y-1">
            <a href="{{ route('profile.edit') }}" class="block text-xs text-gray-500 hover:text-indigo-600">⚙ 個人設定</a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-xs text-red-400 hover:text-red-600">↩ 登出</button>
            </form>
        </div>
    </aside>

    {{-- ===================== 主內容區 ===================== --}}
    <div class="flex-1 flex flex-col min-w-0 h-full overflow-auto">

        {{-- Page Header --}}
        @isset($header)
            <header class="bg-white border-b border-gray-200 px-6 py-4 flex-shrink-0">
                {{ $header }}
            </header>
        @endisset

        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="mx-6 mt-4 bg-green-50 border border-green-300 text-green-800 px-4 py-2 rounded text-sm flex-shrink-0">
                ✓ {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="mx-6 mt-4 bg-red-50 border border-red-300 text-red-800 px-4 py-2 rounded text-sm flex-shrink-0">
                ✗ {{ session('error') }}
            </div>
        @endif

        {{-- Main --}}
        <main class="flex-1 px-6 py-5">
            {{ $slot }}
        </main>
    </div>

</div>

@stack('scripts')
</body>
</html>
