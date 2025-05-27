<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('index');

Route::get('/401', function () {
    return view('401');
});

Route::get('/login', [AuthController::class, 'login_'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');

Route::get('/register', [AuthController::class, 'register_'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.submit');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/event/{id}', [EventController::class, 'show'])->name('event.show');
Route::post('/event/{eventId}/register-and-pay', [PaymentController::class, 'registerAndPay'])->name('event.register.and.pay');

Route::middleware('role')->prefix('member')->name('member.')->group(function () {
    Route::get('/', [MemberController::class, 'index'])->name('index');
});

Route::middleware('role')
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        // Dashboard
        Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('index');

        // User management
        Route::prefix('user')
            ->name('user.')
            ->group(function () {
                Route::get('/', [AdminController::class, 'index'])->name('index');
                Route::get('/create', [AdminController::class, 'create'])->name('create');
                Route::post('/', [AdminController::class, 'store'])->name('store');
                Route::get('/{id}', [AdminController::class, 'show'])->name('show');
                Route::get('/{id}/edit', [AdminController::class, 'edit'])->name('edit');
                Route::put('/{id}', [AdminController::class, 'update'])->name('update');
                Route::delete('/{id}', [AdminController::class, 'destroy'])->name('destroy');
            });
    });
