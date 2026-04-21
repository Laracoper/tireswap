<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

// 1. Публичные роуты
// Route::view('/', 'welcome');
Route::get('/', function () {
    return view('welcome');
})->name('home');
// В файле routes/web.php
// Volt::route('/', 'welcome')->name('home');
// Route::view('/', 'welcome')->name('home');



Volt::route('/wheels', 'pages.wheels.index')->name('wheels.index');

// 2. Только для гостей
Route::middleware('guest')->group(function () {
    Volt::route('/login', 'pages.auth.login')->name('login');
    Volt::route('/register', 'pages.auth.register')->name('register');
});

// 3. Только для авторизованных
Route::middleware('auth')->group(function () {
    Route::view('/dashboard', 'dashboard')->name('dashboard');
    
    // Сначала статичные пути
    Volt::route('/wheels/create', 'pages.wheels.create')->name('wheels.create');
    // Volt::route('/messages', 'pages.messages.index')->name('messages.index');
    // Volt::route('/messages', 'msg')->name('messages.index');
    Volt::route('/test', 'test');
    // Volt::route('/messages', 'chat')->name('messages.index');
    Volt::route('/messages', 'pages.messages.index')->name('messages.index');


    
    // Затем пути с параметрами (ID/Slug) внутри auth
    Volt::route('/wheels/{wheel}/edit', 'pages.wheels.edit')->name('wheels.edit');

    Route::post('/logout', function () {
        auth()->logout();
        return redirect('/');
    })->name('logout');
});

// 4. Публичный просмотр (в самом низу, чтобы не мешать /create и /edit)
Volt::route('/wheels/{wheel}', 'pages.wheels.show')->name('wheels.show');
