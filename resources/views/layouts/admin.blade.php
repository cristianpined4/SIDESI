<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title', 'Admin Panel') - SIDESI</title>
  <link rel="icon" type="image/png" href="{{ asset('images/logosidesii2.png') }}" />
  <link rel="apple-touch-icon" type="image/png" href="{{ asset('images/logosidesii2.png') }}" />
  <link rel="shortcut icon" href="{{ asset('images/logosidesii2.png') }}" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css"
    integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />
  @livewireStyles
  <!-- Scripts -->
  @vite(['resources/css/app.css', 'resources/css/app-admin.css', 'resources/js/app.js',
  'resources/js/app-admin.js'])
</head>

<header class="w-full bg-white/90 backdrop-blur border-b border-slate-200">
  <!-- OJO: quitamos max-w-7xl para usar todo el ancho y pegar a las esquinas -->
  <div class="w-full px-4 sm:px-6 lg:px-8">
    <div class="flex items-center justify-between py-3">

      <!-- Izquierda: logo + título -->
      <div class="flex items-center gap-3">
        <!-- Logo SIDESI (espacio reservado) -->
        <a href="{{ route('home-site') }}" class="flex items-center">
          <img src="{{ asset('images/logosidesii.png') }}" alt="SIDESI" class="h-10 w-auto object-contain"
            loading="lazy" />
        </a>
        <div class="border-l border-black h-10 mx-0"></div>

        <div class="leading-tight">
          <h1 class="text-base sm:text-lg font-semibold text-slate-800">
            Panel de Administración
          </h1>
          <a href="{{ route('home-site') }}" class="text-xs text-slate-500 hover:text-blue-600 transition">
            Volver al sitio
          </a>
        </div>
      </div>

      <!-- Derecha: usuario con dropdown (en la esquina) -->
      <div class="relative ml-auto">
        <button id="user-menu-button"
          class="flex items-center gap-2 rounded-md px-2 py-1.5 hover:bg-slate-100 transition"
          onclick="document.getElementById('userDropdown').classList.toggle('hidden')">
          <div class="hidden sm:flex flex-col items-end">
            <span class="text-sm font-medium text-slate-800" style="white-space: nowrap;">
              {{ Str::of(Auth::user()->name)->explode(' ')->first() }} {{ Str::of(Auth::user()->lastname)->explode(
              ' ')->first() }}
            </span>
            <span class="text-xs text-slate-500">
              {{ Auth::user()->role->name }}
            </span>
          </div>
          @php
          $meta = auth()->user()->metadata ?? [];
          $avatarPath = is_array($meta) && isset($meta['avatar']) ? $meta['avatar'] : null;
          $avatarUrl = $avatarPath ? asset($avatarPath) : 'https://ui-avatars.com/api/?name=' .
          urlencode(auth()->user()->name . ' ' . auth()->user()->lastname) . '&background=E5E7EB&color=111827';
          @endphp
          <img src="{{ $avatarUrl }}" alt="Avatar" class="h-9 w-9 rounded-full object-cover ring-1 ring-slate-200" />

          <svg class="w-4 h-4 text-slate-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
            stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 9l6 6 6-6" />
          </svg>
        </button>

        <!-- Dropdown (incluye Cerrar sesión) -->
        <div id="userDropdown"
          class="absolute right-0 mt-2 w-56 bg-white border border-gray-100 rounded-xl shadow-lg overflow-hidden hidden z-50">
          <a href="{{ route('admin.profile') }}"
            class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-md"
            style="cursor: pointer;">Perfil</a>
          <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full text-left block px-4 py-2.5 text-sm text-red-600 hover:bg-gray-50">
              Cerrar sesión
            </button>
          </form>
        </div>
      </div>

    </div>
  </div>
</header>

<script>
  // Cerrar dropdown al hacer clic fuera
  document.addEventListener('click', function(e) {
    const d = document.getElementById('userDropdown');
    const b = document.getElementById('user-menu-button');
    if (d && b && !b.contains(e.target) && !d.contains(e.target)) d.classList.add('hidden');
  });

  document.addEventListener('DOMContentLoaded', () => {
    const adminNav = document.querySelector('.admin-nav');
    const header = document.querySelector('header');

    // Botón hamburguesa
    const toggleBtn = document.createElement('button');
    toggleBtn.innerHTML = '☰';
    toggleBtn.className = 'menu-toggle';
    header.appendChild(toggleBtn);

    // Overlay oscuro
    const overlay = document.createElement('div');
    overlay.id = 'menu-overlay';
    document.body.appendChild(overlay);

    // Abrir y cerrar menú
    const openMenu = () => {
      adminNav.classList.add('menu-open');
      overlay.classList.add('show');
      toggleBtn.classList.add('active');
    };
    const closeMenu = () => {
      adminNav.classList.remove('menu-open');
      overlay.classList.remove('show');
      toggleBtn.classList.remove('active');
    };
    const toggleMenu = () => {
      if (adminNav.classList.contains('menu-open')) closeMenu();
      else openMenu();
    };

    toggleBtn.addEventListener('click', toggleMenu);
    overlay.addEventListener('click', closeMenu);

    // Responsividad
    const handleResize = () => {
      if (window.innerWidth > 768) {
        toggleBtn.style.display = 'none';
        adminNav.classList.remove('menu-open');
        overlay.classList.remove('show');
        adminNav.removeAttribute('style');
      } else {
        toggleBtn.style.display = 'block';
      }
    };

    window.addEventListener('resize', handleResize);
    handleResize();
  });
</script>

<div class="admin-nav flex-center">
  @include('layouts.Components.admin-main-menu')
</div>

<div class="admin-content bg-white ">
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