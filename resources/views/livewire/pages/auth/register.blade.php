<?php
use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

new #[Layout('components.layouts.auth')] class extends Component {
    public $name = '';
    public $email = '';
    public $password = '';

    public function register() {
        $data = $this->validate([
            'name' => 'required|min:2',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
        ]);

        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
        ]);

        auth()->login($user);

        return redirect('/dashboard');
    }
}; ?>

<div class="max-w-md w-full bg-surface p-8 rounded-3xl border border-slate-800 shadow-2xl">
    <h1 class="text-3xl font-black text-brand mb-6 text-center italic uppercase tracking-tighter">РЕГИСТРАЦИЯ</h1>
    
    <form wire:submit="register" class="space-y-4">
        <div>
            <input type="text" wire:model="name" placeholder="Ваше имя" class="w-full">
            @error('name') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
        </div>

        <div>
            <input type="email" wire:model="email" placeholder="Email">
            @error('email') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
        </div>

        <div>
            <input type="password" wire:model="password" placeholder="Пароль (мин. 8 символов)">
            @error('password') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
        </div>

        <button type="submit" class="w-full bg-brand py-4 rounded-xl font-black text-white hover:brightness-110 transition-all shadow-lg shadow-blue-500/20">
            СОЗДАТЬ АККАУНТ
        </button>

        <p class="text-center text-sm text-slate-500 mt-4">
            Уже есть аккаунт? <a href="/login" class="text-brand font-bold">Войти</a>
        </p>
    </form>
</div>
