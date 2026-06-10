<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\SettingsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RentalOrderController;
use App\Http\Controllers\RentalOrderDailyEndpointController;
use Illuminate\Support\Facades\Route;

// --------------------
// Default entry
// --------------------
Route::get('/', [HomeController::class, 'index'])
    ->name('home')
    ->middleware('auth');

Route::middleware(['auth', 'employee'])->group(function () {
    // Gebruikersbeheer (alleen admin)
    Route::middleware('can:admin')->group(function () {
        Route::get('/users', [DashboardController::class, 'users'])->name('users.index');
        Route::delete('/users/{id}', [DashboardController::class, 'destroyUser'])->name('users.destroy');
        Route::get('/register', [DashboardController::class, 'showRegisterForm'])->name('register.form');
        Route::post('/register', [RegisterController::class, 'register'])->name('register');
    });

    // --------------------
    // Rental orders
    // --------------------
    Route::get('/rental-orders', [RentalOrderController::class, 'index'])->name('rental-orders.index');
    Route::get('/rental-orders/create', [RentalOrderController::class, 'create'])->name('rental-orders.create');
    Route::post('/rental-orders', [RentalOrderController::class, 'store'])->name('rental-orders.store');
    Route::get('/rental-orders/{rentalOrder}', [RentalOrderController::class, 'show'])->name('rental-orders.show');
    Route::get('/rental-orders/{rentalOrder}/print', [RentalOrderController::class, 'print'])->name('rental-orders.print');
    Route::get('/rental-orders/{rentalOrder}/edit', [RentalOrderController::class, 'edit'])->name('rental-orders.edit');
    Route::put('/rental-orders/{rentalOrder}', [RentalOrderController::class, 'update'])->name('rental-orders.update');
    Route::delete('/rental-orders/{rentalOrder}', [RentalOrderController::class, 'destroy'])->name('rental-orders.destroy');

    Route::get('/rental-orders/{rentalOrder}/attachments/{attachment}', [RentalOrderController::class, 'attachment'])
        ->name('rental-orders.attachments.show');

    Route::post('/rental-orders/{rentalOrder}/mail-logs/{mailLog}/resend', [RentalOrderController::class, 'resendMail'])
        ->name('rental-orders.mail-logs.resend');
});

// --------------------
// Login
// --------------------
Route::get('/login', [LoginController::class, 'showLoginForm'])
    ->name('login.form')
    ->middleware('guest');

Route::post('/login', [LoginController::class, 'login'])
    ->name('login')
    ->middleware('guest');

// --------------------
// Logout
// --------------------
Route::post('/logout', [LogoutController::class, 'logout'])
    ->name('logout')
    ->middleware('auth');

// --------------------
// Register
// --------------------
// Publieke registratie is uitgeschakeld; alleen admins kunnen gebruikers toevoegen.
// (Admin route /register staat bovenin binnen auth+can:admin.)

// --------------------
// Email verification
// --------------------
Route::get('/verify-email/{token}', [EmailVerificationController::class, 'verify'])
    ->name('verification.verify')
    ->middleware('guest');

Route::get('/email/resend', [EmailVerificationController::class, 'showResendForm'])
    ->name('verification.resend.form')
    ->middleware('guest');

Route::post('/email/resend', [EmailVerificationController::class, 'resend'])
    ->name('verification.resend')
    ->middleware('guest');

// --------------------
// Password reset
// --------------------
Route::get('/forgot-password', [ForgotPasswordController::class, 'showForgotForm'])
    ->name('password.forgot.form')
    ->middleware('guest');

Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLink'])
    ->name('password.forgot')
    ->middleware('guest');

Route::get('/reset-password/{token}', [ForgotPasswordController::class, 'showResetForm'])
    ->name('password.reset.form')
    ->middleware('guest');

Route::post('/reset-password', [ForgotPasswordController::class, 'resetPassword'])
    ->name('password.reset')
    ->middleware('guest');

// --------------------
// User settings
// --------------------
Route::get('/settings', [SettingsController::class, 'showSettings'])
    ->name('settings.show')
    ->middleware('auth');

Route::put('/settings/profile', [SettingsController::class, 'updateProfile'])
    ->name('settings.updateProfile')
    ->middleware('auth');

Route::put('/settings/password', [SettingsController::class, 'updatePassword'])
    ->name('settings.updatePassword')
    ->middleware('auth');

// --------------------
// Daily endpoint (token protected)
// --------------------
Route::get('/daily/{token}/rental-orders', RentalOrderDailyEndpointController::class)->name('daily.rental-orders');

