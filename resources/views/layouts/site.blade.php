<!DOCTYPE html>
<html lang="es" class="__variable_fb8f2c __variable_f910ec antialiased">

<head>
  <meta charSet="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css"
    integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />
  <link rel="icon" type="image/png" href="{{ asset('images/logosidesii2.png') }}" />
  <link rel="apple-touch-icon" type="image/png" href="{{ asset('images/logosidesii2.png') }}" />
  <link rel="shortcut icon" href="{{ asset('images/logosidesii2.png') }}" />
  <link rel="stylesheet" href="{{ url('/_next/static/css/13259dc9ccfa6acd.css') }}" data-precedence="next" />
  <link rel="stylesheet" href="{{ url('/_next/static/css/c193e56bda037bc1.css') }}" data-precedence="next" />
  <link rel="preload" as="script" fetchPriority="low"
    href="{{ url('/_next/static/chunks/webpack-2dda156b45d185bd.js') }}" />
  <script src="{{ url('/_next/static/chunks/fd9d1056-b88f8a4cc02eef8b.js') }}" async=""></script>
  <script src="{{ url('/_next/static/chunks/117-032e20e470d8ca46.js') }}" async=""></script>
  <script src="{{ url('/_next/static/chunks/main-app-74eb6bc1050af1ab.js') }}" async=""></script>
  <script src="{{ url('/_next/static/chunks/425-387fdc495ccd3415.js') }}" async=""></script>
  <script src="{{ url('/_next/static/chunks/app/page-84a62db6747b31db.js') }}" async=""></script>
  <script src="{{ url('/_next/static/chunks/app/layout-64ee0ccc90ccaaab.js') }}" async=""></script>
  <title>{{ ($title = trim($__env->yieldContent('title'))) }}{{ $title && !str_contains($title, '-') &&
    !str_contains(strtoupper($title), 'SIDESI') ? ' - SIDESI' : '' }}</title>
  <meta name="description"
    content="@yield('meta_description', 'Plataforma oficial de SIDESI para gestión académica, eventos y desarrollo estudiantil')" />
  <meta name="generator" content="v0.app" />
  <script src="{{ url('/_next/static/chunks/polyfills-42372ed130431b0a.js') }}" noModule=""></script>
  @livewireStyles
  <!-- Scripts -->
  @vite(['resources/css/app.css', 'resources/css/app-site.css', 'resources/js/app.js', 'resources/js/app-site.js'])
</head>

