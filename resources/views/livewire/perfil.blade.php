@section('title', 'Perfil')

<div class="max-w-5xl mx-auto p-6">
    <div class="mb-6">
        <h1 class="text-3xl font-bold">Perfil de usuario</h1>
        <p class="text-gray-600">Consulta y actualiza tu información personal.</p>
    </div>

    <div class="space-y-6">
        @php
        $meta = auth()->user()->metadata ?? [];
        $avatarPath = is_array($meta) && isset($meta['avatar']) ? $meta['avatar'] : null;
        $avatarUrl = $avatarPath ? asset($avatarPath) : 'https://ui-avatars.com/api/?name=' .
        urlencode(auth()->user()->name . ' ' . auth()->user()->lastname) . '&background=E5E7EB&color=111827';
        @endphp

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="w-full flex items-center justify-between px-6 py-4">
                <span class="text-lg font-semibold">Imagen del usuario</span>
            </div>
            <div class="px-6 py-4 grid grid-cols-1 md:grid-cols-[200px,1fr] items-start gap-6">
                <div class="flex flex-col items-center gap-3">
                    <img src="{{ $avatarUrl }}" alt="Avatar" style="height:200px; width:auto;"
                        class="rounded-full object-cover ring-1 ring-gray-200" />
                </div>
                <div class="form-group mb-2">
                    <input wire:model="avatar" type="file" accept="image/*"
                        class="form-control @error('avatar') was-validated is-invalid @enderror">
                    <div class="invalid-feedback">@error('avatar') {{$message}} @enderror</div>
                    <div class="flex items-center gap-3 mt-3">
                        <button wire:click="updateAvatar" wire:loading.attr="disabled"
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                            <span wire:loading.remove wire:target="updateAvatar,avatar">Actualizar foto</span>
                            <span wire:loading wire:target="updateAvatar,avatar">Subiendo...</span>
                        </button>
                        <button wire:click="deleteAvatar" type="button"
                            class="px-4 py-2 border border-gray-300 rounded-md hover:bg-gray-50">Eliminar foto</button>
                        @if ($avatar)
                        <span class="text-sm text-gray-500">Archivo seleccionado: {{ $avatar->getClientOriginalName()
                            }}</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
            <button type="button" class="w-full flex items-center justify-between px-6 py-4">
                <span class="text-lg font-semibold">General</span>
            </button>
            <div class="px-6 py-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Nombre</label>
                    <input type="text" wire:model.blur="fields.name"
                        class="px-2 mt-1 w-full rounded-md border border-gray-300 focus:ring-blue-500 focus:border-blue-500" />
                    @error('fields.name')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Apellido</label>
                    <input type="text" wire:model.blur="fields.lastname"
                        class="px-2 mt-1 w-full rounded-md border border-gray-300 focus:ring-blue-500 focus:border-blue-500" />
                    @error('fields.lastname')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Correo electrónico</label>
                    <input type="email" wire:model.blur="fields.email"
                        class="px-2 mt-1 w-full rounded-md border border-gray-300 focus:ring-blue-500 focus:border-blue-500" />
                    @error('fields.email')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Usuario</label>
                    <input type="text" wire:model.blur="fields.username"
                        class="px-2 mt-1 w-full rounded-md border border-gray-300 focus:ring-blue-500 focus:border-blue-500" />
                    @error('fields.username')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
            <div class="w-full flex items-center justify-between px-6 py-4">
                <span class="text-lg font-semibold">Seguridad</span>
            </div>
            <div class="px-6 py-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Contraseña actual</label>
                        <input type="password" wire:model.defer="passwords.current"
                            class="px-2 mt-1 w-full rounded-md border border-gray-300 focus:ring-blue-500 focus:border-blue-500" />
                        @error('passwords.current')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nueva contraseña</label>
                        <input id="new-password" type="password" wire:model.defer="passwords.new"
                            class="px-2 mt-1 w-full rounded-md border border-gray-300 focus:ring-blue-500 focus:border-blue-500" />
                        @error('passwords.new')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                        <p class="text-xs text-gray-500 mt-1">Mín. 8 caracteres, con mayúsculas, minúsculas, número y
                            símbolo.</p>
                        <div class="mt-2">
                            <div class="w-full h-2 bg-gray-200 rounded">
                                <div id="pw-strength-bar" class="h-2 w-0 rounded" style="background-color:#ef4444">
                                </div>
                            </div>
                            <p id="pw-strength-text" class="text-xs mt-1 text-gray-600">Fortaleza: débil</p>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Confirmar contraseña</label>
                        <input type="password" wire:model.defer="passwords.confirm"
                            class="px-2 mt-1 w-full rounded-md border border-gray-300 focus:ring-blue-500 focus:border-blue-500" />
                        @error('passwords.confirm')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>
                <div class="md:col-span-2" style="justify-content: flex-start;display: flex;align-items: center;">
                    <button wire:click="updatePassword" wire:loading.attr="disabled"
                        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                        <span wire:loading.remove wire:target="updatePassword">Actualizar contraseña</span>
                        <span wire:loading wire:target="updatePassword">Guardando...</span>
                    </button>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
            <button type="button" class="w-full flex items-center justify-between px-6 py-4">
                <span class="text-lg font-semibold">Datos adicionales</span>
            </button>
            <div class="px-6 py-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Teléfono</label>
                    <input type="text" wire:model.blur="fields.phone"
                        class="px-2 mt-1 w-full rounded-md border border-gray-300 focus:ring-blue-500 focus:border-blue-500" />
                    @error('fields.phone')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Tipo de documento</label>
                    <input type="text" wire:model.blur="fields.document_type"
                        class="px-2 mt-1 w-full rounded-md border border-gray-300 focus:ring-blue-500 focus:border-blue-500" />
                    @error('fields.document_type')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Número de documento</label>
                    <input type="text" wire:model.blur="fields.document_number"
                        class="px-2 mt-1 w-full rounded-md border border-gray-300 focus:ring-blue-500 focus:border-blue-500" />
                    @error('fields.document_number')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Institución</label>
                    <input type="text" wire:model.blur="fields.institution"
                        class="px-2 mt-1 w-full rounded-md border border-gray-300 focus:ring-blue-500 focus:border-blue-500" />
                    @error('fields.institution')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <button wire:click="update" wire:loading.attr="disabled"
                class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                <span wire:loading.remove>Actualizar información personal</span>
                <span wire:loading>Guardando...</span>
            </button>
            <button wire:click="resetFields" type="button"
                class="px-4 py-2 rounded-md border border-gray-300 hover:bg-gray-50">Cancelar</button>
        </div>
    </div>
