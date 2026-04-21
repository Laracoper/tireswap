<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'TireSwap' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-bg text-slate-200 antialiased italic" x-data="{ mobileMenu: false }"
    wire:key="body-layout-{{ request()->path() }}">

    <!-- Шапка (Header) -->
    <!-- На главной видна всегда, на остальных страницах только на мобилках -->
    <header
        class="flex justify-between items-center p-5 bg-surface/80 backdrop-blur-lg border-b border-slate-800 sticky top-0 z-50 {{ !request()->is('/') ? 'md:hidden' : '' }}">

        <div class="text-xl font-black text-brand tracking-tighter uppercase">TIRESWAP</div>

        <!-- Десктопное меню (только для главной) -->
        <div class="hidden md:flex items-center gap-8 font-bold text-sm uppercase tracking-widest">
            <a href="/wheels" class="hover:text-brand transition-colors">Маркет</a>
            @auth
                <a href="/dashboard"
                    class="px-6 py-2 bg-brand text-white rounded-xl font-black shadow-lg shadow-brand/20">Мой гараж</a>
            @else
                <a href="/login" class="text-slate-400 hover:text-white transition-colors">Войти</a>
                <a href="/register"
                    class="px-6 py-2 border border-brand text-brand rounded-xl font-black hover:bg-brand hover:text-white transition-all text-xs">Регистрация</a>
            @endauth
        </div>

        <!-- Кнопка Бургера / Крестик (Мобильная версия) -->
        <button @click="mobileMenu = !mobileMenu; console.log('Состояние меню:', mobileMenu)"
            class="md:hidden p-2 text-slate-400 focus:outline-none relative z-50">
            <svg x-show="!mobileMenu" class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path>
            </svg>
            <svg x-show="mobileMenu" x-cloak class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>

    </header>

    <!-- Мобильное меню (Overlay) -->
    <div x-show="mobileMenu" x-cloak x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-x-full" x-transition:enter-end="opacity-100 translate-x-0"
        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-x-0"
        x-transition:leave-end="opacity-0 translate-x-full" class="fixed inset-0 z-40 md:hidden">

        <div
            class="absolute inset-0 bg-bg/95 backdrop-blur-xl p-10 flex flex-col justify-center items-center space-y-8">
            <nav class="flex flex-col items-center space-y-8 text-center">
                <a href="/" @click="mobileMenu = false"
                    class="text-2xl font-black {{ request()->is('/') ? 'text-brand' : 'text-slate-500' }} uppercase">Главная</a>

                @auth
                    <a href="/dashboard" @click="mobileMenu = false"
                        class="text-2xl font-black {{ request()->is('dashboard') ? 'text-brand' : 'text-slate-500' }} uppercase text-outline">Моя
                        страница</a>
                @endauth

                <a href="/wheels" @click="mobileMenu = false"
                    class="text-2xl font-black {{ request()->is('wheels*') ? 'text-brand' : 'text-slate-500' }} uppercase">Маркетплейс</a>

                <a href="/wheels/create" @click="mobileMenu = false"
                    class="text-2xl font-black text-white bg-brand px-10 py-4 rounded-2xl shadow-xl shadow-brand/30 uppercase">Добавить
                    лот</a>
            </nav>

            @auth
                <form method="POST" action="{{ route('logout') }}" class="pt-10">
                    @csrf
                    <button type="submit" class="text-red-500 font-bold uppercase tracking-widest text-sm">Выйти из
                        системы</button>
                </form>
            @else
                <div class="flex flex-col gap-4 w-full max-w-xs pt-10">
                    <a href="/login" @click="mobileMenu = false"
                        class="text-center py-4 bg-slate-800 rounded-2xl font-bold uppercase">Войти</a>
                </div>
            @endauth
        </div>
    </div>

    <div class="flex flex-col md:flex-row min-h-screen">
        <!-- Десктопный Сайдбар (Скрыт на главной) -->
        @if (!request()->is('/'))
            <aside class="hidden md:flex w-64 bg-surface border-r border-slate-800 flex-col p-6 sticky top-0 h-screen">
                <div class="text-2xl font-black text-brand italic tracking-tighter mb-10 uppercase">TIRESWAP</div>

                <nav class="flex-1 space-y-2 uppercase text-xs font-bold">
                    @auth
                        <a href="/dashboard" wire:navigate
                            class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all {{ request()->is('dashboard') ? 'bg-brand text-white shadow-lg shadow-brand/20' : 'text-slate-400 hover:bg-slate-800' }}">
                            <span>🏠</span> Личная страница
                        </a>

                        <a href="/messages" wire:navigate
                            class="flex items-center justify-between px-4 py-2.5 rounded-xl transition-all {{ request()->is('messages') ? 'bg-brand text-white shadow-lg shadow-brand/20' : 'text-slate-400 hover:bg-slate-800' }}">
                            <div class="flex items-center gap-3">
                                <span>💬</span>
                                <span>Сообщения</span>
                            </div>
                            <livewire:components.msg-counter />
                        </a>
                    @endauth

                    <a href="/wheels" wire:navigate
                        class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all {{ request()->is('wheels') ? 'bg-brand text-white shadow-lg shadow-brand/20' : 'text-slate-400 hover:bg-slate-800' }}">
                        <span>🔍</span> Маркетплейс
                    </a>

                    <a href="/wheels/create" wire:navigate
                        class="block w-full py-3 rounded-xl bg-brand text-white font-black text-center shadow-lg shadow-brand/20 hover:scale-[1.02] transition-transform mt-6">
                        ДОБАВИТЬ ЛОТ
                    </a>
                </nav>

                <div class="pt-6 border-t border-slate-800">
                    @auth
                        <div class="px-2 mb-4">
                            <p class="text-[10px] text-slate-500 uppercase font-black tracking-widest">Профиль</p>
                            <p class="text-sm font-bold text-white truncate">{{ auth()->user()->name }}</p>
                        </div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                class="w-full text-left px-4 py-2 rounded-xl bg-red-500/10 text-red-500 text-xs font-bold hover:bg-red-500 hover:text-white transition-all uppercase">
                                Выйти
                            </button>
                        </form>
                    @endauth
                </div>
            </aside>
        @endif

        <!-- Контент -->
        <main class="flex-1 {{ request()->is('/') ? 'p-0' : 'p-5 md:p-12' }} pb-24 md:pb-12">
            {{ $slot }}
        </main>
    </div>

    <style>
        [x-cloak] {
            display: none !important;
        }

        .text-outline {
            -webkit-text-stroke: 1px #475569;
        }
    </style>
    {{-- <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Если Alpine загружен, но не запущен — запускаем
            if (window.Alpine && !window.Alpine.started) {
                window.Alpine.start();
            }
        });
    </script> --}}

</body>

</html>
