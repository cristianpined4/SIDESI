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

<body style="background: white;">
  <div class="admin-panel" id="adminPanel">
    <div class="admin-header">
      <div style="display: flex; align-items: center;">
        <div class="logo"
          style="width: 50px; height: 50px; font-size: 0.875rem; display: inline-flex; margin-right: 1rem;margin-bottom: 0;">
          SIDESI
        </div>
        <div style="font-size: 1.5rem; font-weight: bold;">
          Panel de Administración<br>
          <a href="{{ route('home-site') }}"
            style="font-size: 0.75rem; font-weight: normal; color: #e3dada; margin-top: 0.25rem;display: block;">
            Volver al Sitio
          </a>
        </div>
      </div>
      <div style="display: flex; align-items: flex-end; justify-content: center;">
        <div style="display: flex; align-items: center; margin-right: 1rem;">
          <img src="https://cdn-icons-png.flaticon.com/512/149/149071.png" alt="User Icon"
            style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover; margin-right: 0.5rem;">
          <div class="user-info"
            style="display: flex; flex-direction: column; align-items: flex-start; margin-right: 1rem;">
            <span style="font-weight: 500;">
              {{ Auth::user()->name }} {{ Auth::user()->lastname }}
            </span>
            <span style="font-size: 0.875rem; color: #e3dada;">
              {{ Auth::user()->role->name }}
            </span>
          </div>
        </div>
        <a class="logout-btn btn-small" href="{{ route('logout') }}">Cerrar Sesión</a>
      </div>
    </div>

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