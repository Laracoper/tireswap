<?php
use Livewire\Volt\Component;
use App\Models\Wheel;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

new #[Layout('components.layouts.app')] class extends Component {
    use WithPagination;
    
    public $search = '';

    public function getWheelsProperty() {
        return Wheel::query()
            ->when($this->search, fn($q) => $q->where('brand', 'like', "%{$this->search}%"))
            ->with('user')
            ->latest()
            ->paginate(12);
    }

    // Сбрасываем пагинацию при поиске
    public function updatingSearch() {
        $this->resetPage();
    }
}; ?>

<div class="pb-24 md:pb-0">
    <!-- Заголовок и Живой поиск -->
    <div class="flex flex-col gap-6 mb-10">
        <div>
            <h1 class="text-3xl md:text-5xl font-black italic uppercase tracking-tighter">Маркетплейс</h1>
            <p class="text-sm text-slate-500 font-medium">Найди идеальную пару для своих дисков</p>
        </div>
        
        <div class="relative max-w-2xl group">
            <input 
                wire:model.live.debounce.300ms="search" 
                type="text" 
                placeholder="Поиск марки (напр. Michelin...)" 
                class="w-full bg-slate-900 border-2 border-slate-800 rounded-2xl px-6 py-4 text-white placeholder-slate-500 focus:border-brand focus:ring-0 transition-all outline-none"
            >
            <div class="absolute right-6 top-4.5 text-slate-600 group-focus-within:text-brand transition-colors text-xl">
                🔍
            </div>
        </div>
    </div>

    <!-- Сетка объявлений -->
    <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 md:gap-8">
        @forelse($this->wheels as $wheel)
            <div class="bg-surface border border-slate-800 rounded-[32px] overflow-hidden hover:border-brand/40 transition-all group shadow-xl">
                <!-- Контейнер фото -->
                <div class="aspect-square bg-bg relative overflow-hidden">
                    @if($wheel->photos && count($wheel->photos) > 0)
                        <img src="{{ asset('storage/' . $wheel->photos[0]) }}" 
                             class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
                             alt="{{ $wheel->brand }}">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-slate-800 text-5xl italic font-black select-none">
                            TIRE
                        </div>
                    @endif
                    
                    <!-- Бейдж типа сделки -->
                    <div class="absolute bottom-4 left-4 bg-black/70 backdrop-blur-md px-3 py-1.5 rounded-xl text-[10px] font-black text-brand uppercase tracking-widest border border-white/5">
                        {{ $wheel->intent === 'swap' ? '♻️ Обмен' : '💰 Продажа' }}
                    </div>
                </div>
                
                <!-- Информация -->
                <div class="p-5 md:p-7">
                    <div class="flex justify-between items-start mb-1">
                        <h3 class="text-sm md:text-xl font-black uppercase truncate tracking-tighter text-white">
                            {{ $wheel->brand }}
                        </h3>
                        <span class="text-brand font-black italic text-sm md:text-lg ml-2">
                            R{{ $wheel->radius }}
                        </span>
                    </div>
                    
                    <p class="text-[10px] md:text-xs text-slate-500 mb-5 truncate italic opacity-80">
                        Владелец: {{ $wheel->user->name }}
                    </p>
                    
                    <!-- Кнопка с переходом по SLUG -->
                    <a href="{{ route('wheels.show', $wheel) }}" 
                       wire:navigate 
                       class="block w-full py-3 md:py-4 bg-slate-800 group-hover:bg-brand text-white text-center rounded-2xl text-[10px] md:text-xs font-black transition-all uppercase tracking-[0.2em] shadow-lg">
                        Подробнее
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-full py-24 text-center border-2 border-dashed border-slate-800 rounded-[48px] bg-surface/30">
                <div class="text-5xl mb-4 opacity-20">🛞</div>
                <p class="text-slate-500 text-sm font-black uppercase tracking-widest">Ничего не найдено</p>
            </div>
        @endforelse
    </div>

    <!-- Пагинация -->
    <div class="mt-12">
        {{ $this->wheels->links() }}
    </div>
</div>
