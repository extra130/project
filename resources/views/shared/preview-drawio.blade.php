<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>預覽 - {{ $fileName }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 dark:bg-gray-900 min-h-screen flex flex-col">
    <div class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 px-6 py-4 flex justify-between items-center shadow-sm">
        <h1 class="text-xl font-bold text-gray-800 dark:text-gray-200 flex items-center">
            <span class="mr-2">📊</span> {{ $fileName }}
        </h1>
        <button onclick="window.close()" class="text-sm bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 px-4 py-2 rounded hover:bg-gray-200 transition">關閉預覽</button>
    </div>
    
    <div class="flex-grow p-4 md:p-8 flex items-center justify-center">
        <div class="w-full h-full bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden" style="min-height: 80vh;">
            @php
                $mxgraphData = json_encode([
                    'highlight' => '#4f46e5',
                    'nav' => true,
                    'resize' => true,
                    'toolbar' => 'zoom layers lightbox',
                    'xml' => $content
                ]);
            @endphp
            <div class="mxgraph w-full h-full" style="max-width:100%; border:none;" data-mxgraph="{{ $mxgraphData }}"></div>
        </div>
    </div>
    
    <!-- Drawio static viewer -->
    <script type="text/javascript" src="https://viewer.diagrams.net/js/viewer-static.min.js"></script>
</body>
</html>

