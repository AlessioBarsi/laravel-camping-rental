<?php

use Livewire\Volt\Volt;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

//Auth
Route::get('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/');
})->name('logout');

Volt::route('/login', 'auth.login')->name('login');
Volt::route('/register', 'auth.register')->name('register');

Route::middleware(['auth'])->group(function () {
    Volt::route('/dashboard', 'dashboard')->name('dashboard');
});

Volt::route('/', 'home')->name('home');
Volt::route('/shop/stores', 'shop.stores-index');
Volt::route('/shop/stores/{id}', 'shop.store-detail');
Volt::route('/shop/articles', 'shop.articles-index');
Volt::route('/shop/articles/{id}', 'shop.article-detail');
Volt::route('/shop/categories', 'shop.categories-index');

Volt::route('/categories', 'categories.index')->name('categories');
Volt::route('/articles', 'articles.index')->name('articles');
Volt::route('/stores', 'stores.index')->name('stores');