<?php
use Livewire\Volt\Component;
use App\Models\Wheel;

new class extends Component {
    public function getWheelsProperty() {
        return Wheel::where('user_id', auth()->id())->latest()->get();
    }

    public function delete($id) {
        $wheel = Wheel::where('user_id', auth()->id())->findOrFail($id);
        
        // Удаляем файлы из папки storage, чтобы не забивать место
        if ($wheel->photos) {
            foreach ($wheel->photos as $path) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($path);
            }
        }

        $wheel->delete();
    }
}; ?>

<div class="space-y-4">
    @forelse($this->wheels as $wheel)
        <div class="bg-bg/50 border border-slate-800 p-4 rounded-2xl flex items-center justify-between gap-4">
            <div class="flex items-center gap-4 truncate">
                <img src="{{ asset('storage/'.($wheel->photos[0] ?? '')) }}" class="w-12 h-12 rounded-xl object-cover bg-slate-800">
                <div class="truncate">
                    <h4 class="font-bold text-sm uppercase tracking-tighter">{{ $wheel->brand }} R{{ $wheel->radius }}</h4>
                    <p class="text-[10px] text-slate-500 italic">{{ $wheel->slug }}</p>
                </div>
            </div>

            <div class="flex gap-2">
                <a href="{{ route('wheels.edit', $wheel) }}" wire:navigate class="p-2 bg-slate-800 hover:bg-brand rounded-lg transition-colors text-xs">✏️</a>
                <button 
                    wire:click="delete({{ $wheel->id }})" 
                    wire:confirm="Удалить это объявление навсегда?"
                    class="p-2 bg-slate-800 hover:bg-red-600 rounded-lg transition-colors text-xs"
                >🗑️</button>
            </div>
        </div>
    @empty
        <div class="text-center py-10 opacity-30 italic">Список пуст</div>
    @endforelse
</div>
