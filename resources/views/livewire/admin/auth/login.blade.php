@section('title', 'Iniciar Sesión')

<div class="min-h-screen grid grid-cols-1 lg:grid-cols-2 bg-zinc-50">

  {{-- LADO IZQUIERDO: Formulario --}}
  <div class="flex items-center justify-center px-6 sm:px-10 py-16">
    <div class="w-full max-w-lg">

      {{-- Logo centrado y más grande --}}
      <div class="flex justify-center mb-16">
        <img src="{{ asset('images/logosidesii.png') }}" alt="SIDESI" class="h-16 w-auto object-contain">
      </div>

      {{-- Encabezado centrado --}}
      <div class="mb-10 text-center">
        <h1 class="text-3xl font-semibold text-zinc-900 tracking-tight">Bienvenido de nuevo</h1>
        <p class="mt-3 text-base text-zinc-600">
          ¿Aún no tienes cuenta?
          <a href="{{ route('register') }}" class="font-medium text-sky-600 hover:text-sky-700 transition-colors">Crear
            cuenta</a>
        </p>
      </div>

      {{-- Formulario --}}
      <form onsubmit="login(event)" class="space-y-6">
        <div>
          <label for="loginEmail" class="block text-sm font-medium text-zinc-700 mb-2">Usuario</label>
          <input id="loginEmail" type="text" placeholder="usuario o correo" wire:model="username" class="w-full h-12 rounded-xl border border-zinc-200 bg-white px-4 text-zinc-900 text-base
                   placeholder-zinc-400 transition-colors
                   focus:outline-none focus:border-sky-400 focus:ring-2 focus:ring-sky-100
                   @error('username') border-red-300 @enderror">
          @error('username') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
          <div class="flex items-center justify-between mb-2">
            <label for="loginPassword" class="block text-sm font-medium text-zinc-700">Contraseña</label>
            @if(Route::has('password.request'))
            <a href="{{ route('password.request') }}"
              class="text-sm text-sky-600 hover:text-sky-700 transition-colors">¿Olvidaste tu contraseña?</a>
            @endif
          </div>
          <input id="loginPassword" type="password" placeholder="••••••••" wire:model="password" class="w-full h-12 rounded-xl border border-zinc-200 bg-white px-4 text-zinc-900 text-base
                   placeholder-zinc-400 transition-colors
                   focus:outline-none focus:border-sky-400 focus:ring-2 focus:ring-sky-100
                   @error('password') border-red-300 @enderror">
          @error('password') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div class="flex items-center">
          <label class="inline-flex items-center gap-2 text-sm text-zinc-600 select-none cursor-pointer">
            <input type="checkbox" class="rounded border-zinc-300 text-sky-600 focus:ring-sky-400 focus:ring-offset-0">
            <span>Recuérdame</span>
          </label>
        </div>

        <button type="button" wire:click="login"
          class="w-full h-12 rounded-xl bg-sky-600 hover:bg-sky-700 text-white text-base font-medium transition-colors shadow-sm">
          Iniciar Sesión
        </button>

        <div class="relative my-6 text-center">
          <span class="px-3 text-sm text-zinc-400 bg-zinc-50 relative z-10">o</span>
          <div class="absolute inset-0 flex items-center">
            <div class="w-full border-t border-zinc-200"></div>
          </div>
        </div>

        <div class="text-center">
          <a href="{{ route('home-site') }}" class="text-sm text-sky-600 hover:text-sky-700 transition-colors">
            ← Volver al sitio
          </a>
        </div>
      </form>
    </div>
  </div>

  {{-- LADO DERECHO: Panel informativo --}}
  <div class="hidden lg:flex items-center justify-center bg-zinc-50 p-12 border-l border-zinc-200">
    <div class="max-w-md">
      <div class="space-y-6">
        <div class="w-12 h-1 bg-sky-600"></div>
        <h2 class="text-3xl font-semibold text-zinc-900 leading-tight">
          Sección de Ingeniería de Sistemas Informáticos
        </h2>
        <p class="text-zinc-600 leading-relaxed">
          Plataforma integral para la gestión académica, eventos, desarrollo profesional y comunicación institucional de
          la comunidad estudiantil de ingeniería.
        </p>
      </div>
    </div>
  </div>

  {{-- Overlay de carga Livewire --}}
  <div wire:loading class="fixed inset-0 z-50 grid place-items-center bg-white/80 backdrop-blur-sm loading2"
    wire:loading.attr="show2" show2="false">
    <div class="flex items-center gap-3 text-zinc-700">
      <svg class="animate-spin h-6 w-6" viewBox="0 0 24 24" fill="none">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" />
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z" />
      </svg>
      <span class="font-medium">Cargando…</span>
    </div>
  </div>
</div>