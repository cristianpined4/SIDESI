<?php

use Illuminate\Support\Facades\Route;

/* Rutas del sitio */
Route::get('/', App\Livewire\Site\HomeController::class)->name('home-site');
Route::get('/noticias', App\Livewire\Site\NewsListController::class)->name('news-list');
Route::get('/ofertas', App\Livewire\Site\Ofertas_EmpleoController::class)->name('site.ofertas');
Route::get('/login', App\Livewire\Admin\Auth\LoginController::class)->name('login');
Route::get('/register', App\Livewire\Admin\Auth\RegisterController::class)->name('register');
Route::get('/contactos', App\Livewire\Site\ContactoController::class)->name('site.contactos');
Route::get('/documentos', App\Livewire\Site\DocumentosController::class)->name('site.documentos');
Route::get('/eventos', App\Livewire\Site\EventosController::class)->name('site.eventos');
Route::get('/perfil', App\Livewire\PerfilController::class)->name('profile');
Route::get('/ofertas', App\Livewire\Site\Ofertas_EmpleoController::class)->name('site.ofertas');
Route::get('/certificado/{code}', function ($code) {
  return "Certificado {$code}";
})->name('ver-certificado');



/* Rutas del admin */
Route::middleware(['auth'])->group(function () {
  Route::get('/logout', [App\Livewire\Admin\Auth\LoginController::class, 'logout'])->name('logout');
  Route::post('/logout', [App\Livewire\Admin\Auth\LoginController::class, 'logout'])->name('logout');

  Route::prefix('admin')->name('admin.')->group(function () {
    // Redirección base
    Route::redirect('/', '/admin/dashboard');

    // rutas del administrador /admin/...
    Route::get('/dashboard', App\Livewire\Admin\DashboardController::class)->name('dashboard');
    Route::get('/usuarios', App\Livewire\Admin\UsuariosController::class)->name('usuarios');
    Route::get('/eventos', App\Livewire\Admin\EventosController::class)->name('eventos');
    Route::get('/documentos', App\Livewire\Admin\DocumentosController::class)->name('documentos');
    Route::get('/noticias', App\Livewire\Admin\NoticiasController::class)->name('noticias');
    Route::get('/ofertas', App\Livewire\Admin\OfertasDeEmpleoController::class)->name('ofertas');
    Route::get('/perfil', App\Livewire\PerfilController::class)->name('profile');
  });
});