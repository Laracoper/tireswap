<x-layouts.app title="Личный кабинет">
    <div class="space-y-8 pb-20 md:pb-0">
        <div class="flex justify-between items-end">
            <div>
                <p class="text-xs font-black text-brand uppercase tracking-widest mb-1">Профиль мастера</p>
                <h1 class="text-2xl md:text-4xl font-black">ЛК: {{ auth()->user()->name }}</h1>
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="bg-surface p-6 rounded-3xl border border-slate-800 flex justify-between items-center">
                <div>
                    <p class="text-[10px] text-slate-500 uppercase font-black tracking-widest">Мои объявления</p>
                    <p class="text-4xl font-black text-white mt-1">{{ auth()->user()->wheels()->count() }}</p>
                </div>
                <div class="text-4xl">🛞</div>
            </div>

            <a href="/wheels/create" class="bg-brand p-6 rounded-3xl flex items-center justify-between group hover:brightness-110 transition-all shadow-xl shadow-brand/20">
                <span class="text-white font-black text-xl uppercase tracking-tighter italic">Новый лот</span>
                <span class="text-3xl group-hover:rotate-90 transition-transform">➕</span>
            </a>
        </div>

        <div class="space-y-4">
            <h2 class="text-lg font-black uppercase tracking-tighter text-slate-400">Активные предложения</h2>
            <livewire:components.user-wheels />
        </div>
    </div>
</x-layouts.app>