</div>
@push('scripts')
<script>
    (function attachStrengthMeter() {
  function scorePassword(pw) {
    let score = 0;
    if (!pw) return 0;
    const letters = {};
    for (let i = 0; i < pw.length; i++) {
      letters[pw[i]] = (letters[pw[i]] || 0) + 1;
      score += 5.0 / letters[pw[i]];
    }
    const variations = {
      digits: /[0-9]/.test(pw),
      lower: /[a-z]/.test(pw),
      upper: /[A-Z]/.test(pw),
      nonWords: /[^A-Za-z0-9]/.test(pw),
    };
    let variationCount = 0;
    for (let check in variations) variationCount += (variations[check] === true) ? 1 : 0;
    score += (variationCount - 1) * 10;
    return parseInt(score);
  }

  function updateMeter() {
    const el = document.getElementById('new-password');
    const bar = document.getElementById('pw-strength-bar');
    const txt = document.getElementById('pw-strength-text');
    if (!el || !bar || !txt) return;
    const val = el.value || '';
    const s = scorePassword(val);
    let pct = Math.min(100, Math.max(0, Math.round((s / 100) * 100)));
    let label = 'débil';
    let color = '#ef4444'; // red-500
    if (pct >= 75) { label = 'fuerte'; color = '#22c55e'; }      // green-500
    else if (pct >= 50) { label = 'media'; color = '#f59e0b'; }  // amber-500
    bar.style.width = pct + '%';
    bar.style.backgroundColor = color;
    txt.textContent = 'Fortaleza: ' + label;
  }

  // Bind once and on Livewire renders
  function bind() {
    const el = document.getElementById('new-password');
    if (!el) return;
    el.removeEventListener('input', updateMeter);
    el.addEventListener('input', updateMeter);
    updateMeter();
  }

  document.addEventListener('DOMContentLoaded', bind);
  document.addEventListener('livewire:load', bind);
})();
</script>
@endpush