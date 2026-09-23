{{--
    records-layout.blade.php (Modernized)
    Responsive layout with Alpine.js off-canvas sidebar, SVG Heroicons, and Toast notifications.
--}}
<!DOCTYPE html>
<html lang="zh-Hant" x-data="{ darkMode: localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches) }" x-init="$watch('darkMode', val => { localStorage.setItem('theme', val ? 'dark' : 'light'); if(val) document.documentElement.classList.add('dark'); else document.documentElement.classList.remove('dark'); })" :class="{ 'dark': darkMode }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }} - {{ $title ?? '首頁' }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet"/>
    <script>
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="font-sans antialiased bg-gray-50 dark:bg-gray-900 h-screen overflow-hidden text-gray-800 dark:text-gray-200" x-data="{ sidebarOpen: false }">

<div class="flex h-screen overflow-hidden bg-gray-50 dark:bg-gray-900">

    {{-- ===================== Mobile Sidebar Backdrop ===================== --}}
    <div x-show="sidebarOpen" 
         x-transition:enter="transition-opacity ease-linear duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-gray-900 bg-opacity-50 z-40 md:hidden"
         @click="sidebarOpen = false"
         style="display: none;"></div>

    {{-- ===================== Sidebar ===================== --}}
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
           class="fixed inset-y-0 left-0 z-50 w-64 bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 flex flex-col transition-transform duration-300 ease-in-out md:static md:translate-x-0 md:w-64 h-full shadow-lg md:shadow-none">

        {{-- Logo & Mobile Close --}}
        <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
            <a href="{{ route('records.index') }}" class="text-indigo-600 font-bold text-lg flex items-center space-x-2">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                <span>系統開發紀錄</span>
            </a>
            <button @click="sidebarOpen = false" class="md:hidden text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:text-gray-300">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        {{-- User Profile Snippet --}}
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 dark:bg-gray-900/50">
            <div class="font-medium text-gray-800 dark:text-gray-200 truncate">{{ Auth::user()->name }}</div>
            <div class="flex items-center mt-1">
                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-indigo-100 text-indigo-800">
                    {{ Auth::user()->role }}
                </span>
            </div>
        </div>

        {{-- Navigation Menu --}}
        <nav class="flex-1 px-4 py-4 space-y-6 overflow-y-auto">

            {{-- 系統總覽 --}}
            <div>
                <div class="px-2 mb-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">系統總覽</div>
                <a href="{{ route('dashboard') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md transition-colors {{ request()->routeIs('dashboard') ? 'bg-indigo-50 dark:bg-indigo-900/50 text-indigo-700' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:bg-gray-900 hover:text-gray-900 dark:text-gray-100' }}">
                    <svg class="mr-3 h-5 w-5 {{ request()->routeIs('dashboard') ? 'text-indigo-600' : 'text-gray-400 group-hover:text-gray-500 dark:text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                    總覽儀表板
                </a>
            </div>

            {{-- 開發紀錄 --}}
            <div>
                <div class="px-2 mb-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">開發紀錄</div>
                <div class="space-y-1">
                    <a href="{{ route('records.index') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md transition-colors {{ request()->routeIs('records.index') ? 'bg-indigo-50 dark:bg-indigo-900/50 text-indigo-700' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:bg-gray-900 hover:text-gray-900 dark:text-gray-100' }}">
                        <svg class="mr-3 h-5 w-5 {{ request()->routeIs('records.index') ? 'text-indigo-600' : 'text-gray-400 group-hover:text-gray-500 dark:text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        所有紀錄
                    </a>
                    <a href="{{ route('calendar.index') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md transition-colors {{ request()->routeIs('calendar.index') ? 'bg-indigo-50 dark:bg-indigo-900/50 text-indigo-700' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:bg-gray-900 hover:text-gray-900 dark:text-gray-100' }}">
                        <svg class="mr-3 h-5 w-5 {{ request()->routeIs('calendar.index') ? 'text-indigo-600' : 'text-gray-400 group-hover:text-gray-500 dark:text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        月曆視角
                    </a>
                    <a href="{{ route('daily-log.index') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md transition-colors {{ request()->routeIs('daily-log.index') ? 'bg-indigo-50 dark:bg-indigo-900/50 text-indigo-700' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:bg-gray-900 hover:text-gray-900 dark:text-gray-100' }}">
                        <svg class="mr-3 h-5 w-5 {{ request()->routeIs('daily-log.index') ? 'text-indigo-600' : 'text-gray-400 group-hover:text-gray-500 dark:text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                        工作日誌
                    </a>
                    @if(Auth::user()->isEditor())
                        <a href="{{ route('records.create') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md transition-colors {{ request()->routeIs('records.create') ? 'bg-indigo-50 dark:bg-indigo-900/50 text-indigo-700' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:bg-gray-900 hover:text-gray-900 dark:text-gray-100' }}">
                            <svg class="mr-3 h-5 w-5 {{ request()->routeIs('records.create') ? 'text-indigo-600' : 'text-gray-400 group-hover:text-gray-500 dark:text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            新增紀錄
                        </a>
                    @endif
                </div>
            </div>

            {{-- 專案管理 --}}
            <div>
                <div class="px-2 mb-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">專案管理</div>
                <div class="space-y-1">
                    <a href="{{ route('projects.index') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md transition-colors {{ request()->routeIs('projects.index') ? 'bg-indigo-50 dark:bg-indigo-900/50 text-indigo-700' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:bg-gray-900 hover:text-gray-900 dark:text-gray-100' }}">
                        <svg class="mr-3 h-5 w-5 {{ request()->routeIs('projects.index') ? 'text-indigo-600' : 'text-gray-400 group-hover:text-gray-500 dark:text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path></svg>
                        所有專案
                    </a>
                    @if(Auth::user()->isEditor())
                        <a href="{{ route('projects.create') }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md transition-colors {{ request()->routeIs('projects.create') ? 'bg-indigo-50 dark:bg-indigo-900/50 text-indigo-700' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:bg-gray-900 hover:text-gray-900 dark:text-gray-100' }}">
                            <svg class="mr-3 h-5 w-5 {{ request()->routeIs('projects.create') ? 'text-indigo-600' : 'text-gray-400 group-hover:text-gray-500 dark:text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            新增專案
                        </a>
                    @endif
                </div>
            </div>

            {{-- 當前專案模組 (若在專案或模組頁面下) --}}
            @if(isset($currentProject) || request()->route('project'))
                @php $proj = $currentProject ?? request()->route('project'); @endphp
                @if($proj)
                <div>
                    <div class="px-2 mb-2 text-xs font-semibold text-indigo-500 uppercase tracking-wider flex items-center">
                        <span class="truncate w-full" title="{{ $proj->name }}">專案：{{ $proj->name }}</span>
                    </div>
                    <div class="space-y-1">
                        <a href="{{ route('projects.modules.index', $proj) }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md transition-colors {{ request()->routeIs('projects.modules.index') ? 'bg-indigo-50 dark:bg-indigo-900/50 text-indigo-700' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:bg-gray-900 hover:text-gray-900 dark:text-gray-100' }}">
                            <svg class="mr-3 h-5 w-5 {{ request()->routeIs('projects.modules.index') ? 'text-indigo-600' : 'text-gray-400 group-hover:text-gray-500 dark:text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                            模組列表
                        </a>
                        @if(Auth::user()->isEditor())
                            <a href="{{ route('projects.modules.create', $proj) }}" class="group flex items-center px-2 py-2 text-sm font-medium rounded-md transition-colors {{ request()->routeIs('projects.modules.create') ? 'bg-indigo-50 dark:bg-indigo-900/50 text-indigo-700' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:bg-gray-900 hover:text-gray-900 dark:text-gray-100' }}">
                                <svg class="mr-3 h-5 w-5 {{ request()->routeIs('projects.modules.create') ? 'text-indigo-600' : 'text-gray-400 group-hover:text-gray-500 dark:text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                新增模組
                            </a>
                        @endif
                    </div>
                </div>
                @endif
            @endif

        </nav>

        {{-- Footer settings --}}
        <div class="p-4 border-t border-gray-200 dark:border-gray-700 space-y-2">
            <button @click="darkMode = !darkMode" class="w-full group flex items-center px-2 py-2 text-sm font-medium text-gray-600 dark:text-gray-400 rounded-md hover:text-indigo-600 hover:bg-indigo-50 dark:bg-indigo-900/50 transition-colors">
                <svg x-show="!darkMode" class="mr-3 h-5 w-5 text-gray-400 group-hover:text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
                <svg x-show="darkMode" x-cloak class="mr-3 h-5 w-5 text-gray-400 group-hover:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                <span x-text="darkMode ? '切換亮色模式' : '切換暗色模式'"></span>
            </button>
            <a href="{{ route('profile.edit') }}" class="group flex items-center px-2 py-2 text-sm font-medium text-gray-600 dark:text-gray-400 rounded-md hover:text-indigo-600 hover:bg-indigo-50 dark:bg-indigo-900/50 transition-colors">
                <svg class="mr-3 h-5 w-5 text-gray-400 group-hover:text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                個人設定
            </a>
            <form method="POST" action="{{ route('logout') }}" class="w-full">
                @csrf
                <button type="submit" class="w-full group flex items-center px-2 py-2 text-sm font-medium text-gray-600 dark:text-gray-400 rounded-md hover:text-red-600 hover:bg-red-50 dark:bg-red-900/50 transition-colors">
                    <svg class="mr-3 h-5 w-5 text-gray-400 group-hover:text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                    登出系統
                </button>
            </form>
        </div>
    </aside>

    {{-- ===================== Main Content Area ===================== --}}
    <div class="flex-1 flex flex-col min-w-0 h-full overflow-hidden bg-gray-50 dark:bg-gray-900/50">

        {{-- Mobile Top Navbar --}}
        <div class="md:hidden flex items-center justify-between bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 px-4 py-3 shadow-sm">
            <div class="flex items-center space-x-3">
                <button @click="sidebarOpen = true" class="text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500 rounded-md p-1">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </button>
                <span class="font-bold text-gray-800 dark:text-gray-200 text-lg">{{ config('app.name') }}</span>
            </div>
            <div class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold">
                {{ mb_substr(Auth::user()->name, 0, 1) }}
            </div>
        </div>

        {{-- Page Header --}}
        @isset($header)
            <header class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-100 px-6 py-5 flex-shrink-0">
                {{ $header }}
            </header>
        @endisset

        {{-- Main Scrollable Content --}}
        <main class="flex-1 overflow-y-auto p-4 md:p-6 lg:p-8">
            {{ $slot }}
        </main>
    </div>

