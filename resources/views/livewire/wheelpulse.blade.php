<?php
use Livewire\Volt\Component;
use App\Models\Wheel;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public $radius = '';
    public $brand = '';
    public $intent = 'swap'; // По умолчанию смотрим обмен
    public $distance = 50; // Радиус поиска в км

    public function getWheelsProperty()
    {
        return Wheel::query()->when($this->radius, fn($q) => $q->where('radius', $this->radius))->when($this->brand, fn($q) => $q->where('brand', 'like', "%{$this->brand}%"))->where('intent', $this->intent)->latest()->paginate(12);
    }
}; ?>

<div class="bg-slate-950 text-white min-h-screen p-6">
    <!-- Стеклянная панель фильтров -->
    <div class="backdrop-blur-md bg-white/5 border border-white/10 p-6 rounded-2xl mb-8 sticky top-4 z-50">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <select wire:model.live="radius" class="bg-slate-900 border-none rounded-xl focus:ring-2 ring-blue-500">
                <option value="">Любой радиус</option>
                @foreach (range(13, 24) as $r)
                    <option value="{{ $r }}">R{{ $r }}</option>
                @endforeach
            </select>

            <input wire:model.live.debounce.300ms="brand" type="text" placeholder="Бренд (напр. Pirelli)"
                class="bg-slate-900 border-none rounded-xl focus:ring-2 ring-blue-500">

            <select wire:model.live="intent" class="bg-slate-900 border-none rounded-xl focus:ring-2 ring-blue-500">
                <option value="swap">Обмен</option>
                <option value="search">Кто ищет</option>
                <option value="offer">Кто предлагает</option>
            </select>

            <div class="flex items-center gap-2">
                <span class="text-xs text-slate-400">Радиус: {{ $distance }}км</span>
                <input type="range" wire:model.live="distance" min="5" max="500" class="w-full">
            </div>
        </div>
    </div>

    <!-- Сетка колес -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @foreach ($this->wheels as $wheel)
            <div
                class="group relative bg-slate-900 rounded-3xl overflow-hidden border border-white/5 hover:border-blue-500/50 transition-all duration-500">
                <div class="aspect-square bg-slate-800 relative">
                    <!-- Заглушка для фото (в 2026 тут будет крутой Image Placeholder) -->
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-950 to-transparent"></div>
                    @if ($wheel->is_ai_verified)
                        <span
                            class="absolute top-4 left-4 bg-blue-500 text-[10px] uppercase font-bold px-2 py-1 rounded-full">AI
                            Verified</span>
                    @endif
                </div>
                <div class="p-4">
                    <h3 class="text-lg font-bold">{{ $wheel->brand }} R{{ $wheel->radius }}</h3>
                    <p class="text-slate-400 text-sm">PCD: {{ $wheel->pcd }} • Протектор: {{ $wheel->tread_depth }}мм
                    </p>
                    <button
                        class="mt-4 w-full py-2 bg-white text-black font-bold rounded-xl hover:bg-blue-400 transition">Предложить
                        обмен</button>
                </div>
            </div>
        @endforeach
    </div>
</div>
