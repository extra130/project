<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>?汗 - {{ $projectFile->original_name }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/styles/github-dark.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/highlight.min.js"></script>
</head>
<body class="bg-gray-50 dark:bg-gray-900 min-h-screen">
    <div class="max-w-4xl mx-auto py-8 px-4">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-8">
            <div class="border-b border-gray-100 pb-4 mb-6 flex justify-between items-center">
                <h1 class="text-2xl font-bold text-gray-800 dark:text-gray-200">{{ $projectFile->original_name }}</h1>
                <a href="{{ route('project-files.download', $projectFile) }}" class="text-sm bg-indigo-50 dark:bg-indigo-900/50 text-indigo-700 px-3 py-1.5 rounded hover:bg-indigo-100">漎?銝?瑼?</a>
            </div>
            
            <div class="prose max-w-none prose-indigo prose-pre:bg-gray-800 prose-pre:text-gray-100">
                {!! Str::markdown($content, ['html_input' => 'escape']) !!}
            </div>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', (event) => {
            document.querySelectorAll('pre code').forEach((el) => {
                hljs.highlightElement(el);
            });
        });
    </script>
</body>
</html>


