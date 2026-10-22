<?php

use App\Http\Controllers\AccountStatusController;
use App\Http\Controllers\CashCheckoutController;
use App\Http\Controllers\DemoScreenController;
use App\Http\Controllers\Developer\DeveloperPanelController;
use App\Http\Controllers\OwnerScreenController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReceiptController;
use App\Http\Controllers\StockAdjustmentController;
use App\Http\Controllers\StoreMemberController;
use App\Http\Controllers\StoreOnboardingController;
use App\Http\Controllers\StoreThemeController;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->user()?->isDeveloper()) {
        return redirect()->route('developer.home');
    }

    return view('welcome');
});

Route::get('/dashboard', function () {
    $user = auth()->user();

    if ($user->isDeveloper()) {
        return redirect()->route('developer.home');
    }

    if ($user->ownedStores()->doesntExist()) {
        if ($user->storeMemberships()->whereIn('role', ['admin', 'cashier'])->exists()) {
            return redirect()->route('owner.pos');
        }
    }

    return view('dashboard', [
        'store' => $user->ownedStores()->first() ?? active_store(),
        'previewMode' => false,
    ]);
})->middleware(['auth', 'verified', 'account.approved'])->name('dashboard');

Route::get('/akun/status', [AccountStatusController::class, 'show'])
    ->middleware('auth')
    ->name('account.status');

Route::get('/struk/{store}/{order}/{token}', ReceiptController::class)
    ->middleware('signed')
    ->name('receipts.show');

Route::middleware(['auth', 'verified', 'account.approved'])->group(function () {
    Route::get('/onboarding/store', [StoreOnboardingController::class, 'create'])->name('onboarding.create');
    Route::post('/onboarding/store', [StoreOnboardingController::class, 'store'])->name('onboarding.store');

    Route::get('/kasir', [OwnerScreenController::class, 'show'])->defaults('screen', 'pos')->name('owner.pos');
    Route::post('/kasir/tunai', [CashCheckoutController::class, 'store'])->name('owner.checkout.cash');
    Route::get('/produk', [OwnerScreenController::class, 'show'])->defaults('screen', 'products')->name('owner.products');
    Route::get('/produk/buat', [ProductController::class, 'create'])->name('owner.products.create');
    Route::post('/produk', [ProductController::class, 'store'])->name('owner.products.store');
    Route::get('/kategori', [OwnerScreenController::class, 'show'])->defaults('screen', 'categories')->name('owner.categories');
    Route::get('/stok/penyesuaian', [OwnerScreenController::class, 'show'])->defaults('screen', 'stock-adjustment')->name('owner.stock-adjustment');
    Route::post('/stok/penyesuaian', [StockAdjustmentController::class, 'store'])->name('owner.stock-adjustment.store');
    Route::get('/stok/riwayat', [OwnerScreenController::class, 'show'])->defaults('screen', 'stock-history')->name('owner.stock-history');
    Route::get('/transaksi', [OwnerScreenController::class, 'show'])->defaults('screen', 'orders')->name('owner.orders');
    Route::get('/aktivitas', [OwnerScreenController::class, 'show'])->defaults('screen', 'activity')->name('owner.activity');
    Route::get('/laporan', [OwnerScreenController::class, 'show'])->defaults('screen', 'report')->name('owner.reports');
    Route::get('/pengaturan/toko', [OwnerScreenController::class, 'show'])->defaults('screen', 'store-profile')->name('owner.store-profile');
    Route::get('/pengaturan/kasir', [OwnerScreenController::class, 'show'])->defaults('screen', 'members')->name('owner.members');
    Route::post('/pengaturan/kasir', [StoreMemberController::class, 'store'])->name('owner.members.store');
    Route::patch('/pengaturan/kasir/{member}', [StoreMemberController::class, 'update'])->name('owner.members.update');
    Route::get('/pengaturan/tampilan', [OwnerScreenController::class, 'show'])->defaults('screen', 'appearance')->name('owner.appearance');
    Route::put('/pengaturan/tampilan', [StoreThemeController::class, 'update'])->name('owner.appearance.update');
    Route::get('/pengaturan/pembayaran', [OwnerScreenController::class, 'show'])->defaults('screen', 'payments')->name('owner.payment-settings');
    Route::get('/lainnya', [OwnerScreenController::class, 'show'])->defaults('screen', 'more')->name('owner.more');
});

Route::middleware(['auth', 'account.approved'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware('developer')
    ->prefix('developer')
    ->name('developer.')
    ->group(function () {
        Route::get('/', [DeveloperPanelController::class, 'home'])->name('home');
        Route::get('/registrations', [DeveloperPanelController::class, 'registrations'])->name('registrations');
        Route::get('/registrations/{user}', [DeveloperPanelController::class, 'registration'])->name('registrations.show');
        Route::post('/registrations/{user}/approve', [DeveloperPanelController::class, 'approve'])->name('registrations.approve');
        Route::post('/registrations/{user}/reject', [DeveloperPanelController::class, 'reject'])->name('registrations.reject');
        Route::post('/registrations/{user}/suspend', [DeveloperPanelController::class, 'suspendAccount'])->name('registrations.suspend');
        Route::post('/registrations/{user}/reactivate', [DeveloperPanelController::class, 'reactivateAccount'])->name('registrations.reactivate');
        Route::get('/stores', [DeveloperPanelController::class, 'stores'])->name('stores');
        Route::post('/stores/{store}/suspend', [DeveloperPanelController::class, 'suspendStore'])->name('stores.suspend');
        Route::post('/stores/{store}/reactivate', [DeveloperPanelController::class, 'reactivateStore'])->name('stores.reactivate');
        Route::get('/users', [DeveloperPanelController::class, 'users'])->name('users');
        Route::get('/activity', [DeveloperPanelController::class, 'activity'])->name('activity');
        Route::get('/more', [DeveloperPanelController::class, 'more'])->name('more');
        Route::get('/account', [DeveloperPanelController::class, 'editAccount'])->name('account.edit');
        Route::put('/account/password', [DeveloperPanelController::class, 'updatePassword'])->name('account.update');
    });

require __DIR__.'/auth.php';

if (App::environment('local')) {
    Route::view('/_design', 'design')->name('design');
}

if (App::environment(['local', 'testing'])) {
    Route::get('/demo/{screen?}', DemoScreenController::class)
        ->where('screen', '[a-z-]+')
        ->name('demo.screen');
}

if (App::environment('local')) {
    Route::get('/_owner-preview/{screen?}', [OwnerScreenController::class, 'preview'])
        ->where('screen', '[a-z-]+')
        ->name('owner.preview');
}
