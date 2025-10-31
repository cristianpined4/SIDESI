@section('title', 'Configuración de usuario')

<main class="w-full">
  {{-- Loading --}}
  <div class="fixed inset-0 z-50 hidden items-center justify-center bg-black/30" wire:loading.class.remove="hidden">
    <div class="rounded-xl bg-white px-6 py-4 shadow-lg">
      <div class="flex items-center gap-3">
        <span class="animate-spin h-5 w-5 border-2 border-slate-300 border-t-transparent rounded-full"></span>
        <p class="text-sm text-slate-700">Guardando cambios…</p>
      </div>
    </div>
  </div>

  {{-- Encabezado --}}
  <div class="mb-6 text-center">
    <h1 class="text-2xl font-semibold text-slate-900">Configuración de usuario</h1>
    <p class="text-sm text-slate-500">Actualiza tu información básica.</p>
  </div>

  <section class="mx-auto w-full max-w-2xl rounded-2xl border border-slate-200 bg-white shadow-sm">
    <div class="p-6">
      <h2 class="text-base font-medium text-slate-900">Perfil</h2>
      <p class="mt-1 text-sm text-slate-500">Datos del usuario logueado.</p>

      {{-- Avatar --}}
      <div class="mt-5 flex items-center gap-4">
        <img
          src="{{ $fields['avatar_path'] ?? $fields['main_image'] ?? 'https://api.dicebear.com/7.x/initials/svg?seed=' . urlencode(Auth::user()->name ?? 'Usuario') }}"
          class="h-20 w-20 rounded-full object-cover ring-2 ring-slate-200"
          alt="Avatar">
      </div>

      {{-- Info auto --}}
      <div class="mt-5 rounded-xl border border-slate-200 bg-slate-50 p-4">
        <p class="text-xs uppercase tracking-wide text-slate-500">Usuario actual</p>
        <p class="mt-1 text-sm font-medium text-slate-900">{{ Auth::user()->name ?? '—' }}</p>
        <p class="text-sm text-slate-600">{{ Auth::user()->email ?? '—' }}</p>
        @if(Auth::user()->phone ?? false)
          <p class="text-sm text-slate-600">{{ Auth::user()->phone }}</p>
        @endif
      </div>

      {{-- Editables mínimos --}}
      <div class="mt-6 space-y-4">
        <div>
          <label class="block text-sm font-medium text-slate-700">Nombre para mostrar</label>
          <input type="text" wire:model.live.debounce.300ms="fields.name"
                 class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-900 focus:outline-none"
                 placeholder="Ej: Edras Lazo">
          @error('fields.name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
          <label class="block text-sm font-medium text-slate-700">Teléfono</label>
          <input type="text" wire:model.live.debounce.300ms="fields.phone"
                 class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-900 focus:outline-none"
                 placeholder="+503 7xxx-xxxx">
          @error('fields.phone') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
          <label class="block text-sm font-medium text-slate-700">Correo alterno</label>
          <input type="email" wire:model.live.debounce.300ms="fields.alt_email"
                 class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-900 focus:outline-none"
                 placeholder="usuario@ejemplo.com">
          @error('fields.alt_email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>
      </div>

      {{-- Botones --}}
      <div class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-3">
        <button type="button" wire:click="update"
                class="inline-flex items-center justify-center rounded-xl bg-slate-900 px-5 py-2 text-sm font-medium text-white hover:bg-slate-700">
          Guardar cambios
        </button>

        <button type="button" wire:click="resetUI"
                class="inline-flex items-center justify-center rounded-xl border border-slate-300 px-5 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
          Cancelar
        </button>
      </div>
    </div>
  </section>
</main>
