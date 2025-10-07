<?php

use Illuminate\Support\Facades\Route;

/* Rutas del sitio */
Route::get('/', App\Livewire\Site\HomeController::class)->name('home-site');
Route::get('/noticias', App\Livewire\Site\NewsListController::class)->name('news-list');
Route::get('/login', App\Livewire\Admin\Auth\LoginController::class)->name('login');
Route::get('/register', App\Livewire\Admin\Auth\RegisterController::class)->name('register');
Route::get('/eventos', App\Livewire\Site\EventosController::class)->name('eventos');

/* Rutas del admin */
Route::middleware(['auth'])->group(function () {
  Route::get('/logout', [App\Livewire\Admin\Auth\LoginController::class, 'logout'])->name('logout');
  Route::post('/logout', [App\Livewire\Admin\Auth\LoginController::class, 'logout'])->name('logout');
  Route::prefix('admin')->name('admin.')->group(function () {
    // Redirección base
    Route::redirect('/', '/admin/dashboard');

    // rutas del administrador /admin/...
    Route::get('/dashboard', App\Livewire\Admin\DashboardController::class)->name('dashboard');
  });
});