<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\OrganizerController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\StorageController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('index');

Route::get('/401', function () {
    return view('401');
});

Route::get('/images/{filename}', [StorageController::class, 'index'])->name('image.show');

Route::get('/login', [AuthController::class, 'login_'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');

Route::get('/register', [AuthController::class, 'register_'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.submit');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/event/{id}', [EventController::class, 'show'])->name('event.show');

Route::prefix('/')->group(function () {
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/item/{cartItemId}/remove', [CartController::class, 'removeItem'])->name('cart.item.remove');
    Route::post('/cart/clear', [CartController::class, 'clearCart'])->name('cart.clear');

    // Rute untuk Checkout dan Proses Pembayaran dari Keranjang
    Route::get('/checkout', [PaymentController::class, 'checkoutPage'])->name('checkout.page');
    Route::post('/checkout/process', [PaymentController::class, 'processCartCheckout'])->name('checkout.process');
});

// Cart Routes
Route::prefix('cart')->name('cart.')->group(function () { // Pastikan middleware auth Laravel Anda benar
    Route::get('/', [CartController::class, 'index'])->name('index');
    // Menggunakan POST untuk delete karena form HTML standar tidak mendukung DELETE secara langsung tanpa JS
    Route::post('/cart/item/{cartItemId}/remove', [CartController::class, 'removeItem'])->name('item.remove');
    Route::post('/cart/clear', [CartController::class, 'clearCart'])->name('clear');
    // Anda bisa menggunakan Route::delete jika Anda akan menggunakan JavaScript untuk mengirim request DELETE
});

Route::middleware('role')->prefix('organizer')->name('organizer.')->group(function () {
    Route::get('/', [OrganizerController::class, 'index'])->name('index');
    Route::get('/events', [OrganizerController::class, 'events'])->name('events.index');
    Route::get('/events/create', [OrganizerController::class, 'createEvent'])->name('events.create');
    Route::post('/events', [OrganizerController::class, 'storeEvent'])->name('events.store');
    Route::get('/events/{event}', [OrganizerController::class, 'showEvent'])->name('events.show');
    Route::get('/events/{event}/edit', [OrganizerController::class, 'editEvent'])->name('events.edit');
    Route::put('/events/{event}', [OrganizerController::class, 'updateEvent'])->name('events.update');
    Route::delete('/events/{event}', [OrganizerController::class, 'destroyEvent'])->name('events.destroy');
});

Route::middleware('role')->prefix('finance')->name('finance.')->group(function () {
    Route::get('/', [FinanceController::class, 'index'])->name('index');
    Route::patch('/approve/{id}', [FinanceController::class, 'approvePayment'])->name('approve-payment');
    Route::patch('/reject/{id}', [FinanceController::class, 'rejectPayment'])->name('reject-payment');
});

Route::middleware('role')->prefix('member')->name('member.')->group(function () {
    Route::get('/', [RegistrationController::class, 'showMyTickets'])->name('tickets');
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
