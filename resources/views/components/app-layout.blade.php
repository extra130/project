{{--
    Anonymous component: x-app-layout
    ?冽 Breeze Profile ?嚗dit / password / delete嚗?    瘝輻?祉頂蝯梁? records-layout 憭???--}}
<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }} - ?犖閮剖?</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet"/>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-100 dark:bg-gray-700">
<div class="min-h-screen">

    {{-- Navbar嚗? records-layout 銝?湛?--}}
    <nav class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between h-14">
            <div class="flex items-center space-x-6">
                <a href="{{ route('records.index') }}" class="font-bold text-indigo-600 text-base">
                    ?? 撠?蝝?頂蝯?                </a>
                <a href="{{ route('projects.index') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:text-indigo-600">撠?</a>
                <a href="{{ route('records.index') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:text-indigo-600">蝝??/a>
            </div>
            <div class="flex items-center space-x-4 text-sm text-gray-600 dark:text-gray-400">
                <span>{{ Auth::user()->name }}</span>
                <span class="text-xs bg-gray-100 dark:bg-gray-700 px-2 py-0.5 rounded">{{ Auth::user()->role }}</span>
                <a href="{{ route('profile.edit') }}" class="text-indigo-600 text-xs font-medium">閮剖?</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-red-500 hover:underline text-xs">?餃</button>
                </form>
            </div>
        </div>
    </nav>

    {{-- Page Heading --}}
    @isset($header)
        <header class="bg-white dark:bg-gray-800 shadow-sm">
            <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                {{ $header }}
            </div>
        </header>
    @endisset

    {{-- Flash --}}
    @if(session('success'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <div class="bg-green-50 dark:bg-green-900/50 border border-green-300 text-green-800 px-4 py-2 rounded text-sm">
                ??{{ session('success') }}
            </div>
        </div>
    @endif

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        {{ $slot }}
    </main>
</div>
</body>
</html>