</div>

{{-- Global Toast Notification System (AlpineJS) --}}
<div x-data="{ toasts: [] }" 
     @flash-toast.window="
        let newToast = { id: Date.now(), type: $event.detail.type, message: $event.detail.message };
        toasts.push(newToast);
        setTimeout(() => { toasts = toasts.filter(t => t.id !== newToast.id) }, 3500);
     "
     class="fixed top-5 right-5 z-50 flex flex-col gap-3 max-w-sm w-full pointer-events-none">
    
    <template x-for="toast in toasts" :key="toast.id">
        <div x-show="true"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-x-full"
             x-transition:enter-end="opacity-100 translate-x-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-x-0"
             x-transition:leave-end="opacity-0 translate-x-full"
             class="pointer-events-auto shadow-lg rounded-lg border p-4 flex items-start space-x-3"
             :class="{
                'bg-green-50 dark:bg-green-900/50 border-green-200': toast.type === 'success',
                'bg-red-50 dark:bg-red-900/50 border-red-200': toast.type === 'error'
             }">
             
            {{-- Icon --}}
            <div class="flex-shrink-0">
                <template x-if="toast.type === 'success'">
                    <svg class="h-6 w-6 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </template>
                <template x-if="toast.type === 'error'">
                    <svg class="h-6 w-6 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </template>
            </div>
            
            {{-- Message --}}
            <div class="flex-1 pt-0.5">
                <p class="text-sm font-medium" 
                   :class="{
                       'text-green-800': toast.type === 'success',
                       'text-red-800': toast.type === 'error'
                   }"
                   x-text="toast.message"></p>
            </div>
            
            {{-- Close button --}}
            <button @click="toasts = toasts.filter(t => t.id !== toast.id)" class="text-gray-400 hover:text-gray-500 dark:text-gray-400 focus:outline-none">
                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
            </button>
        </div>
    </template>
</div>

{{-- Trigger flash messages if present in session --}}
@if(session('success'))
    <script>
        document.addEventListener('alpine:init', () => {
            setTimeout(() => {
                window.dispatchEvent(new CustomEvent('flash-toast', { detail: { type: 'success', message: '{{ session('success') }}' } }));
            }, 100);
        });
    </script>
@endif
@if(session('error'))
    <script>
        document.addEventListener('alpine:init', () => {
            setTimeout(() => {
                window.dispatchEvent(new CustomEvent('flash-toast', { detail: { type: 'error', message: '{{ session('error') }}' } }));
            }, 100);
        });
    </script>
@endif

@stack('scripts')
</body>
</html>




