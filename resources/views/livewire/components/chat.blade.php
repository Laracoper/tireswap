<?php
use Livewire\Volt\Component;
use App\Models\Message;
use App\Events\MessageSent;
use Illuminate\Support\Facades\Auth;

new class extends Component {
    public $receiverId;
    public $body = '';

    public function getListeners()
    {
        return [
            // Слушаем сообщения именно для этого пользователя
            'echo-private:chat.' . Auth::id() . ',MessageSent' => 'onMessageReceived',
        ];
    }

    public function onMessageReceived($event)
    {
        // Помечаем как прочитанное, если это сообщение от текущего собеседника
        if ($event['message']['sender_id'] == $this->receiverId) {
            Message::find($event['message']['id'])?->update(['is_read' => true]);
            $this->dispatch('refresh-counter'); // Сбрасываем красный кружок в меню
        }

        $this->dispatch('$refresh');
        $this->dispatch('scroll-bottom');
    }

    public function getMessagesProperty()
    {
        return Message::where(function ($q) {
            $q->where('sender_id', Auth::id())->where('receiver_id', $this->receiverId);
        })
            ->orWhere(function ($q) {
                $q->where('sender_id', $this->receiverId)->where('receiver_id', Auth::id());
            })
            ->oldest()
            ->get();
    }

    public function send()
    {
        $text = trim($this->body);
        if (empty($text)) {
            return;
        }

        $message = Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $this->receiverId,
            'body' => $text,
        ]);

        broadcast(new MessageSent($message))->toOthers();
        $this->reset('body');

        $this->dispatch('scroll-bottom');
    }
}; ?>

<div class="flex flex-col h-[500px] bg-surface border border-slate-800 rounded-[32px] overflow-hidden italic shadow-2xl">
    <!-- Окно сообщений -->
    <div x-data="{ scrollToBottom() { $el.scrollTo({ top: $el.scrollHeight, behavior: 'smooth' }) } }" x-init="scrollToBottom()" @scroll-bottom.window="scrollToBottom()"
        class="flex-1 overflow-y-auto p-6 space-y-4 custom-scrollbar bg-bg/10">
        @forelse ($this->messages as $msg)
            <div class="flex {{ $msg->sender_id === auth()->id() ? 'justify-end' : 'justify-start' }}">
                <div
                    class="flex flex-col {{ $msg->sender_id === auth()->id() ? 'items-end' : 'items-start' }} max-w-[85%]">
                    <div
                        class="px-4 py-2 rounded-2xl {{ $msg->sender_id === auth()->id() ? 'bg-brand text-slate-950 font-black rounded-tr-none shadow-lg shadow-brand/20' : 'bg-slate-800 text-slate-200 rounded-tl-none border border-slate-700' }}">
                        <p class="text-sm leading-relaxed">{{ $msg->body }}</p>
                    </div>
                    <span class="text-[9px] opacity-40 mt-1 font-black uppercase tracking-tighter">
                        {{ $msg->created_at->format('H:i') }}
                    </span>
                </div>
            </div>
        @empty
            <div
                class="h-full flex items-center justify-center opacity-20 uppercase font-black text-sm tracking-widest">
                Напишите первым
            </div>
        @endforelse
    </div>

    <!-- Форма отправки -->
    <div class="p-4 bg-bg/30 border-t border-slate-800 flex gap-2 items-center">
        <input wire:model="body" wire:keydown.enter="send" type="text" placeholder="Сообщение..."
            class="flex-1 bg-slate-950 border border-slate-800 rounded-2xl px-4 py-3 text-sm md:text-base text-slate-200 outline-none focus:border-brand transition-all italic font-medium">
        <button type="button" wire:click="send"
            class="bg-brand text-slate-950 w-12 h-12 flex items-center justify-center rounded-2xl shadow-lg shadow-brand/20 hover:scale-105 active:scale-95 transition-all flex-shrink-0">
            <!-- Иконка бумажного самолетика (как в ТГ) -->
            <svg xmlns="http://w3.org" class="w-6 h-6 rotate-45 -translate-x-0.5" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                    d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
            </svg>
        </button>
    </div>

</div>
