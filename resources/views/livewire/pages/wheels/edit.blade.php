<?php
use Livewire\Volt\Component;
use App\Models\Wheel;
use Livewire\Attributes\Layout;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

new #[Layout('components.layouts.app')] class extends Component {
    use WithFileUploads, AuthorizesRequests;

    public Wheel $wheel;
    public $radius;
    public $brand;
    public $intent;
    public $photos = [];      
    public $oldPhotos = [];   

    public function mount(Wheel $wheel) {
        $this->authorize('update', $wheel);

        $this->wheel = $wheel;
        $this->radius = $wheel->radius;
        $this->brand = $wheel->brand;
        $this->intent = $wheel->intent;
        $this->oldPhotos = $wheel->photos ?? [];
    }

    public function removeOldPhoto($index) {
        array_splice($this->oldPhotos, $index, 1);
    }

    public function clearNewPhotos() {
        $this->reset('photos');
    }

    public function save() {
        $this->authorize('update', $this->wheel);

        $this->validate([
            'brand' => 'required|min:2',
            'radius' => 'required|integer|min:12|max:30',
            'intent' => 'required',
            'photos.*' => 'nullable|file|mimes:jpeg,png,jpg,webp,avif|max:5120',
        ]);

        $newPaths = [];
        if ($this->photos) {
            foreach ($this->photos as $photo) {
                $newPaths[] = $photo->store('wheels', 'public');
            }
        }

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
        ]);

        return redirect()->route('dashboard');
    }
}; ?>

{{-- ВСЁ ДОЛЖНО БЫТЬ ВНУТРИ ЭТОГО ОДНОГО DIV --}}
<div class="max-w-2xl mx-auto pb-20 px-4">
    <div class="flex items-center gap-4 mb-8">
        <a href="{{ route('dashboard') }}" class="p-3 bg-slate-900 border border-slate-800 rounded-2xl text-slate-400 hover:text-brand transition shadow-lg">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <h1 class="text-3xl font-black italic uppercase tracking-tighter text-slate-100">Редактировать</h1>
    </div>
    
    <form wire:submit="save" class="space-y-8 bg-slate-900 p-6 lg:p-10 rounded-[40px] border border-slate-800 shadow-2xl relative overflow-hidden">
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-2">
                <label class="text-[10px] font-black text-slate-500 uppercase px-2 tracking-widest">Радиус (R)</label>
                <input type="number" wire:model="radius" class="w-full bg-slate-950 border-slate-800 rounded-2xl px-5 py-4 text-slate-200 focus:border-brand outline-none transition shadow-inner">
                @error('radius') <span class="text-red-500 text-[10px] font-bold px-2">{{ $message }}</span> @enderror
            </div>
            <div class="space-y-2">
                <label class="text-[10px] font-black text-slate-500 uppercase px-2 tracking-widest">Марка</label>
                <input type="text" wire:model="brand" class="w-full bg-slate-950 border-slate-800 rounded-2xl px-5 py-4 text-slate-200 focus:border-brand outline-none transition shadow-inner">
                @error('brand') <span class="text-red-500 text-[10px] font-bold px-2">{{ $message }}</span> @enderror
            </div>
        </div>

        <div class="space-y-6">
            <label class="text-[10px] font-black text-slate-500 uppercase px-2 tracking-widest">Медиа файлы</label>
            
            {{-- Существующие фото --}}
            @if(count($oldPhotos) > 0)
                <div class="flex flex-wrap gap-4 p-5 bg-slate-950/50 rounded-3xl border border-slate-800/50">
                    @foreach($oldPhotos as $index => $path)
                        <div class="relative group">
                            <img src="{{ asset('storage/'.$path) }}" class="w-24 h-24 object-cover rounded-2xl border border-slate-800">
                            <button type="button" wire:click="removeOldPhoto({{ $index }})" class="absolute -top-2 -right-2 bg-slate-800 text-red-500 w-7 h-7 rounded-full flex items-center justify-center border border-slate-700 hover:bg-red-600 hover:text-white transition-all shadow-lg">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="2"/></svg>
                            </button>
                        </div>
                    @endforeach
                </div>
            @endif

            {{-- Новые фото --}}
            @if($photos)
                <div class="space-y-3 animate-fade-in">
                    <div class="flex justify-between items-center px-2">
                        <p class="text-[10px] text-brand font-black uppercase tracking-widest">К загрузке:</p>
                        <button type="button" wire:click="clearNewPhotos" class="text-[10px] text-red-500 font-bold uppercase hover:underline">Отмена</button>
                    </div>
                    <div class="flex flex-wrap gap-4 p-5 bg-brand/5 rounded-3xl border border-brand/20">
                        @foreach($photos as $photo)
                            <div class="relative">
                                @php
                                    try { $url = $photo->temporaryUrl(); } catch (\Exception $e) { $url = null; }
                                @endphp

                                @if($url)
                                    <img src="{{ $url }}" class="w-24 h-24 object-cover rounded-2xl border-2 border-brand shadow-lg shadow-brand/10">
                                @else
                                    <div class="w-24 h-24 bg-slate-800 rounded-2xl flex items-center justify-center text-[8px] text-slate-500 p-2 text-center uppercase">Файл готов</div>
                                @endif
                                <div class="absolute -bottom-2 inset-x-2 bg-brand text-slate-950 text-[9px] font-black uppercase text-center rounded-md py-0.5 shadow-md">NEW</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="relative">
                <label class="flex flex-col items-center justify-center border-2 border-dashed border-slate-800 p-10 rounded-[32px] cursor-pointer hover:border-brand/40 hover:bg-brand/5 transition-all group">
                    <div class="w-12 h-12 bg-slate-800 rounded-full flex items-center justify-center mb-3 group-hover:bg-brand/20 transition-all text-slate-500 group-hover:text-brand">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 4v16m8-8H4" stroke-width="2.5" stroke-linecap="round"/></svg>
                    </div>
                    <span class="text-slate-400 text-sm font-bold uppercase tracking-tight">Добавить еще</span>
                    <input type="file" wire:model="photos" multiple class="hidden">
                </label>
                
                <div wire:loading wire:target="photos" class="absolute inset-0 bg-slate-900/90 backdrop-blur-sm rounded-[32px] flex items-center justify-center z-10">
                    <div class="flex flex-col items-center gap-2">
                        <div class="w-8 h-8 border-4 border-brand border-t-transparent rounded-full animate-spin"></div>
                        <span class="text-brand font-black text-[10px] uppercase tracking-widest">Загружаем...</span>
                    </div>
                </div>
            </div>
            @error('photos') <span class="text-red-500 text-[10px] font-bold px-2 uppercase">{{ $message }}</span> @enderror
        </div>

        <div class="pt-6">
            <button type="submit" 
                    wire:loading.attr="disabled"
                    class="w-full bg-brand text-slate-950 py-6 rounded-[24px] font-black text-xl hover:scale-[1.01] active:scale-[0.98] transition-all shadow-2xl shadow-brand/20 uppercase italic tracking-tighter">
                <span wire:loading.remove wire:target="save">Сохранить изменения</span>
                <span wire:loading wire:target="save" class="animate-pulse">Применяем...</span>
            </button>
        </div>
    </form>

    <style>
        .animate-fade-in { animation: fadeIn 0.3s ease-out; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</div> {{-- КОНЕЦ КОРНЕВОГО ТЕГА --}}
