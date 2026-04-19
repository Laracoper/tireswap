<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use App\Models\User;
use App\Models\Message;
use App\Events\MessageSent;
use Illuminate\Support\Facades\Auth;

new #[Layout('components.layouts.app')] class extends Component {
    public $activeChatId = null;
    public $messageText = '';

    // Слушатель для Real-time
    public function getListeners()
    {
        $userId = Auth::id();
        return [
            "echo-private:chat.{$userId},MessageSent" => '$refresh',
        ];
    }

    // Упрощенный список чатов: берем всех, кому мы писали или кто писал нам
    public function getChats()
    {
        $userId = Auth::id();
        
        $senderIds = Message::where('receiver_id', $userId)->pluck('sender_id')->toArray();
        $receiverIds = Message::where('sender_id', $userId)->pluck('receiver_id')->toArray();
        
        $allIds = array_unique(array_merge($senderIds, $receiverIds));

        return User::whereIn('id', $allIds)->get();
    }

    public function selectChat($id)
    {
        $this->activeChatId = $id;
        Message::where('sender_id', $id)->where('receiver_id', Auth::id())->update(['is_read' => true]);
    }

    public function sendMessage()
    {
        if (!$this->activeChatId || empty(trim($this->messageText))) return;

        $message = Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $this->activeChatId,
            'body' => $this->messageText,
        ]);

        // Отправка в сокеты (если Reverb настроен)
        try {
            broadcast(new MessageSent($message))->toOthers();
        } catch (\Exception $e) {
            // Если сокеты не запущены, просто игнорируем ошибку
        }

        $this->reset('messageText');
    }

    public function getMessages()
    {
        if (!$this->activeChatId) return [];
        
        return Message::where(function($q) {
                $q->where('sender_id', Auth::id())->where('receiver_id', $this->activeChatId);
            })->orWhere(function($q) {
                $q->where('sender_id', $this->activeChatId)->where('receiver_id', Auth::id());
            })
            ->oldest()
            ->get();
    }
}; ?>



<div class="flex flex-col md:flex-row gap-4 h-[600px]">
    <!-- Список пользователей (упрощенный для теста) -->
    <div class="w-full md:w-64 bg-slate-900 border border-slate-800 rounded-2xl overflow-hidden flex flex-col">
        <div class="p-4 border-b border-slate-800 text-[10px] font-black uppercase text-blue-500">Доступные мастера</div>
        <div class="flex-1 overflow-y-auto">
            @foreach($this->getChats() as $user)
                <button wire:click="selectChat({{ $user->id }})" 
                        class="w-full p-4 text-left hover:bg-slate-800 transition-colors border-b border-slate-800/50 {{ $activeChatId == $user->id ? 'bg-blue-500/10 border-r-2 border-blue-500' : '' }}">
                    <p class="text-sm font-bold">{{ $user->name }}</p>
                </button>
            @endforeach
        </div>
    </div>

    <!-- Окно чата -->
    <div class="flex-1 bg-slate-900 border border-slate-800 rounded-2xl flex flex-col overflow-hidden">
        @if($activeChatId)
            <div class="flex-1 overflow-y-auto p-4 space-y-3 flex flex-col-reverse">
                @foreach($this->getMessages() as $msg)
                    <div class="flex {{ $msg->sender_id == auth()->id() ? 'justify-end' : 'justify-start' }}">
                        <div class="max-w-[80%] px-4 py-2 rounded-xl {{ $msg->sender_id == auth()->id() ? 'bg-blue-600 text-white' : 'bg-slate-800 text-slate-300' }}">
                            {{ $msg->body }}
                        </div>
                    </div>
                @endforeach
            </div>

            <form wire:submit.prevent="sendMessage" class="p-4 border-t border-slate-800 flex gap-2">
                <input wire:model="messageText" type="text" placeholder="Напишите сообщение..." class="flex-1 bg-slate-950 border-slate-800 rounded-xl px-4 py-2 outline-none">
                <button type="submit" class="bg-blue-600 px-6 rounded-xl font-bold">ОТПРАВИТЬ</button>
            </form>
        @else
            <div class="flex-1 flex items-center justify-center text-slate-600 italic text-sm">
                Выберите мастера из списка слева
            </div>
        @endif
    </div>
</div>
