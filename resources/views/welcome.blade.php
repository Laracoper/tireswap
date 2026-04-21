<x-layouts.app>
    <div class="max-w-7xl mx-auto px-4 md:px-6 py-12 md:py-24 italic overflow-hidden">
        <!-- Hero Section -->
        <section class="relative mb-32">
            <div class="relative z-10">
                <h1 class="text-5xl md:text-9xl font-black text-white leading-[0.85] uppercase tracking-tighter mb-8">
                    ТВОИ КОЛЕСА <br>
                    <span class="text-brand text-outline-mobile md:text-none">ИЩУТ ПРОЕКТ</span>
                </h1>
                <p class="text-lg md:text-2xl text-slate-400 font-medium max-w-2xl leading-relaxed mb-10">
                    Первое в СНГ комьюнити фанатов стиля. Здесь ищут правильный фитмент и ту самую «полку».
                </p>
                {{-- ------------- --}}
                {{-- <div class="hidden">
                    <livewire:components.msg-counter />
                </div> --}}

                <section class="mt-20">
                    <h2 class="text-3xl font-black uppercase italic mb-8">Свежие <span
                            class="text-brand">поступления</span></h2>
                    <livewire:components.user-wheels :onlyMy="false" />
                </section>


                {{-- -------------- --}}
                <div class="flex flex-col sm:flex-row gap-4">
                    <a href="/register"
                        class="w-full sm:w-auto px-8 py-5 bg-brand text-slate-950 font-black rounded-2xl hover:scale-105 transition-transform uppercase shadow-2xl shadow-brand/40 text-base md:text-xl text-center">
                        Регистрация
                    </a>
                    <a href="/wheels"
                        class="w-full sm:w-auto px-8 py-5 border border-slate-700 text-white font-black rounded-2xl hover:bg-slate-800 transition-all uppercase text-base md:text-xl text-center">
                        Маркетплейс
                    </a>
                </div>
            </div>
            <div
                class="absolute right-0 top-0 opacity-[0.03] text-[25vw] font-black pointer-events-none select-none uppercase leading-none hidden md:block">
                WHEEL
            </div>
        </section>

        <!-- Кто мы -->
        <section class="grid md:grid-cols-2 gap-12 md:gap-20 items-center mb-32 border-t border-slate-800 pt-20">
            <div>
                <h2 class="text-3xl md:text-6xl font-black text-white uppercase tracking-tighter mb-6">КТО МЫ <span
                        class="text-brand">ТАКИЕ?</span></h2>
                <div class="space-y-6 text-slate-400 text-base md:text-lg leading-relaxed italic">
                    <p>Мы — команда энтузиастов, уставших от «штамповок» на обычных сайтах в поисках редкой японской
                        ковки.</p>
                    <p><span class="text-white font-bold uppercase">TireSwap</span> — это экосистема для тех, кто знает,
                        что такое вылет и ширина диска.</p>
                </div>
            </div>
            <div class="bg-surface p-8 md:p-10 rounded-[40px] border border-slate-800 shadow-2xl text-center">
                <div class="text-6xl md:text-8xl mb-4">🛞</div>
                <h3 class="text-xl md:text-2xl font-black uppercase text-white mb-2 italic tracking-tighter">Никакого
                    мусора</h3>
                <p class="text-slate-500 uppercase font-bold text-xs tracking-widest leading-tight">Только диски. Только
                    шины.</p>
            </div>
        </section>

        <!-- Чем занимаемся -->
        <section class="mb-32">
            <h2 class="text-3xl md:text-6xl font-black text-center text-white uppercase mb-16 italic">ЧЕМ МЫ <span
                    class="text-brand">ЗАНЯТЫ?</span></h2>
            <div class="grid md:grid-cols-3 gap-6">
                <div class="bg-surface/50 p-6 md:p-8 rounded-3xl border border-slate-800 italic">
                    <div class="text-brand text-3xl font-black mb-4">01.</div>
                    <h3 class="text-lg font-black uppercase text-white mb-2">Удобный подбор</h3>
                    <p class="text-slate-400 text-sm">Поиск по вылету (ET), ширине (J) и разболтовке (PCD) в один клик.
                    </p>
                </div>
                <div class="bg-surface/50 p-6 md:p-8 rounded-3xl border border-slate-800 italic">
                    <div class="text-brand text-3xl font-black mb-4">02.</div>
                    <h3 class="text-lg font-black uppercase text-white mb-2">Live-общение</h3>
                    <p class="text-slate-400 text-sm">Встроенные чаты для быстрой связи с продавцом и обсуждения цены.
                    </p>
                </div>
                <div class="bg-surface/50 p-6 md:p-8 rounded-3xl border border-slate-800 italic">
                    <div class="text-brand text-3xl font-black mb-4">03.</div>
                    <h3 class="text-lg font-black uppercase text-white mb-2">Комьюнити</h3>
                    <p class="text-slate-400 text-sm">Система рейтингов и отзывов внутри клуба для безопасных сделок.
                    </p>
                </div>
            </div>
        </section>
    </div>

    <!-- Скрипт-спасатель для бургера (чистый JS) -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Находим кнопку бургера и мобильное меню по их стандартным селекторам из Layout
            // Так как Alpine не работает, мы принудительно вешаем событие клика
            const burgerBtn = document.querySelector('[@click="mobileMenu = !mobileMenu"]');
            const mobileMenu = document.querySelector('[x-show="mobileMenu"]');

            if (burgerBtn && mobileMenu) {
                burgerBtn.onclick = function(e) {
                    e.preventDefault();
                    const isHidden = mobileMenu.style.display === 'none' || mobileMenu.classList.contains(
                        'hidden');

                    if (isHidden || !mobileMenu.style.display) {
                        mobileMenu.style.display = 'block';
                        mobileMenu.classList.remove('hidden');
                        mobileMenu.style.opacity = '1';
                        mobileMenu.style.transform = 'translateX(0)';
                    } else {
                        mobileMenu.style.display = 'none';
                    }
                };
            }
        });
    </script>

    <style>
        @media (max-width: 640px) {
            .text-outline-mobile {
                -webkit-text-stroke: 1px #fff;
                color: transparent;
            }
        }
    </style>
</x-layouts.app>
