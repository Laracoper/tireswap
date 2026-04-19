<?php
use Livewire\Volt\Component;
use App\Models\Wheel;
use Livewire\Attributes\Layout;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;

new #[Layout('components.layouts.app')] class extends Component {
    use WithFileUploads;

    public Wheel $wheel;
    public $radius;
    public $brand;
    public $intent;
    public $photos = []; // Новые фото
    public $oldPhotos = []; // Существующие фото

    public function mount(Wheel $wheel) {
        // Проверка прав: только автор может редактировать
        if ($wheel->user_id !== auth()->id()) abort(403);

        $this->wheel = $wheel;
        $this->radius = $wheel->radius;
        $this->brand = $wheel->brand;
        $this->intent = $wheel->intent;
        $this->oldPhotos = $wheel->photos ?? [];
    }

    public function removeOldPhoto($index) {
        array_splice($this->oldPhotos, $index, 1);
    }

    public function save() {
        $this->validate([
            'brand' => 'required|min:2',
            'radius' => 'required|integer|min:12|max:30',
            'intent' => 'required',
        ]);

        $newPaths = [];
        foreach ($this->photos as $photo) {
            $newPaths[] = $photo->store('wheels', 'public');
        }

        // Объединяем старые оставшиеся фото и новые
        $allPhotos = array_merge($this->oldPhotos, $newPaths);

        if (empty($allPhotos)) {
            $this->addError('photos', 'Добавьте хотя бы одно фото');
            return;
        }

        $this->wheel->update([
            'brand' => $this->brand,
            'radius' => $this->radius,
            'intent' => $this->intent,
            'photos' => $allPhotos,
            // Слаг не меняем, чтобы не ломать SEO-ссылки
        ]);

        return redirect()->to('/dashboard');
    }
}; ?>

<div class="max-w-2xl mx-auto pb-20">
    <h1 class="text-3xl font-black mb-8 italic uppercase tracking-tighter">Редактировать лот</h1>
    
    <form wire:submit="save" class="space-y-6 bg-surface p-8 rounded-[32px] border border-slate-800">
        <div class="grid grid-cols-2 gap-4">
            <div class="space-y-2">
                <label class="text-[10px] font-black text-slate-500 uppercase px-2">Радиус (R)</label>
                <input type="number" wire:model="radius" class="w-full">
            </div>
            <div class="space-y-2">
                <label class="text-[10px] font-black text-slate-500 uppercase px-2">Марка</label>
                <input type="text" wire:model="brand" class="w-full">
            </div>
        </div>

        <div class="space-y-4">
            <label class="text-[10px] font-black text-slate-500 uppercase px-2">Текущие фото (можно удалить)</label>
            <div class="flex gap-2 overflow-x-auto pb-2">
                @foreach($oldPhotos as $index => $path)
                    <div class="relative flex-shrink-0">
                        <img src="{{ asset('storage/'.$path) }}" class="w-20 h-20 object-cover rounded-xl border border-slate-700">
                        <button type="button" wire:click="removeOldPhoto({{ $index }})" class="absolute -top-2 -right-2 bg-red-600 text-white w-5 h-5 rounded-full text-[10px]">✕</button>
                    </div>
                @endforeach
            </div>

            <label class="block border-2 border-dashed border-slate-800 p-6 rounded-2xl text-center cursor-pointer hover:border-brand transition">
                <span class="text-slate-500 text-sm italic">Загрузить новые фото</span>
                <input type="file" wire:model="photos" multiple class="hidden">
            </label>
        </div>

        <button type="submit" class="w-full bg-brand py-4 rounded-2xl font-black text-white hover:scale-[1.02] transition-all">СОХРАНИТЬ ИЗМЕНЕНИЯ</button>
    </form>
</div>
