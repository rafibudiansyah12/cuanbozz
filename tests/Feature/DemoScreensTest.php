<?php

use Illuminate\Support\Facades\Config;

it('renders each static demo screen without requiring database records', function () {
    $screens = [
        'home', 'login', 'register', 'forgot-password', 'onboarding', 'dashboard', 'pos', 'payment', 'receipt',
        'products', 'product-form', 'categories', 'stock-adjustment', 'stock-history', 'orders', 'order-detail',
        'report', 'store-profile', 'members', 'appearance', 'admin-stores', 'admin-users', 'more',
    ];

    foreach ($screens as $screen) {
        $this->get('/demo/'.$screen)
            ->assertOk()
            ->assertSee('Data contoh');
    }
});

it('shows the cashier navigation and hides owner settings in local demo mode', function () {
    Config::set('app.env', 'local');

    $this->get('/demo/pos?role=cashier')
        ->assertOk()
        ->assertDontSee('Laporan')
        ->assertDontSee('Pengaturan toko')
        ->assertDontSee('Harga jual');

    $this->get('/demo/report?role=cashier')->assertNotFound();
    $this->get('/demo/dashboard?role=cashier')->assertNotFound();
    $this->get('/demo/product-form?role=cashier')->assertNotFound();
});

it('ignores the cashier role query outside the local environment', function () {
    $this->get('/demo/pos?role=cashier')
        ->assertOk()
        ->assertSee('Laporan');
});

it('renders the sample login and its google sign in affordance', function () {
    $this->get('/demo/login')
        ->assertOk()
        ->assertSee('Masuk dengan Google')
        ->assertSee('Email')
        ->assertSee('Kata sandi');
});
