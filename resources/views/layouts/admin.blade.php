<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title', 'Admin Panel') - SIDESI</title>
  @livewireStyles
  <!-- Scripts -->
  @vite(['resources/css/app.css', 'resources/css/app-admin.css', 'resources/js/app.js',
  'resources/js/app-admin.js'])
</head>

<header class="w-full bg-white/90 backdrop-blur border-b border-slate-200">
  <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
    <div class="flex items-center justify-between py-3">

      <!-- Izquierda: logo + título -->
      <div class="flex items-center gap-3">
        <!-- Logo compacto -->
        <a href="{{ route('home-site') }}" class="flex h-10 w-10 items-center justify-center rounded-lg bg-gradient-to-br from-blue-600 to-indigo-600 text-white font-semibold shadow-sm">
          S
        </a>

        <div class="leading-tight">
          <h1 class="text-base sm:text-lg font-semibold text-slate-800">
            Panel de Administración
          </h1>
          <a href="{{ route('home-site') }}" class="text-xs text-slate-500 hover:text-blue-600 transition">
            Volver al sitio
          </a>
        </div>
      </div>

      <!-- Derecha: usuario + logout -->
      <div class="flex items-center gap-3">
        <div class="hidden sm:flex flex-col items-end">
          <span class="text-sm font-medium text-slate-800">
            {{ Auth::user()->name }} {{ Auth::user()->lastname }}
          </span>
          <span class="text-xs text-slate-500">
            {{ Auth::user()->role->name }}
          </span>
        </div>

        <img
          src="https://cdn-icons-png.flaticon.com/512/149/149071.png"
          alt="Avatar"
          class="h-9 w-9 rounded-full object-cover ring-1 ring-slate-200" />

        <a href="{{ route('logout') }}"
          class="inline-flex items-center rounded-md bg-blue-600 px-3 py-1.5 text-xs font-medium text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-2">
          Cerrar sesión
        </a>
      </div>

    </div>
  </div>
</header>


<div class="admin-nav">
  @include('layouts.Components.admin-main-menu')
</div>

<div class="admin-content">
  <!-- Dashboard Tab -->
  <div class="tab-content active">
    @yield('content')
  </div>
</div>
</div>
@livewireScripts
<script>
  document.addEventListener('livewire:initialized', function() {
    Livewire.on('message-success', function(message) {
      Alert(
        '¡Éxito!',
        message,
        'success'
      );
    });

    Livewire.on('message-error', function(message) {
      Alert(
        '¡Error!',
        message,
        'error'
      );
    });
  });
</script>
</body>

</html>