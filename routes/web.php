<?php

use App\Http\Controllers\KcbWebhookController;
use App\Http\Controllers\PDF\MemberContributionsPdfController;
use App\Http\Controllers\PDF\TransactionsPdfController;
use App\Http\Middleware\UpdateUserLastActive;
use App\Livewire\Admin\BenevolenceCases\Create as BenevolenceCaseCreate;
use App\Livewire\Admin\BenevolenceCases\Edit as BenevolenceCaseEdit;
use App\Livewire\Admin\BenevolenceCases\Index as BenevolenceCaseIndex;
use App\Livewire\Admin\BenevolenceCases\Show as BenevolenceCaseShow;
use App\Livewire\Admin\BenevolenceCategories;
use App\Livewire\Admin\Dashboard;
use App\Livewire\Admin\ManageMembershipRequests;
use App\Livewire\Admin\MemberOnboard;
use App\Livewire\Admin\Members\Create as MemberCreate;
use App\Livewire\Admin\Members\Edit as MemberEdit;
use App\Livewire\Admin\Members\Index as MemberIndex;
use App\Livewire\Admin\Members\Show as MemberShow;
use App\Livewire\Admin\Roles;
use App\Livewire\Admin\Settings;
use App\Livewire\Admin\Transactions;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use App\Livewire\Auth\MembershipStatus;
use App\Livewire\BenevolenceContribution;
use App\Livewire\Frontend\Home;
use App\Livewire\Frontend\Updates;
use App\Livewire\MemberDependants;
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

Route::post('/kcb/ipn', [KcbWebhookController::class, 'handle'])
    ->withoutMiddleware([
        \Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class,
    ])
    ->name('kcb.ipn');

// Authenticated Routes with Activity Tracking Middleware
Route::middleware(['auth', UpdateUserLastActive::class])->group(function () {
    
    // Member Portal & Self-Service Routes
    Route::get('/membership/status', MembershipStatus::class)
        ->middleware('permission:view membership status')
        ->name('membership.status');

    Route::get('/register/fee', RegistrationFee::class)
        ->middleware('permission:pay registration fee')
        ->name('register.fee');

    Route::get('/portal', Portal::class)
        ->middleware('permission:access portal')
        ->name('portal');

    Route::get('/portal/solidarity', SolidarityFund::class)
        ->middleware('permission:view solidarity fund')
        ->name('member.solidarity');

    Route::get('/member/profile', MemberProfile::class)
        ->middleware('permission:update profile')
        ->name('profile.update');
    
    Route::get('/member/dependants', MemberDependants::class)
        ->middleware('permission:update dependants')
        ->name('member.dependants.update');

    Route::get('/portal/benevolence/contribute/{id}', BenevolenceContribution::class)
        ->middleware('permission:contribute benevolence')
        ->name('benevolence.contribute');

    // Member PDF Reports Group
    Route::prefix('portal/pdf')->name('portal.pdf.')->group(function () {
        Route::get('/contributions', [MemberContributionsPdfController::class, 'download'])
            ->middleware('permission:download member contribution pdfs')
            ->name('mycontribtiondowlaod');
    });

    // Admin Routes
    Route::get('/dashboard', Dashboard::class)
        ->middleware('permission:view dashboard')
        ->name('admin.dashboard');

    Route::get('/admin/benevolence/categories', BenevolenceCategories::class)
        ->middleware('permission:manage benevolence categories')
        ->name('admin.benevolence.categories');
    
    Route::get('/admin/membership-requests', ManageMembershipRequests::class)
        ->middleware('permission:manage membership requests')
        ->name('admin.membership-requests');

    // Members Management Routes (CRUD Architecture)
    Route::get('/admin/members', MemberIndex::class)
        ->middleware('permission:view members')
        ->name('admin.members');

    Route::get('/admin/members/create', MemberCreate::class)
        ->middleware('permission:create members')
        ->name('admin.members.create');

    Route::get('/admin/members/{id}', MemberShow::class)
        ->middleware('permission:show members')
        ->name('admin.members.show');

    Route::get('/admin/members/{id}/edit', MemberEdit::class)
        ->middleware('permission:edit members')
        ->name('admin.members.edit');

    // Benevolence Cases Management Routes
    Route::get('/admin/benevolence/cases', BenevolenceCaseIndex::class)
        ->middleware('permission:view benevolence cases')
        ->name('admin.benevolence.cases.index');

    Route::get('/admin/benevolence/cases/create', BenevolenceCaseCreate::class)
        ->middleware('permission:create benevolence cases')
        ->name('admin.benevolence.cases.create');

    Route::get('/admin/benevolence/cases/{id}/{slug}/edit', BenevolenceCaseEdit::class)
        ->middleware('permission:edit benevolence cases')
        ->name('admin.benevolence.cases.edit');

    Route::get('/admin/benevolence/cases/{id}/{slug}', BenevolenceCaseShow::class)
        ->middleware('permission:show benevolence cases')
        ->name('admin.benevolence.cases.show');

    // Gateway Transactions Route
    Route::get('/admin/transactions', Transactions::class)
        ->middleware('permission:view transactions')
        ->name('admin.transactions');

    // Admin PDF Reports Group
    Route::prefix('admin/pdf')->name('admin.pdf.')->group(function () {
        Route::get('/transactions', [TransactionsPdfController::class, 'download'])
            ->middleware('permission:download admin transaction pdfs')
            ->name('transactions.download');
    });

    // System Roles & Settings Routes (Restricted primarily to Super Admin)
    Route::get('/admin/roles', Roles::class)
        ->middleware('role:super admin')
        ->name('admin.roles');

    Route::get('/admin/members/onboard', MemberOnboard::class)
        ->middleware('permission:onboard members')
        ->name('admin.members.onboard');

    Route::get('/admin/settings', Settings::class)
        ->middleware('permission:manage settings')
        ->name('admin.settings');
});