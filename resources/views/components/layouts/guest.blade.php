<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>TireSwap</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-bg text-slate-200 antialiased font-sans">
    {{ $slot }}
    
    <style> [x-cloak] { display: none !important; } </style>
</body>
</html>
