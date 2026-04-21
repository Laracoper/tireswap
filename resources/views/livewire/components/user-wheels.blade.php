<?php
use Livewire\Volt\Component;
use App\Models\Wheel;
use Illuminate\Support\Facades\Storage;

new class extends Component {
    // Параметр, который определяет режим работы компонента
    public $onlyMy = false;

    public function getWheelsProperty()
    {
        if ($this->onlyMy) {
            return Wheel::where('user_id', auth()->id())
                ->latest()
                ->get();
        }

        // Для главной страницы берем последние 6 любых объявлений
        return Wheel::latest()->take(6)->get();
    }

    public function delete($id)
    {
        if (!$this->onlyMy) {
            return;
        }

        $wheel = Wheel::where('user_id', auth()->id())->findOrFail($id);

        if ($wheel->photos) {
            foreach ($wheel->photos as $path) {
                Storage::disk('public')->delete($path);
            }
        }
        $wheel->delete();
    }
}; ?>

<div class="grid grid-cols-1 {{ !$onlyMy ? 'md:grid-cols-2 lg:grid-cols-3' : '' }} gap-4 italic">
    @forelse($this->wheels as $wheel)
        <div
            class="group bg-surface/40 border border-slate-800 p-4 rounded-[32px] flex flex-col gap-4 hover:border-brand/50 transition-all duration-300 shadow-2xl relative overflow-hidden">

            <!-- Изображение -->
            <div class="relative h-48 w-full overflow-hidden rounded-2xl bg-slate-900 border border-slate-800">
                <img src="{{ asset('storage/' . ($wheel->photos[0] ?? 'no-image.png')) }}"
                    class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">

                <!-- Радиус -->
                <div
                    class="absolute top-3 left-3 bg-brand text-slate-950 px-3 py-1 rounded-full font-black text-xs uppercase shadow-xl">
                    R{{ $wheel->radius }}
                </div>
            </div>

            <!-- Инфо -->
            <!-- Название и параметры -->
            <div class="flex-1">
                <h4 class="font-black text-sm md:text-xl uppercase tracking-tighter text-white">
                    {{ $wheel->brand }} <span class="text-brand">{{ $wheel->model }}</span>
                </h4>

                <div
                    class="flex flex-wrap gap-x-2 md:gap-x-4 gap-y-1 text-[9px] md:text-[11px] font-bold text-slate-500 uppercase tracking-widest mt-1">
                    <span>J <span class="text-slate-300">{{ $wheel->width }}</span></span>
                    <span>ET <span class="text-slate-300">{{ $wheel->offset }}</span></span>
                    <span class="hidden sm:inline">PCD <span class="text-slate-300">{{ $wheel->pcd }}</span></span>
                </div>
            </div>


            <!-- Кнопки управления -->
            <div class="flex gap-2">
                @if ($onlyMy)
                    <!-- Режим Дашборда -->
                    <a href="{{ route('wheels.edit', $wheel) }}" wire:navigate
                        class="flex-1 py-3 bg-slate-800 hover:bg-brand hover:text-slate-950 rounded-xl font-black text-center transition-all uppercase text-xs">
                        Правка
                    </a>
                    <button wire:click="delete({{ $wheel->id }})" wire:confirm="Удалить лот?"
                        class="px-4 bg-slate-800 hover:bg-red-600 rounded-xl transition-all">
                        🗑️
                    </button>
                @else
                    <!-- Режим Маркета (на Главной) -->
                    <a href="{{ route('wheels.show', $wheel) }}" wire:navigate
                        class="flex-1 py-3 bg-brand text-slate-950 font-black rounded-xl text-center transition-all uppercase text-xs shadow-lg shadow-brand/20">
                        Смотреть лот
                    </a>
                @endif
            </div>
        </div>
    @empty
        <div class="col-span-full text-center py-20 opacity-20 font-black uppercase text-2xl italic tracking-widest">
            Нет объявлений
        </div>
    @endforelse
</div>
