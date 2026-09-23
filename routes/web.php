<?php

use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use App\Livewire\Frontend\Home;
use App\Livewire\Frontend\Updates;
use Illuminate\Support\Facades\Route;

Route::get('/', Home::class)->name('home');
Route::get('/updates', Updates::class)->name('updates');

Route::get('/login', Login::class)->name('login');
Route::get('/register', Register::class)->name('register');