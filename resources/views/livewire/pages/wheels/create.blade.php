<?php

use Livewire\Volt\Component;
use App\Models\Wheel;
use Livewire\Attributes\Layout;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

new #[Layout('components.layouts.app')] class extends Component {
    use WithFileUploads;

    public $radius = 17;
    public $brand = '';
    public $intent = 'swap';
    public $photos = [];

    // Мгновенная очистка невалидных файлов, чтобы не "падало" превью
    public function updatedPhotos()
    {
        try {
            $this->validate([
                'photos.*' => 'image|mimes:jpeg,png,jpg,webp,avif|max:5120',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->photos = array_filter($this->photos, function($photo) {
                return in_array(strtolower($photo->getClientOriginalExtension()), ['jpg', 'jpeg', 'png', 'webp', 'avif']);
            });
            throw $e;
        }
    }

    public function removePhoto($index)
    {
        array_splice($this->photos, $index, 1);
    }

    public function save()
    {
        $this->validate([
            'brand' => 'required|min:2',
            'radius' => 'required|integer|min:12|max:30',
            'intent' => 'required',
            'photos' => 'required|array|min:1',
            // 'photos.*' => 'image|mimes:jpeg,png,jpg,webp,avif|max:5120',
            'photos.*' => 'required|file|mimetypes:image/jpeg,image/png,image/webp,image/avif|max:5120',

        ], [
            'brand.required' => 'Введите марку шин',
            'brand.min' => 'Название слишком короткое',
            'radius.required' => 'Укажите радиус',
            'intent.required' => 'Выберите тип сделки',
            'photos.required' => 'Добавьте хотя бы одно фото',
            'photos.min' => 'Нужно минимум 1 фото',
        ]);

        $paths = [];
        foreach ($this->photos as $photo) {
            $paths[] = $photo->store('wheels', 'public');
        }

        // Автоматическая генерация SLUG
        $slug = Str::slug($this->brand . '-' . $this->radius . '-' . $this->intent . '-' . Str::random(5));

        Wheel::create([
            'user_id' => auth()->id(),
            'brand' => $this->brand,
            'radius' => $this->radius,
            'intent' => $this->intent,
            'slug' => $slug,
            'photos' => $paths,
            'location' => DB::raw('ST_GeomFromText("POINT(0 0)")'),
        ]);

        return redirect()->to('/dashboard');
    }
}; ?>

<div class="max-w-2xl mx-auto pb-20 md:pb-0">
    <div class="mb-8">
        <h1 class="text-3xl md:text-5xl font-black italic uppercase tracking-tighter">Новый лот</h1>
        <p class="text-slate-500 text-sm md:text-base">Заполните данные для обмена или продажи</p>
    </div>

    <form wire:submit="save" class="space-y-6">
        <div class="bg-surface border border-slate-800 rounded-[32px] p-6 md:p-10 shadow-2xl">
            
            <!-- Тип сделки -->
            <div class="mb-8">
                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] mb-4">Намерение</label>
                <div class="grid grid-cols-2 gap-3">
                    @foreach(['swap' => '♻️ Обмен', 'offer' => '💰 Продажа'] as $key => $label)
                        <button type="button" wire:click="$set('intent', '{{$key}}')" 
                                class="py-4 rounded-2xl border-2 transition-all font-bold text-sm {{ $intent === $key ? 'border-brand bg-brand/5 text-brand' : 'border-slate-800 text-slate-500' }}">
                            {{ $label }}
                        </button>
                    @endforeach
                </div>
                @error('intent') <p class="text-red-500 text-[10px] font-bold uppercase mt-2 px-2">{{ $message }}</p> @enderror
            </div>

            <!-- Характеристики -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] px-2">Радиус (R)</label>
                    <input type="number" wire:model="radius" min="12" max="30" 
                           class="w-full {{ $errors->has('radius') ? 'border-red-500 ring-red-500/20' : 'border-slate-800' }}">
                    @error('radius') <p class="text-red-500 text-[10px] font-bold uppercase px-2">{{ $message }}</p> @enderror
                </div>
                
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] px-2">Марка</label>
                    <input type="text" wire:model="brand" placeholder="Напр: Michelin" 
                           class="w-full {{ $errors->has('brand') ? 'border-red-500 ring-red-500/20' : 'border-slate-800' }}">
                    @error('brand') <p class="text-red-500 text-[10px] font-bold uppercase px-2">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Фотографии -->
            <div class="space-y-4">
                <label class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] px-2">Фотографии (минимум 1)</label>
                
                <label class="flex flex-col items-center justify-center w-full h-40 border-2 border-dashed rounded-[24px] cursor-pointer transition-all {{ $errors->has('photos') || $errors->has('photos.*') ? 'border-red-500 bg-red-500/5' : 'border-slate-800 hover:border-brand/50 hover:bg-slate-800/30' }}">
                    <span class="text-3xl mb-2">📸</span>
                    <span class="text-slate-500 text-xs font-bold uppercase tracking-widest">Добавить фото</span>
                    <input type="file" wire:model="photos" multiple class="hidden">
                </label>

                @error('photos') <p class="text-red-500 text-[10px] font-bold uppercase px-2">{{ $message }}</p> @enderror
                @error('photos.*') <p class="text-red-500 text-[10px] font-bold uppercase px-2">{{ $message }}</p> @enderror

                <!-- Предпросмотр -->
                @if($photos)
                    <div class="flex gap-3 overflow-x-auto py-2 scrollbar-hide">
                        @foreach($photos as $index => $photo)
                            <div class="relative flex-shrink-0 group">
                                @php
                                    $canPreview = in_array(strtolower($photo->getClientOriginalExtension()), ['jpg', 'jpeg', 'png', 'webp', 'avif']);
                                @endphp

                                @if($canPreview)
                                    <img src="{{ $photo->temporaryUrl() }}" class="w-20 h-20 object-cover rounded-2xl border border-slate-700">
                                @else
                                    <div class="w-20 h-20 bg-slate-800 rounded-2xl flex items-center justify-center text-[10px] text-slate-500 uppercase font-bold">
                                        {{ $photo->getClientOriginalExtension() }}
                                    </div>
                                @endif

                                <button type="button" wire:click="removePhoto({{ $index }})" 
                                        class="absolute -top-2 -right-2 bg-red-500 text-white w-6 h-6 rounded-full flex items-center justify-center text-xs shadow-lg hover:scale-110 transition-transform">
                                    ✕
                                </button>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <button type="submit" class="w-full bg-brand py-5 rounded-[24px] font-black text-xl text-white shadow-2xl shadow-brand/30 hover:scale-[1.02] active:scale-95 transition-all uppercase italic tracking-tighter">
            Опубликовать лот
        </button>
    </form>
</div>
