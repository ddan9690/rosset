<?php

use App\Http\Controllers\KcbWebhookController;
use App\Http\Controllers\PDF\MemberContributionsPdfController;
use App\Http\Middleware\UpdateUserLastActive;
use App\Livewire\Admin\BenevolenceCases\Create as BenevolenceCaseCreate;
use App\Livewire\Admin\BenevolenceCases\Edit as BenevolenceCaseEdit;
use App\Livewire\Admin\BenevolenceCases\Index as BenevolenceCaseIndex;
use App\Livewire\Admin\BenevolenceCases\Show as BenevolenceCaseShow;
use App\Livewire\Admin\BenevolenceCategories;
use App\Livewire\Admin\Dashboard;
use App\Livewire\Admin\MemberOnboard;
use App\Livewire\Admin\Members\Create as MemberCreate;
use App\Livewire\Admin\Members\Edit as MemberEdit;
use App\Livewire\Admin\Members\Index as MemberIndex;
use App\Livewire\Admin\Members\Show as MemberShow;
use App\Livewire\Admin\Settings;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use App\Livewire\BenevolenceContribution;
use App\Livewire\Frontend\Home;
use App\Livewire\Frontend\Updates;
use App\Livewire\MemberProfile;
use App\Livewire\PDF\MemberContributionsPdf;
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

Route::post('/kcb/ipn', [KcbWebhookController::class, 'handle'])
    ->withoutMiddleware([
        \Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class,
    ])
    ->name('kcb.ipn');

// Authenticated Routes with Activity Tracking Middleware
Route::middleware(['auth', UpdateUserLastActive::class])->group(function () {
    Route::get('/register/fee', RegistrationFee::class)->name('register.fee');
    Route::get('/portal', Portal::class)->name('portal');
    Route::get('/portal/solidarity', SolidarityFund::class)->name('member.solidarity');
    Route::get('/member/profile', MemberProfile::class)->name('profile.update');

    // Benevolence Contribution Route using case ID
    Route::get('/portal/benevolence/contribute/{id}', BenevolenceContribution::class)->name('benevolence.contribute');

    // Member PDF Reports Group
    Route::prefix('portal/pdf')->name('portal.pdf.')->group(function () {
        Route::get('/contributions', [MemberContributionsPdfController::class, 'download'])->name('mycontribtiondowlaod');
    });

    // Admin Routes
    Route::get('/dashboard', Dashboard::class)->name('admin.dashboard');
    Route::get('/admin/benevolence/categories', BenevolenceCategories::class)->name('admin.benevolence.categories');

    // Members Management Routes (CRUD Architecture)
    Route::get('/admin/members', MemberIndex::class)->name('admin.members');
    Route::get('/admin/members/create', MemberCreate::class)->name('admin.members.create');
    Route::get('/admin/members/{id}', MemberShow::class)->name('admin.members.show');
    Route::get('/admin/members/{id}/edit', MemberEdit::class)->name('admin.members.edit');

    // Benevolence Cases Management Routes
    Route::get('/admin/benevolence/cases', BenevolenceCaseIndex::class)->name('admin.benevolence.cases.index');
    Route::get('/admin/benevolence/cases/create', BenevolenceCaseCreate::class)->name('admin.benevolence.cases.create');
    Route::get('/admin/benevolence/cases/{id}/{slug}/edit', BenevolenceCaseEdit::class)->name('admin.benevolence.cases.edit');
    Route::get('/admin/benevolence/cases/{id}/{slug}', BenevolenceCaseShow::class)->name('admin.benevolence.cases.show');

    Route::get('/admin/members/onboard', MemberOnboard::class)->name('admin.members.onboard');
    Route::get('/admin/settings', Settings::class)->name('admin.settings');
});