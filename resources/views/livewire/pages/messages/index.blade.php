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

    public function getListeners()
    {
        return [
            'echo-private:chat.' . Auth::id() . ',MessageSent' => 'onMessageReceived',
        ];
    }

    public function onMessageReceived($event)
    {
        $this->dispatch('$refresh');
        $this->dispatch('scroll-bottom');
    }

    public function getChats()
    {
        $userId = Auth::id();
        $messages = Message::where('sender_id', $userId)->orWhere('receiver_id', $userId)->get();

        $userIds = $messages->pluck('sender_id')->merge($messages->pluck('receiver_id'))->unique()->filter(fn($id) => $id != $userId);

        return User::whereIn('id', $userIds)->get();
    }

    public function selectChat($id)
    {
        $this->activeChatId = $id;
        Message::where('sender_id', $id)
            ->where('receiver_id', Auth::id())
            ->update(['is_read' => true]);

        $this->dispatch('scroll-bottom');
        $this->dispatch('refresh-counter');
    }

    public function sendMessage()
    {
        $text = trim($this->messageText);
        if (!$this->activeChatId || empty($text)) {
            return;
        }

        $message = Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $this->activeChatId,
            'body' => $text,
        ]);

        $this->messageText = '';

        try {
            broadcast(new MessageSent($message))->toOthers();
        } catch (\Exception $e) {
        }

        $this->dispatch('scroll-bottom');
    }

    public function getMessages()
    {
        if (!$this->activeChatId) {
            return [];
        }
        return Message::query()
            ->where(function ($q) {
                $q->where('sender_id', Auth::id())->where('receiver_id', $this->activeChatId);
            })
            ->orWhere(function ($q) {
                $q->where('sender_id', $this->activeChatId)->where('receiver_id', Auth::id());
            })
            ->oldest()
            ->get();
    }
}; ?>

<div x-data="{ sidebarOpen: true }"
    class="flex flex-col lg:flex-row gap-4 h-[calc(100vh-150px)] max-h-[700px] w-full overflow-hidden relative italic">

    <!-- Сайдбар чатов -->
    <div x-show="sidebarOpen"
        class="w-full lg:w-80 lg:flex-shrink-0 bg-surface border border-slate-800 rounded-3xl overflow-hidden flex flex-col shadow-2xl h-64 lg:h-full z-40">

        <div class="p-6 border-b border-slate-800 flex justify-between items-center">
            <h2 class="text-xl font-black text-brand uppercase tracking-tighter">ЧАТЫ</h2>
            <button @click="sidebarOpen = false" class="lg:hidden text-slate-500">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M6 18L18 6M6 6l12 12" stroke-width="3" />
                </svg>
            </button>
        </div>

        <div class="flex-1 overflow-y-auto custom-scrollbar">
            @forelse($this->getChats() as $user)
                <button wire:click="selectChat({{ $user->id }})"
                    class="w-full p-4 text-left hover:bg-slate-800/50 transition-all border-b border-slate-800/30 flex items-center gap-3 {{ $activeChatId == $user->id ? 'bg-brand/10 border-r-4 border-brand' : '' }}">
                    <div
                        class="w-10 h-10 flex-shrink-0 rounded-full bg-slate-800 flex items-center justify-center font-black text-brand border border-slate-700 uppercase text-xs">
                        {{ substr($user->name, 0, 2) }}
                    </div>
                    <div class="min-w-0">
                        <p
                            class="text-sm font-black truncate {{ $activeChatId == $user->id ? 'text-brand' : 'text-slate-200' }}">
                            {{ $user->name }}
                        </p>
                    </div>
                </button>
            @empty
                <div class="p-8 text-center text-slate-600 text-sm font-black uppercase opacity-20 tracking-widest">
                    Список пуст</div>
            @endforelse
        </div>
    </div>

    <!-- Окно сообщений -->
    <div
        class="flex-1 min-w-0 bg-surface border border-slate-800 rounded-3xl flex flex-col overflow-hidden shadow-2xl relative">

        <button x-show="!sidebarOpen" x-cloak @click="sidebarOpen = true"
            class="absolute top-4 left-4 z-50 bg-slate-800 text-brand p-2 rounded-xl border border-slate-700 shadow-xl hover:scale-110 transition-transform">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-width="3" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>

        @if ($activeChatId)
            <div x-data="{ scrollToBottom() { $el.scrollTo({ top: $el.scrollHeight, behavior: 'smooth' }) } }" x-init="scrollToBottom()" @scroll-bottom.window="scrollToBottom()"
                class="flex-1 overflow-y-auto p-6 space-y-4 flex flex-col custom-scrollbar">

                @foreach ($this->getMessages() as $msg)
                    <div class="flex {{ $msg->sender_id == auth()->id() ? 'justify-end' : 'justify-start' }}">
                        <div
                            class="flex flex-col {{ $msg->sender_id == auth()->id() ? 'items-end' : 'items-start' }} max-w-[75%]">
                            <div
                                class="px-5 py-3 rounded-2xl {{ $msg->sender_id == auth()->id() ? 'bg-brand text-slate-950 font-black rounded-tr-none shadow-lg shadow-brand/20' : 'bg-slate-800 text-slate-200 rounded-tl-none border border-slate-700' }}">
                                <span class="break-words leading-relaxed">{{ $msg->body }}</span>
                            </div>
                            <span
                                class="text-[10px] text-slate-600 mt-1 font-black uppercase tracking-tighter">{{ $msg->created_at->format('H:i') }}</span>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="p-4 bg-bg/50 backdrop-blur-md border-t border-slate-800">
                <div class="flex items-stretch gap-3">
                    <input wire:model="messageText" wire:keydown.enter="sendMessage" type="text"
                        placeholder="Напишите мастеру..."
                        class="flex-1 min-w-0 bg-slate-950 border border-slate-800 rounded-2xl px-6 py-4 outline-none focus:border-brand text-slate-200 transition-all font-medium">
                    <button type="button" wire:click="sendMessage"
                        class="bg-brand text-slate-950 px-8 rounded-2xl font-black uppercase shadow-lg shadow-brand/20 hover:scale-[1.02] active:scale-95 transition-all">
                        ОТПРАВИТЬ
                    </button>
                </div>
            </div>
        @else
            <div
                class="flex-1 flex items-center justify-center text-slate-700 uppercase font-black opacity-20 text-xl tracking-widest">
                Выберите чат
            </div>
        @endif
    </div>
</div>
