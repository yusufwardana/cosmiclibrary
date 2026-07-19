<?php

declare(strict_types=1);

use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\BorrowController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FineController;
use App\Http\Controllers\Installer\AdminAccountController;
use App\Http\Controllers\Installer\ConfirmController;
use App\Http\Controllers\Installer\DatabaseController;
use App\Http\Controllers\Installer\LicenseController;
use App\Http\Controllers\Installer\RequirementsController;
use App\Http\Controllers\Installer\SchoolController;
use App\Http\Controllers\Installer\SmtpController;
use App\Http\Controllers\Installer\WelcomeController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\ThemeController;
use App\Http\Middleware\RedirectIfInstalled;
use App\Http\Middleware\RedirectIfNotInstalled;
use Illuminate\Support\Facades\Route;

/*
 |---------------------------------------------------------------------------
 | Web Routes
 |---------------------------------------------------------------------------
 | Single root route for the application.
 */

Route::get('/', function () {
    return view('welcome');
});

/*
 |---------------------------------------------------------------------------
 | Installer routes – guarded by middleware.
 |---------------------------------------------------------------------------
 */

Route::middleware([RedirectIfInstalled::class])->prefix(config('installer.route_prefix'))->group(function () {
    Route::get('/', [WelcomeController::class, 'index'])->name('installer.welcome');
    Route::get('/license', [LicenseController::class, 'index'])->name('installer.license');
    Route::post('/license', [LicenseController::class, 'accept'])->name('installer.license.accept');
    Route::get('/requirements', [RequirementsController::class, 'index'])->name('installer.requirements');
    Route::post('/requirements', [RequirementsController::class, 'verify'])->name('installer.requirements.verify');
    Route::get('/database', [DatabaseController::class, 'index'])->name('installer.database');
    Route::post('/database', [DatabaseController::class, 'test'])->name('installer.database.test');
    Route::get('/admin', [AdminAccountController::class, 'index'])->name('installer.admin');
    Route::post('/admin', [AdminAccountController::class, 'store'])->name('installer.admin.store');
    Route::get('/school', [SchoolController::class, 'index'])->name('installer.school');
    Route::post('/school', [SchoolController::class, 'store'])->name('installer.school.store');
    Route::get('/smtp', [SmtpController::class, 'index'])->name('installer.smtp');
    Route::post('/smtp', [SmtpController::class, 'store'])->name('installer.smtp.store');
    Route::get('/confirm', [ConfirmController::class, 'index'])->name('installer.confirm');
    Route::post('/run', [ConfirmController::class, 'run'])->name('installer.run');
});

/*
 |---------------------------------------------------------------------------
 | Protected routes – accessible only after installation.
 |---------------------------------------------------------------------------
 */

Route::middleware([RedirectIfNotInstalled::class])->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('auth.login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/register', [RegisterController::class, 'showRegisterForm'])->name('auth.register');
    Route::post('/register', [RegisterController::class, 'register']);
    Route::post('/logout', [LoginController::class, 'logout'])->name('auth.logout');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/themes', [ThemeController::class, 'index'])->name('theme.index');
    Route::post('/themes/{theme}/activate', [ThemeController::class, 'activate'])->name('theme.activate');

    Route::get('/modules', [ModuleController::class, 'index'])->name('module.index');
    Route::post('/modules/{module}/enable', [ModuleController::class, 'enable'])->name('module.enable');
    Route::post('/modules/{module}/disable', [ModuleController::class, 'disable'])->name('module.disable');

    Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics.index');

    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');

    /* -----------------------------------
       Library Module Routes
       ----------------------------------- */
    Route::prefix('library')->middleware('auth')->group(function () {
        Route::resource('books', BookController::class);
        Route::resource('members', MemberController::class);
        Route::resource('borrows', BorrowController::class)->only([
            'index', 'create', 'store', 'show',
        ]);
        Route::get('borrows/{borrow}/return', [BorrowController::class, 'returnForm'])
            ->name('borrows.return.form');
        Route::put('borrows/{borrow}/return', [BorrowController::class, 'returnProcess'])
            ->name('borrows.return.process');

        Route::get('borrows/{borrow}/extend', [BorrowController::class, 'extendForm'])
            ->name('borrows.extend.form');
        Route::put('borrows/{borrow}/extend', [BorrowController::class, 'extendProcess'])
            ->name('borrows.extend.process');

        Route::resource('fines', FineController::class)->only(['index', 'show']);
        Route::post('fines/{fine}/pay', [FineController::class, 'pay'])->name('fines.pay');
        Route::post('fines/{fine}/waive', [FineController::class, 'waive'])->name('fines.waive');

        Route::resource('reservations', ReservationController::class)->only(['index', 'create', 'store', 'show']);
        Route::post('reservations/{reservation}/cancel', [ReservationController::class, 'cancel'])->name('reservations.cancel');

        Route::resource('categories', CategoryController::class);
    });
});
