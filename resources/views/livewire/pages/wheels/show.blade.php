<?php
use Livewire\Volt\Component;
use App\Models\Wheel;
use Livewire\Attributes\Layout;

new #[Layout('components.layouts.app')] class extends Component {
    public Wheel $wheel;
    public $mainPhoto = '';

    public function mount(Wheel $wheel)
    {
        $this->wheel = $wheel->load('user');

        // Устанавливаем первое фото как главное по умолчанию
        if ($this->wheel->photos && count($this->wheel->photos) > 0) {
            $this->mainPhoto = $this->wheel->photos[0];
        }
    }

    public function setMainPhoto($path)
    {
        $this->mainPhoto = $path;
    }

    public function contact()
    {
        if (auth()->guest()) {
            return redirect()->route('login');
        }
        // Здесь будет логика открытия чата
    }
}; ?>

<div class="max-w-6xl mx-auto pb-20 md:pb-0">
    <!-- Кнопка назад -->
    <a href="{{ route('wheels.index') }}" wire:navigate
        class="inline-flex items-center gap-2 text-slate-500 hover:text-white mb-8 transition-colors group">
        <span class="group-hover:-translate-x-1 transition-transform">←</span>
        <span class="text-xs font-bold uppercase tracking-widest">Назад к маркету</span>
    </a>

    @auth
        @if (auth()->id() !== $wheel->user_id)
            <livewire:components.chat :receiverId="$wheel->user_id" />
        @endif
    @endauth


    <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 md:gap-16">

        <!-- ГАЛЕРЕЯ (5 колонок) -->
        <div class="lg:col-span-7 space-y-6">
            <!-- Главное фото -->
            <div class="aspect-square bg-surface rounded-[40px] overflow-hidden border border-slate-800 shadow-2xl">
                @if ($mainPhoto)
                    <img src="{{ asset('storage/' . $mainPhoto) }}" class="w-full h-full object-cover animate-fade-in">
                @else
                    <div
                        class="w-full h-full flex items-center justify-center text-slate-800 text-9xl italic font-black select-none">
                        🛞</div>
                @endif
            </div>

            <!-- Миниатюры -->
            @if ($wheel->photos && count($wheel->photos) > 1)
                <div class="flex gap-4 overflow-x-auto pb-2 scrollbar-hide">
                    @foreach ($wheel->photos as $photo)
                        <button wire:click="setMainPhoto('{{ $photo }}')"
                            class="w-24 h-24 flex-shrink-0 rounded-2xl overflow-hidden border-2 transition-all {{ $mainPhoto === $photo ? 'border-brand scale-95' : 'border-slate-800 opacity-50 hover:opacity-100' }}">
                            <img src="{{ asset('storage/' . $photo) }}" class="w-full h-full object-cover">
                        </button>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- ИНФОРМАЦИЯ (5 колонок) -->
        <div class="lg:col-span-5 space-y-8">
            <div class="space-y-2">
                <div
                    class="inline-flex px-3 py-1 bg-brand/10 text-brand rounded-lg text-[10px] font-black uppercase tracking-widest border border-brand/20">
                    {{ $wheel->intent === 'swap' ? '♻️ Обмен' : '💰 Продажа' }}
                </div>
                <h1 class="text-5xl md:text-6xl font-black italic uppercase tracking-tighter text-white">
                    {{ $wheel->brand }}</h1>
                <p class="text-2xl text-slate-500 font-light tracking-widest">ПАРАМЕТР: <span
                        class="text-white font-bold italic">R{{ $wheel->radius }}</span></p>
            </div>

            <div class="p-8 bg-surface rounded-[32px] border border-slate-800 space-y-6">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-brand/20 rounded-full flex items-center justify-center text-xl">👤</div>
                    <div>
                        <p class="text-[10px] text-slate-500 font-black uppercase tracking-widest">Владелец</p>
                        <p class="text-lg font-bold text-white">{{ $wheel->user->name }}</p>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-slate-800 rounded-full flex items-center justify-center text-xl">📅</div>
                    <div>
                        <p class="text-[10px] text-slate-500 font-black uppercase tracking-widest">Размещено</p>
                        <p class="text-lg font-bold text-white">{{ $wheel->created_at->format('d.m.Y') }}</p>
                    </div>
                </div>
            </div>

            <div class="pt-4">
                <button wire:click="contact"
                    class="w-full py-6 bg-brand hover:brightness-110 text-white rounded-[24px] font-black text-xl shadow-2xl shadow-brand/30 transition-all active:scale-95 uppercase tracking-tighter italic">
                    Связаться с мастером
                </button>
                <p class="text-center text-[10px] text-slate-600 mt-4 uppercase font-bold tracking-widest">ID
                    объявления: #{{ $wheel->id }}</p>
            </div>
        </div>
    </div>
</div>