<body class="font-sans">
  <!--$-->
  <div class="min-h-screen">
    <header class="sticky top-0 z-50 w-full border-b bg-card/95 backdrop-blur supports-[backdrop-filter]:bg-card/60">
      <div class="container mx-auto px-4">
        <div class="flex h-16 items-center justify-between">
          <div class="flex items-center space-x-4">
            <a href="{{ url('/') }}" class="flex h-16 items-end leading-none">
              <img src="{{ asset('images/logosidesii.png') }}" alt="Logo SIDESI"
                style="max-height: 64px; width: auto; object-fit: contain;" class="block self-center m-2l-0" />
            </a>
            <div class="border-l border-black h-10 mx-0"></div>
            <div class="block">
              <a href="{{ route('home-site') }}" class="text-xs text-slate-500 hover:text-blue-600 transition">
              </a>
              <a href="{{ url('/') }}" class="flex h-16 items-end leading-none">
                <img src="{{ asset('images/logoues.png') }}" alt="Logo SIDESI"
                  style="max-height: 64px; width: auto; object-fit: contain;" class="block self-center m-2l-0" />
              </a>
            </div>
            <nav class="hidden lg:flex items-center space-x-6">
              @include('layouts.Components.site-main-menu')
            </nav>
          </div>
          <div class="flex items-center space-x-4">
            <div class="hidden lg:flex items-center space-x-2">
              <div class="relative" style="opacity: 0; visibility: none;">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                  stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                  class="lucide lucide-search absolute left-2.5 top-2.5 h-4 w-4 text-muted-foreground">
                  <circle cx="11" cy="11" r="8"></circle>
                  <path d="m21 21-4.3-4.3"></path>
                </svg>
                <input type="search" data-slot="input"
                  class="file:text-foreground placeholder:text-muted-foreground selection:bg-primary selection:text-primary-foreground dark:bg-input/30 border-input flex h-9 min-w-0 rounded-md border bg-transparent px-3 py-1 text-base shadow-xs transition-[color,box-shadow] outline-none file:inline-flex file:h-7 file:border-0 file:bg-transparent file:text-sm file:font-medium disabled:pointer-events-none disabled:cursor-not-allowed disabled:opacity-50 md:text-sm focus-visible:border-ring focus-visible:ring-ring/50 focus-visible:ring-[3px] aria-invalid:ring-destructive/20 dark:aria-invalid:ring-destructive/40 aria-invalid:border-destructive pl-8 w-64"
                  placeholder="Buscar..." />
              </div>
            </div>
            @if (!Auth::check())
            <a data-slot="button"
              class="items-center justify-center whitespace-nowrap text-sm font-medium transition-all disabled:pointer-events-none disabled:opacity-50 [&amp;_svg]:pointer-events-none [&amp;_svg:not([class*=&#x27;size-&#x27;])]:size-4 shrink-0 [&amp;_svg]:shrink-0 outline-none focus-visible:border-ring focus-visible:ring-ring/50 focus-visible:ring-[3px] aria-invalid:ring-destructive/20 dark:aria-invalid:ring-destructive/40 aria-invalid:border-destructive hover:bg-accent hover:text-accent-foreground dark:hover:bg-accent/50 h-8 rounded-md gap-1.5 px-3 has-[&gt;svg]:px-2.5 hidden sm:flex"
              href="{{ route('login') }}">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="lucide lucide-user h-4 w-4 mr-2">
                <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2">
                </path>
                <circle cx="12" cy="7" r="4"></circle>
              </svg>Acceder</a>
            @else
            <!-- Menú de usuario -->
            <div class="relative hidden lg:flex" x-data="{ menuVisible: false }">
              @php
              $meta = auth()->user()->metadata ?? [];
              $avatarPath = is_array($meta) && isset($meta['avatar']) ? $meta['avatar'] : null;
              $avatarUrl = $avatarPath ? asset($avatarPath) : 'https://ui-avatars.com/api/?name=' .
              urlencode(auth()->user()->name . ' ' . auth()->user()->lastname) . '&background=E5E7EB&color=111827';
              @endphp
              <button @click="menuVisible = !menuVisible" class="flex items-center space-x-2 focus:outline-none">
                <img src="{{ $avatarUrl }}" alt="Avatar" class="h-8 w-8 rounded-full object-cover">
                <span class="text-sm font-medium text-gray-800"
                  style="text-align: left !important;white-space: nowrap;">
                  {{ Str::of(Auth::user()->name)->explode(' ')->first() }} {{ Str::of(Auth::user()->lastname)->explode(
                  ' ')->first() }}<br>
                  <span class="text-xs font-normal text-gray-500">{{ Auth::user()->role->name }}</span>
                </span>
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                  stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                  class="h-4 w-4 text-gray-500">
                  <polyline points="6 9 12 15 18 9"></polyline>
                </svg>
              </button>

              <!-- Dropdown -->
              <div x-show="menuVisible" @click.away="menuVisible = false"
                x-transition:enter="transition ease-out duration-100"
                x-transition:enter-start="transform opacity-0 scale-95"
                x-transition:enter-end="transform opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-75"
                x-transition:leave-start="transform opacity-100 scale-100"
                x-transition:leave-end="transform opacity-0 scale-95"
                class="absolute right-0 mt-2 w-48 origin-top-right rounded-md bg-white shadow-lg z-50">

                <div class="py-1">
                  {{-- Perfil --}}
                  <a href="{{ route('profile') }}"
                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-md" style="cursor: pointer;">
                    Perfil
                  </a>
                  <a href="{{ route('site.mis-eventos') }}"
                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-md" style="cursor: pointer;">
                    Mis Eventos
                  </a>

                  @if (in_array(Auth::user()->role_id, [1, 2]))
                  {{-- Panel de Administración --}}
                  <a href="{{ route('admin.dashboard') }}"
                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-md" style="cursor: pointer;">
                    Panel de Administración
                  </a>
                  @endif

                  {{-- Cerrar sesión --}}
                  <form method="POST" action="{{ route('logout') }}" style="cursor: pointer;">
                    @csrf
                    <button type="submit"
                      class="w-full text-left block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-md">
                      Cerrar sesión
                    </button>
                  </form>
                </div>
              </div>
            </div>
            @endif
            <button data-slot="button"
              class="inline-flex items-center justify-center whitespace-nowrap text-sm font-medium transition-all disabled:pointer-events-none disabled:opacity-50 [&amp;_svg]:pointer-events-none [&amp;_svg:not([class*=&#x27;size-&#x27;])]:size-4 shrink-0 [&amp;_svg]:shrink-0 outline-none focus-visible:border-ring focus-visible:ring-ring/50 focus-visible:ring-[3px] aria-invalid:ring-destructive/20 dark:aria-invalid:ring-destructive/40 aria-invalid:border-destructive hover:bg-accent hover:text-accent-foreground dark:hover:bg-accent/50 h-8 rounded-md gap-1.5 px-3 has-[&gt;svg]:px-2.5 lg:hidden"
              id="mobile-menu-button"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="lucide lucide-menu h-5 w-5">
                <line x1="4" x2="20" y1="12" y2="12"></line>
                <line x1="4" x2="20" y1="6" y2="6"></line>
                <line x1="4" x2="20" y1="18" y2="18"></line>
              </svg></button>
          </div>
        </div>
        <div class="md:hidden border-t bg-card" id="mobile-menu" style="display: none;">
          <div class="px-2 pt-2 pb-3 space-y-1">
            @include('layouts.Components.site-main-menu')
            <div class="px-3 py-2" bis_skin_checked="1">
              <div class="relative" bis_skin_checked="1" style="opacity: 0; visibility: none;">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                  stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                  class="lucide lucide-search absolute left-2.5 top-2.5 h-4 w-4 text-muted-foreground">
                  <circle cx="11" cy="11" r="8"></circle>
                  <path d="m21 21-4.3-4.3"></path>
                </svg><input data-slot="input"
                  class="file:text-foreground placeholder:text-muted-foreground selection:bg-primary selection:text-primary-foreground dark:bg-input/30 border-input flex h-9 w-full min-w-0 rounded-md border bg-transparent px-3 py-1 text-base shadow-xs transition-[color,box-shadow] outline-none file:inline-flex file:h-7 file:border-0 file:bg-transparent file:text-sm file:font-medium disabled:pointer-events-none disabled:cursor-not-allowed disabled:opacity-50 md:text-sm focus-visible:border-ring focus-visible:ring-ring/50 focus-visible:ring-[3px] aria-invalid:ring-destructive/20 dark:aria-invalid:ring-destructive/40 aria-invalid:border-destructive pl-8"
                  placeholder="Buscar..." type="search">
              </div>
            </div>
            <div class="px-3 py-2" bis_skin_checked="1">
              @if(!Auth::check())
              <a data-slot="button" href="{{ route('login') }}"
                class="inline-flex items-center justify-center whitespace-nowrap text-sm font-medium transition-all disabled:pointer-events-none disabled:opacity-50 [&amp;_svg]:pointer-events-none [&amp;_svg:not([class*='size-'])]:size-4 shrink-0 [&amp;_svg]:shrink-0 outline-none focus-visible:border-ring focus-visible:ring-ring/50 focus-visible:ring-[3px] aria-invalid:ring-destructive/20 dark:aria-invalid:ring-destructive/40 aria-invalid:border-destructive border shadow-xs hover:bg-accent hover:text-accent-foreground dark:bg-input/30 dark:border-input dark:hover:bg-input/50 h-8 rounded-md gap-1.5 px-3 has-[&gt;svg]:px-2.5 w-full bg-transparent"><svg
                  xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                  stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                  class="lucide lucide-user h-4 w-4 mr-2">
                  <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path>
                  <circle cx="12" cy="7" r="4"></circle>
                </svg>Acceder</a>
              @else
              <div class="relative" style="display: flex;justify-content: flex-end;"
                x-data="{ menuVisibleMobile: false }">
                <button @click="menuVisibleMobile = !menuVisibleMobile"
                  class="flex items-center space-x-2 focus:outline-none">
                  <img src="{{ $avatarUrl }}" alt="Avatar" class="h-8 w-8 rounded-full object-cover">
                  <span class="text-sm font-medium text-gray-800"
                    style="text-align: left !important;white-space: nowrap;">
                    {{ Str::of(Auth::user()->name)->explode(' ')->first() }} {{
                    Str::of(Auth::user()->lastname)->explode(' ')->first() }}<br>
                    <span class="text-xs font-normal text-gray-500">{{ Auth::user()->role->name }}</span>
                  </span>
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="h-4 w-4 text-gray-500">
                    <polyline points="6 9 12 15 18 9"></polyline>
                  </svg>
                </button>

                <!-- Dropdown -->
                <div x-show="menuVisibleMobile" @click.away="menuVisibleMobile = false"
                  x-transition:enter="transition ease-out duration-100"
                  x-transition:enter-start="transform opacity-0 scale-95"
                  x-transition:enter-end="transform opacity-100 scale-100"
                  x-transition:leave="transition ease-in duration-75"
                  x-transition:leave-start="transform opacity-100 scale-100"
                  x-transition:leave-end="transform opacity-0 scale-95"
                  class="absolute right-0 mt-2 w-48 origin-top-right rounded-md bg-white shadow-lg z-50">

                  <div class="py-1">
                    {{-- Perfil --}}
                    <a href="{{ route('profile') }}"
                      class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-md"
                      style="cursor: pointer;">
                      Perfil
                    </a>

                    @if (in_array(Auth::user()->role_id, [1, 2]))
                    {{-- Panel de Administración --}}
                    <a href="{{ route('admin.dashboard') }}"
                      class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-md"
                      style="cursor: pointer;">
                      Panel de Administración
                    </a>
                    @endif

                    {{-- Cerrar sesión --}}
                    <form method="POST" action="{{ route('logout') }}">
                      @csrf
                      <button type="submit"
                        class="w-full text-left block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-md"
                        style="cursor: pointer;">
                        Cerrar sesión
                      </button>
                    </form>
                  </div>
                </div>
              </div>
              @endif
            </div>
          </div>
        </div>
    </header>
    @yield('content')
    <footer class="bg-secondary text-secondary-foreground">
      <div class="container mx-auto px-4 py-16">
        <div class="grid lg:grid-cols-4 gap-8">
          <div class="space-y-4">
            <div class="flex items-center space-x-2">
              <div class="h-8 w-8 rounded-lg bg-primary flex items-center justify-center">
                <span class="text-primary-foreground font-bold text-sm">S</span>
              </div>
              <span class="font-bold text-xl">SIDESI</span>
            </div>
            <p class="text-sm leading-relaxed">Sección de Ingeniería de Sistemas Informáticos. Impulsando el
              crecimiento académico y profesional de la comunidad estudiantil.</p>
            <div class="flex space-x-4">
              <a href="#" class="text-muted-foreground hover:text-primary transition-colors"><svg
                  xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                  stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                  class="lucide lucide-facebook h-5 w-5">
                  <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path>
                </svg>
              </a>
              <a href="#" class="text-muted-foreground hover:text-primary transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                  stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                  class="lucide lucide-twitter h-5 w-5">
                  <path
                    d="M22 4s-.7 2.1-2 3.4c1.6 10-9.4 17.3-18 11.6 2.2.1 4.4-.6 6-2C3 15.5.5 9.6 3 5c2.2 2.6 5.6 4.1 9 4-.9-4.2 4-6.6 7-3.8 1.1 0 3-1.2 3-1.2z">
                  </path>
                </svg>
              </a>
              <a href="#" class="text-muted-foreground hover:text-primary transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                  stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                  class="lucide lucide-instagram h-5 w-5">
                  <rect width="20" height="20" x="2" y="2" rx="5" ry="5"></rect>
                  <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path>
                  <line x1="17.5" x2="17.51" y1="6.5" y2="6.5"></line>
                </svg>
              </a>
              <a href="#" class="text-muted-foreground hover:text-primary transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                  stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                  class="lucide lucide-linkedin h-5 w-5">
                  <path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"></path>
                  <rect width="4" height="12" x="2" y="9"></rect>
                  <circle cx="4" cy="4" r="2"></circle>
                </svg>
              </a>
            </div>
          </div>
          <div class="space-y-4">
            <h3 class="font-semibold text-lg">Servicios</h3>
            <ul class="space-y-2">
              <li>
                <a href="/eventos" class="text-sm text-muted-foreground hover:text-primary transition-colors">Gestión de
                  Eventos</a>
              </li>
              <li>
                <a href="/documentos"
                  class="text-sm text-muted-foreground hover:text-primary transition-colors">Documentos Académicos</a>
              </li>
              <li>
                <a href="/certificaciones"
                  class="text-sm text-muted-foreground hover:text-primary transition-colors">Certificaciones</a>
              </li>
              <li>
                <a href="/ofertas" class="text-sm text-muted-foreground hover:text-primary transition-colors">Bolsa de
                  Empleo</a>
              </li>
            </ul>
          </div>
          <div class="space-y-4">
            <h3 class="font-semibold text-lg">Comunidad</h3>
            <ul class="space-y-2">
              <li>
                <a href="/estudiantes"
                  class="text-sm text-muted-foreground hover:text-primary transition-colors">Estudiantes</a>
              </li>
              <li>
                <a href="/docentes"
                  class="text-sm text-muted-foreground hover:text-primary transition-colors">Docentes</a>
              </li>
              <li>
                <a href="/empresas"
                  class="text-sm text-muted-foreground hover:text-primary transition-colors">Empresas</a>
              </li>
            </ul>
          </div>
          <div class="space-y-4">
            <h3 class="font-semibold text-lg">Recursos</h3>
            <ul class="space-y-2">
              <li>
                <a href="/ayuda" class="text-sm text-muted-foreground hover:text-primary transition-colors">Centro de
                  Ayuda</a>
              </li>
              <li>
                <a href="/guias" class="text-sm text-muted-foreground hover:text-primary transition-colors">Guías y
                  Tutoriales</a>
              </li>
              <li>
                <a href="/faq" class="text-sm text-muted-foreground hover:text-primary transition-colors">Preguntas
                  Frecuentes</a>
              </li>
              <li>
                <a href="/soporte" class="text-sm text-muted-foreground hover:text-primary transition-colors">Soporte
                  Técnico</a>
              </li>
            </ul>
          </div>
        </div>
        <div class="border-t border-border mt-12 pt-8">
          <div class="grid md:grid-cols-3 gap-6">
            <div class="flex items-center space-x-3">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="lucide lucide-mail h-5 w-5 text-primary">
                <rect width="20" height="16" x="2" y="4" rx="2"></rect>
                <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"></path>
              </svg>
              <span class="text-sm">contacto@sidesi.edu.sv</span>
            </div>
            <div class="flex items-center space-x-3">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                class="lucide lucide-phone h-5 w-5 text-primary">
                <path
                  d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z">
                </path>
              </svg>
              <span class="text-sm">+503 2011-2025</span>
            </div>
            <div class="flex items-center space-x-3"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                stroke-linejoin="round" class="lucide lucide-map-pin h-5 w-5 text-primary">
                <path
                  d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0">
                </path>
                <circle cx="12" cy="10" r="3"></circle>
              </svg>
              <a href="https://www.ues.edu.sv" target="_blank" class="text-sm text-black-600 hover:underline">
                Ciudad Universitaria, Facultad Multidisciplinaria Oriental (FMO)
              </a>
            </div>
          </div>
        </div>
        <div class="border-t border-border mt-8 pt-8 text-center">
          <p class="text-sm text-muted-foreground">© {{ date('Y') }} SIDESI. Todos los derechos reservados. |
            <a href="/privacidad" class="hover:text-primary transition-colors ml-1">Política de Privacidad</a>
            <a href="/terminos" class="hover:text-primary transition-colors ml-1">Términos de Uso</a>
          </p>
        </div>
      </div>
    </footer>
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

      let mobile_menu_button = document.getElementById('mobile-menu-button');
      if (mobile_menu_button != null) {
        mobile_menu_button.addEventListener('click', function() {
          let mobile_menu = document.getElementById('mobile-menu');
          if (mobile_menu.style.display === 'none') {
            mobile_menu.style.display = 'block';
            mobile_menu_button.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-x h-5 w-5">
                        <path d="M18 6 6 18"></path>
                        <path d="m6 6 12 12"></path>
                      </svg>`;
          } else {
            mobile_menu.style.display = 'none';
            mobile_menu_button.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-menu h-5 w-5">
                        <line x1="4" x2="20" y1="12" y2="12"></line>
                        <line x1="4" x2="20" y1="6" y2="6"></line>
                        <line x1="4" x2="20" y1="18" y2="18"></line>
                      </svg>`;
          }
        });

        window.addEventListener('resize', function() {
          let mobile_menu = document.getElementById('mobile-menu');
          if (window.innerWidth >= 768) {
            mobile_menu.style.display = 'none';
            mobile_menu_button.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-menu h-5 w-5">
                        <line x1="4" x2="20" y1="12" y2="12"></line>
                        <line x1="4" x2="20" y1="6" y2="6"></line>
                        <line x1="4" x2="20" y1="18" y2="18"></line>
                      </svg>`;
          }
        });
      }
    });
  </script>
</body>

</html>