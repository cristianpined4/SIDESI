<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Site\NewsListController;

/* Rutas del sitio */
Route::get('/', App\Livewire\Site\HomeController::class)->name('home-site');
Route::get('/noticias', NewsListController::class)->name('news-list');
Route::get('/login', App\Livewire\Admin\Auth\LoginController::class)->name('login');
Route::get('/register', App\Livewire\Admin\Auth\RegisterController::class)->name('register');

/* Rutas del admin */
Route::middleware(['auth'])->group(function () {
  Route::redirect('/admin', '/admin/dashboard');
  Route::get('/admin/dashboard', App\Livewire\Admin\DashboardController::class)->name('dashboard-admin');
  Route::get('/logout', [App\Livewire\Admin\Auth\LoginController::class, 'logout'])->name('logout');
  Route::post('/logout', [App\Livewire\Admin\Auth\LoginController::class, 'logout'])->name('logout');
});