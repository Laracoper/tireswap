<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>TireSwap — Сервис обмена колес</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#020617] text-white antialiased font-sans">
    <nav class="p-8 flex justify-between items-center max-w-7xl mx-auto">
        <div class="text-3xl font-black tracking-tighter text-blue-500 italic">TIRESWAP</div>
        <div class="space-x-6 flex items-center">
            <a href="/wheels" class="font-bold text-slate-400 hover:text-white transition">Маркетплейс</a>
            @auth
                <a href="/dashboard" class="bg-blue-600 px-6 py-3 rounded-2xl font-bold shadow-lg shadow-blue-600/20">В кабинет</a>
            @else
                <a href="/login" class="font-bold">Войти</a>
                <a href="/register" class="bg-blue-600 px-6 py-3 rounded-2xl font-bold shadow-lg shadow-blue-600/20">Начать</a>
            @endauth
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-8 pt-20 pb-32 text-center md:text-left flex flex-col md:flex-row items-center gap-16">
        <div class="flex-1 space-y-8">
            <h1 class="text-6xl md:text-8xl font-black leading-none tracking-tight">
                МЕНЯЙСЯ <br> <span class="text-blue-500 underline decoration-blue-500/30">КОЛЕСАМИ</span>
            </h1>
            <p class="text-xl text-slate-500 max-w-xl">
                Пробил одно колесо? Хочешь другой радиус? TireSwap — умная платформа для обмена и поиска автомобильных шин и дисков.
            </p>
            <div class="flex gap-4 justify-center md:justify-start">
                <a href="/register" class="px-8 py-5 bg-blue-600 rounded-3xl font-black text-xl hover:scale-105 transition-transform shadow-2xl shadow-blue-600/30">ПРИСОЕДИНИТЬСЯ</a>
                <a href="/wheels" class="px-8 py-5 bg-slate-800 rounded-3xl font-black text-xl">СМОТРЕТЬ ЛОТЫ</a>
            </div>
        </div>
        <div class="flex-1 hidden md:block opacity-20 text-9xl select-none pointer-events-none font-black italic text-blue-500 rotate-12">
            R17 R18 R19 R20
        </div>
    </main>
</body>
</html>
