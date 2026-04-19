<?php
use Livewire\Volt\Component;
use App\Models\Message;
use App\Events\MessageSent;
use Livewire\Attributes\On;

new class extends Component {
    public $receiverId;
    public $body = '';

    // Слушаем входящие сообщения в реальном времени

    // На это:
    public function getListeners()
    {
        return [
            'echo-private:chat.' . auth()->id() . ',MessageSent' => '$refresh',
        ];
    }

    public function onMessageSent($event)
    {
        // Список обновится автоматически
    }

    public function getMessagesProperty()
    {
        return Message::where(function ($q) {
            $q->where('sender_id', auth()->id())->where('receiver_id', $this->receiverId);
        })
            ->orWhere(function ($q) {
                $q->where('sender_id', $this->receiverId)->where('receiver_id', auth()->id());
            })
            ->oldest()
            ->get();
    }

    public function send()
    {
        $this->validate(['body' => 'required']);

        $message = Message::create([
            'sender_id' => auth()->id(),
            'receiver_id' => $this->receiverId,
            'body' => $this->body,
        ]);

        broadcast(new MessageSent($message))->toOthers();
        $this->reset('body');
    }
}; ?>

<div class="flex flex-col h-[500px] bg-surface border border-slate-800 rounded-[32px] overflow-hidden">
    <div class="flex-1 overflow-y-auto p-6 space-y-4 scrollbar-hide">
        @foreach ($this->messages as $msg)
            <div class="flex {{ $msg->sender_id === auth()->id() ? 'justify-end' : 'justify-start' }}">
                <div
                    class="max-w-[80%] px-4 py-2 rounded-2xl {{ $msg->sender_id === auth()->id() ? 'bg-brand text-white rounded-tr-none' : 'bg-slate-800 text-slate-300 rounded-tl-none' }}">
                    <p class="text-sm">{{ $msg->body }}</p>
                    <span class="text-[8px] opacity-50">{{ $msg->created_at->format('H:i') }}</span>
                </div>
            </div>
        @endforeach
    </div>

    <form wire:submit="send" class="p-4 bg-bg/50 border-t border-slate-800 flex gap-2">
        <input wire:model="body" type="text" placeholder="Напишите сообщение..." class="flex-1 !bg-surface">
        <button type="submit" class="bg-brand px-6 rounded-xl font-bold italic">GO</button>
    </form>
</div>
