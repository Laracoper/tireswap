<?php
use Livewire\Volt\Component;
use App\Models\Message;
use Livewire\Attributes\On;

new class extends Component {
    // Слушаем событие прихода нового сообщения
    public function getListeners() {
        return ["echo-private:chat." . auth()->id() . ",MessageSent" => '$refresh'];
    }

    public function getCountProperty() {
        return Message::where('receiver_id', auth()->id())
                      ->where('is_read', false)
                      ->count();
    }
}; ?>

<div wire:poll.10s> {{-- На всякий случай проверяем раз в 10 сек, если сокеты моргнут --}}
    @if($this->count > 0)
        <span class="flex h-5 w-5 items-center justify-center rounded-full bg-red-500 text-[10px] font-bold text-white shadow-lg shadow-red-500/40 animate-pulse">
            {{ $this->count }}
        </span>
    @endif
</div>
