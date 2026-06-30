<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\TwoFactorController;
use App\Http\Controllers\Client\CartController;
use App\Http\Controllers\Client\OrderController;
use App\Http\Controllers\Gestionnaire\DashboardController as GestionnaireDashboardController;
use App\Http\Controllers\Gestionnaire\ProductController as GestionnaireProductController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UploadCheckController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

if (app()->environment('local')) {
    Route::match(['get', 'post'], '/dev/upload-check', UploadCheckController::class)->name('dev.upload-check');
}
Route::get('/products/search', [ProductController::class, 'search'])->name('products.search');
Route::get('/products/{product:slug}', [ProductController::class, 'show'])->name('products.show');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('rate.limited:login,10');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::get('/two-factor', [TwoFactorController::class, 'show'])->name('two-factor.show');
Route::post('/two-factor', [TwoFactorController::class, 'verify'])->name('two-factor.verify');

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/email/verify', fn () => view('auth.verify-email'))->name('verification.notice');

    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();

        return redirect()->route('home')->with('success', 'Email vérifié avec succès.');
    })->middleware(['signed'])->name('verification.verify');

    Route::post('/email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();

        return back()->with('success', 'Lien de vérification renvoyé.');
    })->middleware(['throttle:6,1'])->name('verification.send');

    Route::middleware('account.active')->group(function () {
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

        Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
        Route::post('/cart/{product}', [CartController::class, 'add'])->name('cart.add');
        Route::delete('/cart/{product}', [CartController::class, 'remove'])->name('cart.remove');

        Route::get('/checkout', [OrderController::class, 'checkoutForm'])->name('checkout');
        Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
        Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');

        Route::get('/payments/initiate/{order}', [PaymentController::class, 'initiate'])->name('payments.initiate');
        Route::post('/payments/process/{order}', [PaymentController::class, 'process'])->name('payments.process');
        Route::get('/payments/status/{payment}', [PaymentController::class, 'status'])->name('payments.status');
        Route::post('/payments/status/{payment}/confirm', [PaymentController::class, 'confirmPaid'])->name('payments.confirm');
        Route::post('/payments/simulate/{payment}', [PaymentController::class, 'simulate'])->name('payments.simulate');
        Route::get('/payments/history', [PaymentController::class, 'history'])->name('payments.history');
        Route::get('/payments/receipt/{payment}', [PaymentController::class, 'receipt'])->name('payments.receipt');
    });
});

Route::post('/payments/callback', [PaymentController::class, 'callback'])->name('payments.callback');

Route::middleware(['auth', 'account.active', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::patch('/users/{user}/toggle', [UserController::class, 'toggleActive'])->name('users.toggle');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
});

Route::middleware(['auth', 'account.active', 'role:gestionnaire,admin'])->prefix('gestionnaire')->name('gestionnaire.')->group(function () {
    Route::get('/dashboard', [GestionnaireDashboardController::class, 'index'])->name('dashboard');
    Route::get('/products', [GestionnaireProductController::class, 'index'])->name('products.index');
    Route::get('/products/create', [GestionnaireProductController::class, 'create'])->name('products.create');
    Route::post('/products', [GestionnaireProductController::class, 'store'])->name('products.store');
    Route::get('/products/{product}/edit', [GestionnaireProductController::class, 'edit'])->name('products.edit');
    Route::post('/products/{product}', [GestionnaireProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{product}', [GestionnaireProductController::class, 'destroy'])->name('products.destroy');
    Route::patch('/products/{product}/status', [GestionnaireProductController::class, 'updateStatus'])->name('products.status');
});
