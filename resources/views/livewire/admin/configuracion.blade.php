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
  <div class="mb-6">
    <h1 class="text-2xl font-semibold text-slate-900">Configuración de usuario</h1>
    <p class="text-sm text-slate-500">Actualiza tu información básica y preferencias.</p>
  </div>

  {{-- Contenido --}}
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Columna izquierda: Perfil --}}
    <section class="lg:col-span-1 rounded-2xl border border-slate-200 bg-white shadow-sm">
      <div class="p-6">
        <h2 class="text-base font-medium text-slate-900">Perfil</h2>
        <p class="mt-1 text-sm text-slate-500">Nombre para mostrar, avatar y datos de contacto alternos.</p>

        {{-- Avatar --}}
        <div class="mt-5 flex items-center gap-4">
          <img
            src="{{ $fields['avatar_path'] ?? $fields['main_image'] ?? 'https://api.dicebear.com/7.x/initials/svg?seed=' . urlencode($fields['name'] ?? 'Usuario') }}"
            class="h-20 w-20 rounded-full object-cover ring-2 ring-slate-200"
            alt="Avatar">
        </div>

        <div class="mt-6 space-y-4">
          {{-- Nombre para mostrar --}}
          <div>
            <label class="block text-sm font-medium text-slate-700">Nombre para mostrar</label>
            <input type="text" wire:model.live.debounce.300ms="fields.name"
                   placeholder="Ej: Edras Lazo"
                   class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-900 focus:outline-none">
            @error('fields.name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
          </div>

          {{-- Teléfono alterno --}}
          <div>
            <label class="block text-sm font-medium text-slate-700">Teléfono alterno</label>
            <input type="text" wire:model.live.debounce.300ms="fields.phone"
                   placeholder="+503 7xxx-xxxx"
                   class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-900 focus:outline-none">
            @error('fields.phone') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
          </div>

          {{-- Correo alterno --}}
          <div>
            <label class="block text-sm font-medium text-slate-700">Correo alterno</label>
            <input type="email" wire:model.live.debounce.300ms="fields.alt_email"
                   placeholder="usuario@ejemplo.com"
                   class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-900 focus:outline-none">
            @error('fields.alt_email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
          </div>
        </div>
      </div>
    </section>

    {{-- Columna derecha: Preferencias & Privacidad --}}
    <section class="lg:col-span-2 rounded-2xl border border-slate-200 bg-white shadow-sm">
      <div class="p-6">
        <h2 class="text-base font-medium text-slate-900">Preferencias</h2>
        <p class="mt-1 text-sm text-slate-500">Idioma, zona horaria y notificaciones.</p>

        <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 gap-4">
          {{-- Idioma --}}
          <div>
            <label class="block text-sm font-medium text-slate-700">Idioma</label>
            <select wire:model="fields.language"
                    class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-900 focus:outline-none">
              <option value="es">Español</option>
              <option value="en">English</option>
            </select>
            @error('fields.language') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
          </div>

          {{-- Zona horaria --}}
          <div>
            <label class="block text-sm font-medium text-slate-700">Zona horaria</label>
            <select wire:model="fields.timezone"
                    class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-900 focus:outline-none">
              <option value="America/El_Salvador">America/El_Salvador (GMT-6)</option>
              <option value="America/Mexico_City">America/Mexico_City (GMT-6)</option>
              <option value="America/Bogota">America/Bogota (GMT-5)</option>
              <option value="UTC">UTC</option>
            </select>
            @error('fields.timezone') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
          </div>

          {{-- Notificaciones correo --}}
          <div class="sm:col-span-1">
            <label class="block text-sm font-medium text-slate-700">Notificaciones por correo</label>
            <div class="mt-2 flex items-center gap-3">
              <input id="notify_email" type="checkbox" wire:model="fields.notify_email"
                     class="h-4 w-4 rounded border-slate-300 text-slate-900 focus:ring-slate-900">
              <label for="notify_email" class="text-sm text-slate-700">Activar</label>
            </div>
            @error('fields.notify_email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
          </div>

          {{-- Notificaciones push --}}
          <div class="sm:col-span-1">
            <label class="block text-sm font-medium text-slate-700">Notificaciones push</label>
            <div class="mt-2 flex items-center gap-3">
              <input id="notify_push" type="checkbox" wire:model="fields.notify_push"
                     class="h-4 w-4 rounded border-slate-300 text-slate-900 focus:ring-slate-900">
              <label for="notify_push" class="text-sm text-slate-700">Activar</label>
            </div>
            @error('fields.notify_push') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
          </div>

          {{-- Privacidad: mostrar correo / teléfono --}}
          <div class="sm:col-span-1">
            <label class="block text-sm font-medium text-slate-700">Privacidad</label>
            <div class="mt-2 flex items-center gap-3">
              <input id="show_email" type="checkbox" wire:model="fields.show_email"
                     class="h-4 w-4 rounded border-slate-300 text-slate-900 focus:ring-slate-900">
              <label for="show_email" class="text-sm text-slate-700">Mostrar correo</label>
            </div>
            <div class="mt-2 flex items-center gap-3">
              <input id="show_phone" type="checkbox" wire:model="fields.show_phone"
                     class="h-4 w-4 rounded border-slate-300 text-slate-900 focus:ring-slate-900">
              <label for="show_phone" class="text-sm text-slate-700">Mostrar teléfono</label>
            </div>
            @error('fields.show_email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            @error('fields.show_phone') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
          </div>

          {{-- Estado (activo) --}}
          <div class="sm:col-span-1">
            <label class="block text-sm font-medium text-slate-700">Estado</label>
            <select wire:model="fields.is_active"
                    class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-slate-900 focus:outline-none">
              <option value="1">Activo</option>
              <option value="0">Inactivo</option>
            </select>
            @error('fields.is_active') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
          </div>
        </div>

        {{-- Botones --}}
        <div class="mt-8 flex flex-col sm:flex-row items-center gap-3">
          @if ($record_id)
            <button type="button" wire:click="update"
                    class="inline-flex items-center justify-center rounded-xl bg-slate-900 px-5 py-2 text-sm font-medium text-white hover:bg-slate-700">
              Guardar cambios
            </button>
          @else
            <button type="button" wire:click="store"
                    class="inline-flex items-center justify-center rounded-xl bg-slate-900 px-5 py-2 text-sm font-medium text-white hover:bg-slate-700">
              Guardar
            </button>
          @endif

          <button type="button" wire:click="resetUI"
                  class="inline-flex items-center justify-center rounded-xl border border-slate-300 px-5 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
            Cancelar
          </button>
        </div>
      </div>
    </section>
  </div>
</main>
