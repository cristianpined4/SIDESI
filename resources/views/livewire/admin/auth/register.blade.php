@section('title', 'Crear una cuenta')

<div class="min-h-screen grid grid-cols-1 lg:grid-cols-2 bg-zinc-50">

  {{-- LADO IZQUIERDO: Formulario --}}
  <div class="flex items-center justify-center px-6 sm:px-10 py-16 overflow-y-auto">
    <div class="w-full max-w-2xl">

      {{-- Logo centrado y más grande --}}
      <div class="flex justify-center mb-12">
        <img src="{{ asset('images/logosidesii.png') }}" alt="SIDESI" class="h-16 w-auto object-contain">
      </div>

      {{-- Encabezado centrado --}}
      <div class="mb-10 text-center">
        <h1 class="text-3xl font-semibold text-zinc-900 tracking-tight">Crear cuenta</h1>
        <p class="mt-3 text-base text-zinc-600">
          ¿Ya tienes cuenta?
          <a href="{{ route('login') }}" class="font-medium text-sky-600 hover:text-sky-700 transition-colors">Iniciar sesión</a>
        </p>
      </div>

      {{-- Formulario --}}
      <form class="space-y-6">

        {{-- Nombre / Apellido --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
          <div>
            <label for="name" class="block text-sm font-medium text-zinc-700 mb-2">Nombre</label>
            <input id="name" type="text" placeholder="Nombre"
                   wire:model="fields.name"
                   onkeyup="this.value=this.value.replace(/[^a-zA-Z\sÁÉÍÓÚáéíóúÑñ]/g,'');"
                   class="w-full h-12 rounded-xl border border-zinc-200 bg-white px-4 text-zinc-900 text-base placeholder-zinc-400
                          transition-colors focus:outline-none focus:border-sky-400 focus:ring-2 focus:ring-sky-100
                          @error('fields.name') border-red-300 @enderror">
            @error('fields.name') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
          </div>

          <div>
            <label for="lastname" class="block text-sm font-medium text-zinc-700 mb-2">Apellido</label>
            <input id="lastname" type="text" placeholder="Apellido"
                   wire:model="fields.lastname"
                   onkeyup="this.value=this.value.replace(/[^a-zA-Z\sÁÉÍÓÚáéíóúÑñ]/g,'');"
                   class="w-full h-12 rounded-xl border border-zinc-200 bg-white px-4 text-zinc-900 text-base placeholder-zinc-400
                          transition-colors focus:outline-none focus:border-sky-400 focus:ring-2 focus:ring-sky-100
                          @error('fields.lastname') border-red-300 @enderror">
            @error('fields.lastname') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
          </div>
        </div>

        {{-- Username --}}
        <div>
          <label for="username" class="block text-sm font-medium text-zinc-700 mb-2">Nombre de usuario</label>
          <input id="username" type="text" placeholder="Nombre de usuario"
                 wire:model="fields.username"
                 onkeyup="this.value=this.value.toLowerCase();"
                 class="w-full h-12 rounded-xl border border-zinc-200 bg-white px-4 text-zinc-900 text-base placeholder-zinc-400
                        transition-colors focus:outline-none focus:border-sky-400 focus:ring-2 focus:ring-sky-100
                        @error('fields.username') border-red-300 @enderror">
          @error('fields.username') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        {{-- Email --}}
        <div>
          <label for="email" class="block text-sm font-medium text-zinc-700 mb-2">Correo electrónico</label>
          <input id="email" type="email" placeholder="Correo electrónico"
                 wire:model="fields.email"
                 onkeyup="this.value=this.value.toLowerCase();"
                 class="w-full h-12 rounded-xl border border-zinc-200 bg-white px-4 text-zinc-900 text-base placeholder-zinc-400
                        transition-colors focus:outline-none focus:border-sky-400 focus:ring-2 focus:ring-sky-100
                        @error('fields.email') border-red-300 @enderror">
          @error('fields.email') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        {{-- Password / Confirmación --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
          <div>
            <label for="password" class="block text-sm font-medium text-zinc-700 mb-2">Contraseña</label>
            <input id="password" type="password" placeholder="Contraseña"
                   wire:model="fields.password"
                   class="w-full h-12 rounded-xl border border-zinc-200 bg-white px-4 text-zinc-900 text-base placeholder-zinc-400
                          transition-colors focus:outline-none focus:border-sky-400 focus:ring-2 focus:ring-sky-100
                          @error('fields.password') border-red-300 @enderror">
            @error('fields.password') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
          </div>

          <div>
            <label for="password_confirmation" class="block text-sm font-medium text-zinc-700 mb-2">Confirmar contraseña</label>
            <input id="password_confirmation" type="password" placeholder="Confirmar contraseña"
                   wire:model="fields.password_confirmation"
                   class="w-full h-12 rounded-xl border border-zinc-200 bg-white px-4 text-zinc-900 text-base placeholder-zinc-400
                          transition-colors focus:outline-none focus:border-sky-400 focus:ring-2 focus:ring-sky-100
                          @error('fields.password_confirmation') border-red-300 @enderror">
            @error('fields.password_confirmation') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
          </div>
        </div>

        {{-- Teléfono --}}
        <div>
          <label for="phone" class="block text-sm font-medium text-zinc-700 mb-2">Teléfono</label>
          <input id="phone" type="text" placeholder="Teléfono"
                 wire:model="fields.phone"
                 onkeypress="return /[0-9]/.test(String.fromCharCode(event.which || event.keyCode));"
                 class="w-full h-12 rounded-xl border border-zinc-200 bg-white px-4 text-zinc-900 text-base placeholder-zinc-400
                        transition-colors focus:outline-none focus:border-sky-400 focus:ring-2 focus:ring-sky-100
                        @error('fields.phone') border-red-300 @enderror">
          @error('fields.phone') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        {{-- Rol / Institución --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
          <div>
            <label for="role_id" class="block text-sm font-medium text-zinc-700 mb-2">Tipo de usuario</label>
            <select id="role_id" wire:model="fields.role_id"
                    class="w-full h-12 rounded-xl border border-zinc-200 bg-white px-4 text-zinc-900 text-base
                           transition-colors focus:outline-none focus:border-sky-400 focus:ring-2 focus:ring-sky-100
                           @error('fields.role_id') border-red-300 @enderror">
              <option value="" disabled>Seleccione una opción</option>
              @foreach ($roles as $role)
                <option value="{{ $role->name }}">{{ $role->name }}</option>
              @endforeach
            </select>
            @error('fields.role_id') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
          </div>

          <div id="institutionDiv" class="hidden">
            <label for="institution" class="block text-sm font-medium text-zinc-700 mb-2">Institución</label>
            <input id="institution" type="text" placeholder="Institución"
                   list="universidades"
                   wire:model="fields.institution"
                   class="w-full h-12 rounded-xl border border-zinc-200 bg-white px-4 text-zinc-900 text-base placeholder-zinc-400
                          transition-colors focus:outline-none focus:border-sky-400 focus:ring-2 focus:ring-sky-100
                          @error('fields.institution') border-red-300 @enderror">
            @error('fields.institution') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror

            <datalist id="universidades">
              @foreach ($universidades as $uni)
                <option value="{{ $uni }}">{{ $uni }}</option>
              @endforeach
            </datalist>
          </div>
        </div>

        {{-- Documento --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
          <div>
            <label for="document_type" class="block text-sm font-medium text-zinc-700 mb-2">Tipo de documento</label>
            <select id="document_type" wire:model="fields.document_type"
                    class="w-full h-12 rounded-xl border border-zinc-200 bg-white px-4 text-zinc-900 text-base
                           transition-colors focus:outline-none focus:border-sky-400 focus:ring-2 focus:ring-sky-100
                           @error('fields.document_type') border-red-300 @enderror">
              <option value="" disabled>Seleccione una opción</option>
              <option value="DUI">DUI</option>
              <option value="Carnet Estudiantil">Carnet Estudiantil</option>
              <option value="Pasaporte">Pasaporte</option>
              <option value="Carnet de extranjería">Carnet de extranjería</option>
            </select>
            @error('fields.document_type') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
          </div>

          <div>
            <label for="document_number" class="block text-sm font-medium text-zinc-700 mb-2">Número de documento</label>
            <input id="document_number" type="text" placeholder="Número de documento"
                   wire:model="fields.document_number"
                   class="w-full h-12 rounded-xl border border-zinc-200 bg-white px-4 text-zinc-900 text-base placeholder-zinc-400
                          transition-colors focus:outline-none focus:border-sky-400 focus:ring-2 focus:ring-sky-100
                          @error('fields.document_number') border-red-300 @enderror">
            @error('fields.document_number') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
          </div>
        </div>

        {{-- Botón --}}
        <button type="button" wire:click="register"
                class="w-full h-12 rounded-xl bg-sky-600 hover:bg-sky-700 text-white text-base font-medium transition-colors shadow-sm">
          Crear cuenta
        </button>

        {{-- Separador --}}
        <div class="relative my-6 text-center">
          <span class="px-3 text-sm text-zinc-400 bg-zinc-50 relative z-10">o</span>
          <div class="absolute inset-0 flex items-center">
            <div class="w-full border-t border-zinc-200"></div>
          </div>
        </div>

        {{-- Links --}}
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
          Plataforma integral para la gestión académica, eventos, desarrollo profesional y comunicación institucional de la comunidad estudiantil de ingeniería.
        </p>
      </div>
    </div>
  </div>

  {{-- Overlay de carga Livewire --}}
  <div wire:loading
       class="fixed inset-0 z-50 grid place-items-center bg-white/80 backdrop-blur-sm">
    <div class="flex items-center gap-3 text-zinc-700">
      <svg class="animate-spin h-6 w-6" viewBox="0 0 24 24" fill="none">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"/>
        <path class="opacity-75" fill="currentColor"
              d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"/>
      </svg>
      <span class="font-medium">Cargando…</span>
    </div>
  </div>
</div>

{{-- Toggle Institución según rol (Invitado oculta) --}}
<script>
  document.addEventListener('DOMContentLoaded', () => {
    const roleSelect = document.getElementById('role_id');
    const institutionDiv = document.getElementById('institutionDiv');
    const institutionInput = document.getElementById('institution');

    const toggleInstitution = () => {
      const value = (roleSelect.value || '').toLowerCase();
      const show = value && value !== 'invitado';
      institutionDiv.classList.toggle('hidden', !show);
      if (!show) {
        institutionInput.value = '';
        institutionInput.dispatchEvent(new Event('input', { bubbles: true }));
      }
    };

    toggleInstitution();
    roleSelect.addEventListener('change', toggleInstitution);
  });
</script>