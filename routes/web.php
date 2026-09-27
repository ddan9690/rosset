<?php

use App\Http\Controllers\KcbWebhookController;
use App\Livewire\Admin\Dashboard;
use App\Livewire\Admin\BenevolenceCategories;
use App\Livewire\Admin\BenevolenceCases\Index;
use App\Livewire\Admin\BenevolenceCases\Create;
use App\Livewire\Admin\BenevolenceCases\Edit;
use App\Livewire\Admin\BenevolenceCases\Show;
use App\Livewire\Admin\MemberOnboard;
use App\Livewire\Admin\Settings;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use App\Livewire\BenevolenceContribution;
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

// Benevolence Contribution Route using case ID
Route::get('/portal/benevolence/contribute/{id}', BenevolenceContribution::class)->name('benevolence.contribute');

// Admin Routes
Route::get('/dashboard', Dashboard::class)->name('admin.dashboard');
Route::get('/admin/benevolence/categories', BenevolenceCategories::class)->name('admin.benevolence.categories');

// Benevolence Cases Management Routes
Route::get('/admin/benevolence/cases', Index::class)->name('admin.benevolence.cases.index');
Route::get('/admin/benevolence/cases/create', Create::class)->name('admin.benevolence.cases.create');
Route::get('/admin/benevolence/cases/{id}/{slug}/edit', Edit::class)->name('admin.benevolence.cases.edit');
Route::get('/admin/benevolence/cases/{id}/{slug}', Show::class)->name('admin.benevolence.cases.show');

Route::get('/admin/members/onboard', MemberOnboard::class)->name('admin.members.onboard');
Route::get('/admin/settings', Settings::class)->name('admin.settings');