<?php
use Livewire\Volt\Component;
use App\Models\Message;
use App\Models\User;
use App\Events\MessageSent;
use Livewire\Attributes\Layout;

new #[Layout('components.layouts.app')] class extends Component {
    public $activeChatId = null;
    public $body = '';

    public function getListeners() {
        return ["echo-private:chat." . auth()->id() . ",MessageSent" => '$refresh'];
    }

    // Получаем список людей, с которыми есть переписка
    public function getChatsProperty() {
        return User::whereIn('id', function($query) {
            $query->select('receiver_id')->from('messages')->where('sender_id', auth()->id())
                  ->union($query->select('sender_id')->from('messages')->where('receiver_id', auth()->id()));
        })->get();
    }

    public function selectChat($userId) {
        $this->activeChatId = $userId;
        Message::where('sender_id', $userId)->where('receiver_id', auth()->id())->update(['is_read' => true]);
    }

    public function getMessagesProperty() {
        if (!$this->activeChatId) return [];
        return Message::where(function($q) {
            $q->where('sender_id', auth()->id())->where('receiver_id', $this->activeChatId);
        })->orWhere(function($q) {
            $q->where('sender_id', $this->activeChatId)->where('receiver_id', auth()->id());
        })->oldest()->get();
    }

    public function send() {
        $this->validate(['body' => 'required']);
        $message = Message::create([
            'sender_id' => auth()->id(),
            'receiver_id' => $this->activeChatId,
            'body' => $this->body,
        ]);
        broadcast(new MessageSent($message))->toOthers();
        $this->reset('body');
    }
}; ?>

<div class="h-[calc(100vh-160px)] flex flex-col md:flex-row gap-6">
    <!-- Список чатов -->
    <div class="w-full md:w-80 bg-surface border border-slate-800 rounded-[32px] flex flex-col overflow-hidden">
        <div class="p-6 border-b border-slate-800"><h2 class="text-xl font-black italic">ЧАТЫ</h2></div>
        <div class="flex-1 overflow-y-auto">
            @foreach($this->chats as $chat)
                <button wire:click="selectChat({{ $chat->id }})" 
                        class="w-full p-4 flex items-center gap-3 hover:bg-slate-800 transition-colors {{ $activeChatId === $chat->id ? 'bg-brand/10 border-r-4 border-brand' : '' }}">
                    <div class="w-10 h-10 bg-slate-700 rounded-full flex items-center justify-center text-xs">👤</div>
                    <div class="text-left truncate">
                        <p class="font-bold text-sm">{{ $chat->name }}</p>
                        <p class="text-[10px] text-slate-500 truncate">Нажмите, чтобы открыть</p>
                    </div>
                </button>
            @endforeach
        </div>
    </div>

    <!-- Окно сообщения -->
    <div class="flex-1 bg-surface border border-slate-800 rounded-[32px] flex flex-col overflow-hidden">
        @if($activeChatId)
            <div class="flex-1 overflow-y-auto p-6 space-y-4">
                @foreach($this->messages as $msg)
                    <div class="flex {{ $msg->sender_id === auth()->id() ? 'justify-end' : 'justify-start' }}">
                        <div class="max-w-[70%] px-4 py-2 rounded-2xl {{ $msg->sender_id === auth()->id() ? 'bg-brand text-white rounded-tr-none' : 'bg-slate-800 text-slate-300 rounded-tl-none' }}">
                            <p class="text-sm">{{ $msg->body }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
            <form wire:submit="send" class="p-4 bg-bg/30 border-t border-slate-800 flex gap-2">
                <input wire:model="body" type="text" placeholder="Ваше сообщение..." class="flex-1">
                <button type="submit" class="bg-brand px-6 rounded-xl font-bold">ОТПРАВИТЬ</button>
            </form>
        @else
            <div class="flex-1 flex flex-col items-center justify-center text-slate-600 italic">
                <span class="text-6xl mb-4">💬</span>
                <p>Выберите чат для начала общения</p>
            </div>
        @endif
    </div>
</div>

