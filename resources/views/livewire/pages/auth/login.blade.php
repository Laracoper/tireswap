<?php
use Livewire\Volt\Component;
use Livewire\Attributes\Layout;

new #[Layout('components.layouts.auth')] class extends Component {
    public $email = '';
    public $password = '';

    public function login() {
        $credentials = $this->validate(['email' => 'required|email', 'password' => 'required']);
        if (auth()->attempt($credentials)) return redirect()->intended('/dashboard');
        $this->addError('email', 'Ошибка входа.');
    }
}; ?>

<div class="max-w-md w-full bg-surface p-8 rounded-3xl border border-slate-800 shadow-2xl">
    <h1 class="text-3xl font-black text-brand mb-6 text-center italic">ВХОД</h1>
    <form wire:submit="login" class="space-y-4">
        <input type="email" wire:model="email" placeholder="Email">
        <input type="password" wire:model="password" placeholder="Пароль">
        <button type="submit" class="w-full bg-brand py-4 rounded-xl font-bold">ВОЙТИ</button>
    </form>
</div>
