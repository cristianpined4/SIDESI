<?php

use Illuminate\Support\Facades\Route;

/* Rutas del sitio */
Route::get('/', App\Livewire\Site\HomeController::class)->name('home-site');
Route::get('/noticias', App\Livewire\Site\NewsListController::class)->name('news-list');
Route::get('/login', App\Livewire\Admin\Auth\LoginController::class)->name('login');
Route::get('/register', App\Livewire\Admin\Auth\RegisterController::class)->name('register');
Route::get('/contactos', App\Livewire\Site\ContactoController::class)->name('site.contactos');
Route::get('/documentos', App\Livewire\Site\DocumentosController::class)->name('site.documentos');
Route::get('/eventos', App\Livewire\Site\EventosController::class)->name('site.eventos');
Route::get('/perfil', App\Livewire\PerfilController::class)->name('profile');
Route::get('/ofertas', App\Livewire\Site\OfertasEmpleoController::class)->name('site.ofertas');
Route::get('/certificado/{code}', App\Livewire\Site\VerCertificadosController::class)->name('ver-certificado');

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


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Pagos as Pago;
use App\Models\InscripcionesEvento;


Route::get('/payment/callback', function (Request $request) {
  $pagoId = $request->query('pago_id');
  $inscripcionId = $request->query('inscripcion_id');
  $transactionId = $request->query('idTransaccion');
  $hash = $request->query('hash');

  $pago = Pago::find($pagoId);
  $inscripcion = InscripcionesEvento::find($inscripcionId);

  if (!$pago || !$inscripcion) {
    return redirect('/')
      ->with('error', 'Datos de pago o inscripción no encontrados.');
  }

  try {
    /**
     * 3️⃣ Obtener token de acceso desde Wompi
     */
    $tokenResponse = Http::asForm()->post('https://id.wompi.sv/connect/token', [
      'grant_type' => 'client_credentials',
      'client_id' => env('WOMPI_APP_ID'),
      'client_secret' => env('WOMPI_APP_SECRET'),
      'audience' => 'wompi_api',
    ]);

    if ($tokenResponse->failed()) {
      throw new \Exception('Error al obtener token de Wompi: ' . $tokenResponse->body());
    }

    $accessToken = $tokenResponse->json('access_token');

    /**
     * 4️⃣ Verificar la transacción con Wompi
     */
    $response = Http::withToken($accessToken)
      ->get("https://api.wompi.sv/v1/transactions/{$transactionId}");

    return response()->json($response);

    if ($response->failed()) {
      throw new \Exception('No se pudo verificar la transacción con Wompi.');
    }

    $data = $response->json();
    $estado = $data['estado'] ?? 'pendiente';

    if ($estado === 'APROVADA' || $estado === 'APROBADA') {
      $pago->update([
        'transaction_id' => $transactionId,
        'status' => 'completado',
        'paid_at' => now(),
      ]);

      $inscripcion->update([
        'status' => 'registrado',
        'comprobante_codigo' => 'WOMPI-' . strtoupper(substr($transactionId, 0, 8)),
      ]);

      return redirect('/')
        ->with('success', '✅ Pago completado e inscripción confirmada.');
    } else {
      $pago->update(['status' => strtolower($estado)]);
      return redirect('/')
        ->with('error', '⚠️ El pago aún no ha sido aprobado. Estado: ' . $estado);
    }

  } catch (\Throwable $th) {
    Log::error('Error al procesar callback de Wompi', ['error' => $th->getMessage()]);
    return redirect('/')
      ->with('error', 'Error al procesar el pago: ' . $th->getMessage());
  }
})->name('wompi.callback');

Route::post('/payment/webhook', [App\Http\Controllers\WompiWebhookController::class, 'handle'])->name('wompi.webhook');