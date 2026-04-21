<?php
use Livewire\Volt\Component;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;

new class extends Component {
    
    public function getListeners() {
        return [
            // Реалтайм обновление через сокеты
            "echo-private:chat." . Auth::id() . ",MessageSent" => '$refresh',
            // Обновление, когда мы прочитали сообщения внутри страницы чата
            "refresh-counter" => '$refresh',
        ];
    }

    public function getCountProperty() {
        return Message::where('receiver_id', Auth::id())
                      ->where('is_read', false)
                      ->count();
    }
}; ?>

<div wire:poll.30s>
    @if($this->count > 0)
        <span class="flex h-5 w-5 items-center justify-center rounded-full bg-red-500 text-[10px] font-black text-white shadow-lg shadow-red-500/40 animate-pulse border border-red-400/20">
            {{ $this->count }}
        </span>
    @endif
</div>
