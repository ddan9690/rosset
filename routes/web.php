<?php

use App\Http\Controllers\KcbWebhookController;
use App\Livewire\Admin\Dashboard;
use App\Livewire\Admin\BenevolenceCategories;
use App\Livewire\Admin\MemberOnboard;
use App\Livewire\Admin\Settings;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use App\Livewire\Frontend\Home;
use App\Livewire\Frontend\Updates;
use App\Livewire\MemberProfile;
use App\Livewire\Portal;
use App\Livewire\RegistrationFee;
use App\Livewire\SolidarityFund;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', Home::class)->name('home');
Route::get('/updates', Updates::class)->name('updates');

Route::get('/login', Login::class)->name('login');
Route::get('/register', Register::class)->name('register');

Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();

    return redirect('/');
})->name('logout');

Route::post('/kcb/ipn', [KcbWebhookController::class, 'handle']);

Route::get('/register/fee', RegistrationFee::class)->name('register.fee');
Route::get('/portal', Portal::class)->name('portal');
Route::get('/portal/solidarity', SolidarityFund::class)->name('member.solidarity');
Route::get('/member/profile', MemberProfile::class)->name('profile.update');

// Admin Routes
Route::get('/dashboard', Dashboard::class)->name('admin.dashboard');
Route::get('/admin/benevolence/categories', BenevolenceCategories::class)->name('admin.benevolence.categories');
Route::get('/admin/members/onboard', MemberOnboard::class)->name('admin.members.onboard');
Route::get('/admin/settings', Settings::class)->name('admin.settings');