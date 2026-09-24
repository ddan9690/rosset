<?php

use App\Livewire\Admin\Dashboard;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use App\Livewire\Frontend\Home;
use App\Livewire\Frontend\Updates;
use App\Livewire\Portal;
use Illuminate\Support\Facades\Route;

Route::get('/', Home::class)->name('home');
Route::get('/updates', Updates::class)->name('updates');

Route::get('/login', Login::class)->name('login');
Route::get('/register', Register::class)->name('register');

Route::get('/portal', Portal::class)->name('portal');
Route::get('/admin/dashboard', Dashboard::class)->name('admin.dashboard');