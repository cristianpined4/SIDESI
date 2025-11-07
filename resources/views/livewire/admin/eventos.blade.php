@section('title', "Eventos")

<main style="width: 100%;">
    <div class="loading" wire:loading.attr="show" show="false">
        <div class="loader"></div>
        <p class="loading-text">Cargando...</p>
    </div>
    <!-- modales -->

    <!-- modal de eventos -->
    <div id="modal-home" style="z-index: 3" class="modal" wire:ignore.self>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="userLabel">{{ $record_id ? 'Editar evento' : 'Nuevo evento' }}</h5>
                    <button type="button" class="btn-close" aria-label="Cerrar"
                        onclick="closeModal(this.closest('.modal'))">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group mb-2">
                        <label class="form-label">Título</label>
                        <input wire:model="fields.title" type="text" placeholder="Título del evento"
                            class="form-control @error('fields.title') was-validated is-invalid @enderror">
                        <div class="invalid-feedback">@error('fields.title') {{$message}} @enderror</div>
                    </div>

                    <div class="form-group mb-2">
                        <label class="form-label">Descripción</label>
                        <textarea wire:model="fields.description" placeholder="Descripción del evento"
                            class="form-control @error('fields.description') was-validated is-invalid @enderror"
                            rows="3"></textarea>
                        <div class="invalid-feedback">@error('fields.description') {{$message}} @enderror</div>
                    </div>

                    <div class="form-group mb-2">
                        <label class="form-label">Inicio</label>
                        <input wire:model="fields.start_time" type="datetime-local"
                            class="form-control @error('fields.start_time') was-validated is-invalid @enderror">
                        <div class="invalid-feedback">@error('fields.start_time') {{$message}} @enderror</div>
                    </div>

                    <div class="form-group mb-2">
                        <label class="form-label">Fin</label>
                        <input wire:model="fields.end_time" type="datetime-local" min="{{ $fields['start_time'] }}"
                            class="form-control @error('fields.end_time') was-validated is-invalid @enderror">
                        <div class="invalid-feedback">@error('fields.end_time') {{$message}} @enderror</div>
                    </div>

                    <div class="form-group mb-2">
                        <label class="form-label">Localización</label>
                        <input wire:model="fields.location" type="text" placeholder="Lugar del evento"
                            class="form-control @error('fields.location') was-validated is-invalid @enderror"
                            oninput="this.value = this.value.toUpperCase();">
                        <div class="invalid-feedback">@error('fields.location') {{$message}} @enderror</div>
                    </div>

                    <div class="form-group mb-2">
                        <label class="form-label">Habilitar Inscripciones</label>
                        <select wire:model="fields.inscriptions_enabled"
                            class="form-control @error('fields.inscriptions_enabled') was-validated is-invalid @enderror">
                            <option value="">Seleccione...</option>
                            <option value="1">Sí</option>
                            <option value="0">No</option>
                        </select>
                        <div class="invalid-feedback">@error('fields.inscriptions_enabled') {{$message}} @enderror</div>
                    </div>

                    <div class="form-group mb-2">
                        <label class="form-label">Máximo de Participantes</label>
                        <input wire:model="fields.max_participants" type="number" min="1" placeholder="Cantidad"
                            class="form-control @error('fields.max_participants') was-validated is-invalid @enderror">
                        <div class="invalid-feedback">@error('fields.max_participants') {{$message}} @enderror</div>
                    </div>

                    <div class="form-group mb-2">
                        <label class="form-label">Correo de Contacto</label>
                        <input wire:model="fields.contact_email" type="email" placeholder="correo@ejemplo.com"
                            class="form-control @error('fields.contact_email') was-validated is-invalid @enderror">
                        <div class="invalid-feedback">@error('fields.contact_email') {{$message}} @enderror</div>
                    </div>

                    <div class="form-group mb-2">
                        <label class="form-label">Teléfono de Contacto</label>
                        <input wire:model="fields.contact_phone" type="text" placeholder="0000-0000"
                            class="form-control @error('fields.contact_phone') was-validated is-invalid @enderror"
                            onkeypress="return /[0-9]/.test(String.fromCharCode(event.which || event.keyCode));">
                        <div class="invalid-feedback">@error('fields.contact_phone') {{$message}} @enderror</div>
                    </div>

                    <div class="form-group mb-2">
                        <label class="form-label">Activo</label>
                        <select wire:model="fields.is_active"
                            class="form-control @error('fields.is_active') was-validated is-invalid @enderror">
                            <option value="">Seleccione...</option>
                            <option value="1">Activo</option>
                            <option value="0">Inactivo</option>
                        </select>
                        <div class="invalid-feedback">@error('fields.is_active') {{$message}} @enderror</div>
                    </div>

                    <div class="form-group mb-2">
                        <label class="form-label">Modalidad</label>
                        <select wire:model="fields.mode"
                            class="form-control @error('fields.mode') was-validated is-invalid @enderror">
                            <option value="">Seleccione...</option>
                            <option value="presencial">Presencial</option>
                            <option value="virtual">Virtual</option>
                        </select>
                        <div class="invalid-feedback">@error('fields.mode') {{$message}} @enderror</div>
                    </div>

                    <div class="form-group mb-2">
                        <label class="form-label">¿Evento Pagado?</label>
                        <select wire:model="fields.is_paid" id="is_paid"
                            class="form-control @error('fields.is_paid') was-validated is-invalid @enderror">
                            <option value="">Seleccione...</option>
                            <option value="1">Sí</option>
                            <option value="0">No</option>
                        </select>
                        <div class="invalid-feedback">@error('fields.is_paid') {{$message}} @enderror</div>
                    </div>

                    <div class="form-group mb-2" id="price" style="display: none;" wire:ignore.self>
                        <label class="form-label">Precio ($)</label>
                        <input wire:model="fields.price" type="number" min="0" step="0.01" placeholder="0.00"
                            class="form-control @error('fields.price') was-validated is-invalid @enderror">
                        <div class="invalid-feedback">@error('fields.price') {{$message}} @enderror</div>
                    </div>

                    <div class="form-group mb-2">
                        <label class="form-label">Organizador</label>
                        <select wire:model="fields.organizer_id"
                            class="form-control @error('fields.organizer_id') was-validated is-invalid @enderror">
                            <option value="">Seleccione un organizador...</option>
                            @foreach ($recordsUsers as $organizador)
                            <option value="{{ $organizador->id }}">{{ $organizador->name }}</option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback">@error('fields.organizer_id') {{$message}} @enderror</div>
                    </div>
                    <div class="form-group mb-2">
                        <label class="form-label">Imagen Principal</label>
                        <input wire:model="file" type="file" accept="image/*"
                            class="form-control @error('file') was-validated is-invalid @enderror">
                        <div class="invalid-feedback">@error('file') {{$message}} @enderror</div>
                    </div>
                    {{-- imagen --}}
                    <div class="mb-2 w-full flex items-center justify-center">
                        @if (isset($fields['main_image']) && $file == null)
                        <img class="w-1/2" src="{{ $fields['main_image'] }}" alt="imagen" />
                        @elseif ($file != null)
                        <img class="w-1/2" src="{{ $file->temporaryUrl() }}" alt="imagen" />
                        @else
                        <img class="w-1/2" src="{{ url('/') }}/images/imagen_placeholder.avif" alt="imagen" />
                        @endif
                    </div>
                </div>

                <div class="modal-footer">
                    @if ($record_id)
                    <button type="button" class="btn btn-warning" wire:click="update">Actualizar</button>
                    @else
                    <button type="button" class="btn btn-primary" wire:click="store">Guardar</button>
                    @endif
                    <button type="button" class="btn btn-secondary"
                        onclick="closeModal(this.closest('.modal'))">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Fin modal de eventos -->

    <!-- Modal de Sesion -->
    <div id="Sesion-modal" style="z-index: 1" class="modal" wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable flex justify-center items-center"
            style="max-width: 80%;padding: 0;">
            <div class="modal-content w-full h-[80vh] flex flex-col rounded-xl shadow-lg overflow-hidden">

                <!-- Header -->
                <div class="modal-header bg-gradient-to-r text-white flex justify-between items-center">
                    <h5 class="modal-title text-lg font-semibold px-6 py-3">Gestión de Sesiones</h5>
                    <button type="button"
                        class="btn-close text-white text-2xl font-bold leading-none opacity-80 hover:opacity-100"
                        aria-label="Cerrar" onclick="closeModal(this.closest('.modal'))">
                        &times;
                    </button>

                </div>

                <!-- Barra superior de acciones -->
                <div class="flex flex-col md:flex-row justify-between items-center gap-4 px-6 py-3 border-b bg-gray-50">
                    <div class="w-full md:w-1/2">
                        <input type="text" placeholder="Buscar..."
                            class="form-input w-full border border-gray-300 rounded-md focus:border-blue-500 focus:ring focus:ring-blue-200 transition"
                            wire:model.live.debounce.500ms="search_sesiones">

                    </div>

                    <button
                        class="btn btn-primary bg-blue-600 hover:bg-blue-700 text-white font-medium px-3 py-1.5 text-sm rounded-md shadow-sm transition"
                        wire:click="abrirModal('Sesion-modal-form',false,true)">
                        + Nueva Sesión
                    </button>
                </div>

                <!-- Contenido -->
                <div class="modal-body flex-1 overflow-y-auto p-4">
                    <div class="w-full overflow-x-auto">
                        <table class="table w-full border border-gray-200 rounded-lg shadow-sm bg-white text-sm">
                            <thead class="bg-gray-100 text-gray-600 uppercase text-xs font-semibold">
                                <tr>
                                    <th class="px-4 py-3 text-left"></th>
                                    <th class="px-4 py-3 text-left">Título</th>
                                    <th class="px-4 py-3 text-left">Descripción</th>
                                    <th class="px-4 py-3 text-left">Tipo de sesión</th>
                                    <th class="px-4 py-3 text-left">Ponente</th>
                                    <th class="px-4 py-3 text-left">Hora de comienzo</th>
                                    <th class="px-4 py-3 text-left">Hora fin</th>
                                    <th class="px-4 py-3 text-left">Requiere Aprobación</th>
                                    <th class="px-4 py-3 text-left">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y">
                                @foreach ($records_sesiones as $sesion)
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="px-4 py-3">
                                        @if ($sesion->main_image)
                                        <img src="{{ $sesion->main_image }}" alt="{{ $sesion->title }}"
                                            class="w-12 h-12 object-cover rounded">
                                        @else
                                        <img src="{{ url('/') }}/images/imagen_placeholder.avif" alt="Imagen"
                                            class="w-12 h-12 object-cover rounded">
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">{{ $sesion->title }}</td>
                                    <td class="px-4 py-3">{{ substr($sesion->description, 0, 50) }}{{
                                        strlen($sesion->description) > 50
                                        ? '...' : '' }}</td>
                                    <td class="px-4 py-3">{{ ucfirst($sesion->mode) }}</td>
                                    <td class="px-4 py-3">
                                        {{ $sesion->ponente ? $sesion->ponente->name . ' ' . $sesion->ponente->lastname
                                        : 'N/A' }}
                                    </td>
                                    <td class="px-4 py-3">{{ \Carbon\Carbon::parse($sesion->start_time)->format('d/m/Y
                                        h:i A') }}</td>
                                    <td class="px-4 py-3">{{ \Carbon\Carbon::parse($sesion->end_time)->format('d/m/Y h:i
                                        A') }}</td>
                                    <td class="px-4 py-3">
                                        @if ($sesion->require_approval)
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Requiere Aprobación
                                        </span>
                                        @else
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                            No Requiere Aprobación
                                        </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 flex space-x-2 items-center">
                                        <button
                                            class="bg-yellow-500 text-white px-3 py-1 rounded-md hover:bg-yellow-600 transition text-sm cursor-pointer"
                                            wire:click="editSesion('{{ $sesion->id }}')">
                                            Editar
                                        </button>
                                        <button
                                            class="bg-red-500 text-white px-3 py-1 rounded-md hover:bg-red-600 transition text-sm cursor-pointer"
                                            onclick="confirmarEliminarSesion({{ $sesion->id }})">
                                            Eliminar
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @if (!empty($records_sesiones) && $records_sesiones->count() && method_exists($records_sesiones,
                        'links'))
                        <div class="mt-4">
                            {{ $records_sesiones->links() }}
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Footer -->
                <div class="modal-footer bg-gray-50 border-t px-6 py-3 flex justify-end">
                    <button type="button"
                        class="btn btn-primary   bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md"
                        onclick="closeModal(this.closest('.modal'))">
                        Cerrar
                    </button>
                </div>

            </div>
        </div>
    </div>
    <!-- Fin Modal Sesion -->

    <!-- modal de sesion formulario -->
    <div id="Sesion-modal-form" style="z-index: 3" class="modal" wire:ignore.self>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="userLabel">{{ $record_sesion_id ? 'Editar Sesión' : 'Nuevo Sesión' }}
                    </h5>
                    <button type="button" class="btn-close" aria-label="Cerrar"
                        onclick="closeModal(this.closest('.modal'))">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group mb-2">
                        <label class="form-label">Título</label>
                        <input wire:model="fieldsSesiones.title" type="text" placeholder="Título de la sesión"
                            class="form-control @error('fieldsSesiones.title') was-validated is-invalid @enderror">
                        <div class="invalid-feedback">@error('fieldsSesiones.title') {{$message}} @enderror</div>
                    </div>
                    <div class="form-group mb-2">
                        <label class="form-label">Descripción</label>
                        <textarea wire:model="fieldsSesiones.description" placeholder="Descripción de la sesión"
                            class="form-control @error('fieldsSesiones.description') was-validated is-invalid @enderror"
                            rows="3"></textarea>
                        <div class="invalid-feedback">@error('fieldsSesiones.description') {{$message}} @enderror</div>
                    </div>
                    <div class="form-group mb-2">
                        <label class="form-label">Inicio</label>
                        <input wire:model="fieldsSesiones.start_time" type="datetime-local"
                            max="{{ $fields['end_time'] }}" min="{{ $fields['start_time'] }}"
                            class="form-control @error('fieldsSesiones.start_time') was-validated is-invalid @enderror">
                        <div class="invalid-feedback">@error('fieldsSesiones.start_time') {{$message}} @enderror</div>
                    </div>
                    <div class="form-group mb-2">
                        <label class="form-label">Fin</label>
                        <input wire:model="fieldsSesiones.end_time" type="datetime-local"
                            min="{{ $fieldsSesiones['start_time'] ? $fieldsSesiones['start_time'] : $fields['start_time'] }}"
                            max="{{ $fields['end_time'] }}"
                            class="form-control @error('fieldsSesiones.end_time') was-validated is-invalid @enderror">
                        <div class="invalid-feedback">@error('fieldsSesiones.end_time') {{$message}} @enderror</div>
                    </div>
                    <div class="form-group mb-2">
                        <label class="form-label">Ponente</label>
                        <select wire:model="fieldsSesiones.ponente_id"
                            class="form-control @error('fieldsSesiones.ponente_id') was-validated is-invalid @enderror">
                            <option value="">Seleccione un ponente...</option>
                            @foreach ($recordsPonentes as $ponente)
                            <option value="{{ $ponente->id }}">{{ $ponente->name }}</option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback">@error('fieldsSesiones.ponente_id') {{$message}} @enderror</div>
                    </div>
                    <div class="form-group mb-2">
                        <label class="form-label">Tipo de Sesión</label>
                        <select wire:model="fieldsSesiones.mode"
                            class="form-control @error('fieldsSesiones.mode') was-validated is-invalid @enderror">
                            <option value="">Seleccione un tipo de sesión...</option>
                            <option value="taller">Taller</option>
                            <option value="ponencia">Ponencia</option>
                            <option value="panel">Panel</option>
                            <option value="otro">Otro</option>
                        </select>
                        <div class="invalid-feedback">@error('fieldsSesiones.mode') {{$message}} @enderror</div>
                    </div>
                    <div class="form-group mb-2">
                        <label class="form-label">Máximo de Participantes</label>
                        <input wire:model="fieldsSesiones.max_participants" type="number" min="1" placeholder="Cantidad"
                            class="form-control @error('fieldsSesiones.max_participants') was-validated is-invalid @enderror">
                        <div class="invalid-feedback">@error('fieldsSesiones.max_participants') {{$message}} @enderror
                        </div>
                    </div>
                    <div class="form-group mb-2">
                        <label class="form-label">Requiere Aprobación</label>
                        <select wire:model="fieldsSesiones.require_approval"
                            class="form-control @error('fieldsSesiones.require_approval') was-validated is-invalid @enderror">
                            <option value="">Seleccione...</option>
                            <option value="1">Sí</option>
                            <option value="0">No</option>
                        </select>
                        <div class="invalid-feedback">@error('fieldsSesiones.require_approval') {{$message}} @enderror
                        </div>
                    </div>
                    <div class="form-group mb-2">
                        <label class="form-label">Imagen Principal</label>
                        <input wire:model="file2" type="file" accept="image/*"
                            class="form-control @error('file2') was-validated is-invalid @enderror">
                        <div class="invalid-feedback">@error('file2') {{$message}} @enderror</div>
                    </div>
                    {{-- imagen --}}
                    <div class="mb-2 w-full flex items-center justify-center">
                        @if (isset($fieldsSesiones['main_image']) && $file2 == null)
                        <img class="w-1/2" src="{{ $fieldsSesiones['main_image'] }}" alt="imagen" />
                        @elseif ($file2 != null)
                        <img class="w-1/2" src="{{ $file2->temporaryUrl() }}" alt="imagen" />
                        @else
                        <img class="w-1/2" src="{{ url('/') }}/images/imagen_placeholder.avif" alt="imagen" />
                        @endif
                    </div>
                </div>

                <div class="modal-footer">
                    @if ($record_sesion_id)
                    <button type="button" class="btn btn-warning" wire:click="updateSesion">Actualizar</button>
                    @else
                    <button type="button" class="btn btn-primary" wire:click="storeSesion">Guardar</button>
                    @endif
                    <button type="button" class="btn btn-secondary"
                        onclick="closeModal(this.closest('.modal'))">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Fin modal de sesion formulario -->

    <!-- Modal de participantes en el evento -->
    <div id="participantes-evento-modal" style="z-index: 1" class="modal" wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable flex justify-center items-center"
            style="max-width: 80%;padding: 0;">
            <div class="modal-content w-full h-[80vh] flex flex-col rounded-xl shadow-lg overflow-hidden">

                <!-- Header -->
                <div class="modal-header bg-gradient-to-r text-white flex justify-between items-center">
                    <h5 class="modal-title text-lg font-semibold px-6 py-3">Usuarios registrados al evento</h5>
                    <button type="button"
                        class="btn-close text-white text-2xl font-bold leading-none opacity-80 hover:opacity-100"
                        aria-label="Cerrar" onclick="closeModal(this.closest('.modal'))">
                        &times;
                    </button>

                </div>

                <!-- Barra superior de acciones -->
                <div class="flex flex-col md:flex-row justify-between items-center gap-4 px-6 py-3 border-b bg-gray-50">
                    <div class="w-full md:w-1/2">
                        <input type="text" placeholder="Buscar..."
                            class="form-input w-full border border-gray-300 rounded-md focus:border-blue-500 focus:ring focus:ring-blue-200 transition"
                            wire:model.live.debounce.500ms="search_sesiones">

                    </div>
                    @if ($record_id && $records_users_event->count() > 0)
                    <button
                        class="bg-cyan-500 text-white px-3 py-1 rounded-md hover:bg-cyan-600 transition text-sm cursor-pointer"
                        id="generarAllDiplomas" wire:click="generarDiplomasAll('{{ $record_id }}')">
                        Generar todos los diplomas
                    </button>
                    @endif
                </div>

                <!-- Contenido -->
                <div class="modal-body flex-1 overflow-y-auto p-4">
                    <div class="overflow-x-auto rounded-lg shadow-md bg-white">
                        <table class="min-w-full text-left text-sm text-gray-700 border border-gray-200">
                            <thead class="bg-gray-100 text-gray-800 uppercase text-xs font-semibold">
                                <tr>
                                    <th class="px-6 py-3 border-b">#</th>
                                    <th class="px-6 py-3 border-b">Nombre</th>
                                    <th class="px-6 py-3 border-b">Apellido</th>
                                    <th class="px-6 py-3 border-b">Correo</th>
                                    <th class="px-6 py-3 border-b">Teléfono</th>
                                    <th class="px-6 py-3 border-b">Institución</th>
                                    <th class="px-6 py-3 border-b">Estado</th>
                                    <th class="px-6 py-3 border-b">Accion</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($records_users_event as $index => $user)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-3 border-b">{{ $index + 1 }}</td>
                                    <td class="px-6 py-3 border-b">{{ $user?->name }}</td>
                                    <td class="px-6 py-3 border-b">{{ $user?->lastname }}</td>
                                    <td class="px-6 py-3 border-b">{{ $user?->email }}</td>
                                    <td class="px-6 py-3 border-b">{{ $user?->phone ?? '—' }}</td>
                                    <td class="px-6 py-3 border-b">{{ $user?->institution ?? '—' }}</td>
                                    <td class="px-6 py-3 border-b text-center">
                                        @switch($user?->status)
                                        @case('aprobado')
                                        <span
                                            class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-semibold">
                                            Aprobado
                                        </span>
                                        @break

                                        @case('pendiente')
                                        <span
                                            class="bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full text-xs font-semibold">
                                            Pendiente
                                        </span>
                                        @break

                                        @case('rechazado')
                                        <span
                                            class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-xs font-semibold">
                                            Rechazado
                                        </span>
                                        @break

                                        @case('cancelado')
                                        <span
                                            class="bg-gray-200 text-gray-700 px-3 py-1 rounded-full text-xs font-semibold">
                                            Cancelado
                                        </span>
                                        @break

                                        @default
                                        <span
                                            class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-xs font-semibold">
                                            Registrado
                                        </span>
                                        @endswitch
                                    </td>
                                    <td class="px-4 py-3 flex space-x-2 items-center">
                                        @switch($user?->status)
                                        @case('aprobado')
                                        <button
                                            class="bg-red-500 text-white px-3 py-1 rounded-md hover:bg-red-600 transition text-sm cursor-pointer"
                                            wire:click="rechazarParticipante({{$user->id}})">
                                            Rechazar
                                        </button>
                                        <button
                                            class="bg-cyan-500 text-white px-3 py-1 rounded-md hover:bg-cyan-600 transition text-sm cursor-pointer btn-diploma"
                                            data-participante="{{$user->id}}"
                                            wire:click="generarDiplomaIndividual('{{ $record_id }}','{{ $user->id }}')">
                                            Imprimir
                                        </button>
                                        @break
                                        @case('pendiente')
                                        <button
                                            class="bg-green-500 text-white px-3 py-1 rounded-md hover:bg-green-600 transition text-sm cursor-pointer"
                                            wire:click="aprobarParticipante({{$user->id}})">
                                            Aprobar
                                        </button>
                                        <button
                                            class="bg-red-500 text-white px-3 py-1 rounded-md hover:bg-red-600 transition text-sm cursor-pointer"
                                            wire:click="rechazarParticipante({{$user->id}})">
                                            Rechazar
                                        </button>
                                        @break
                                        @case('registrado')
                                        <button
                                            class="bg-red-500 text-white px-3 py-1 rounded-md hover:bg-red-600 transition text-sm cursor-pointer"
                                            wire:click="rechazarParticipante({{$user->id}})">
                                            Rechazar
                                        </button>
                                        <button
                                            class="bg-cyan-500 text-white px-3 py-1 rounded-md hover:bg-cyan-600 transition text-sm cursor-pointer btn-diploma"
                                            data-participante="{{$user->id}}"
                                            wire:click="generarDiplomaIndividual('{{ $record_id }}','{{ $user->id }}')">
                                            Imprimir
                                        </button>
                                        @break
                                        @endswitch
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="px-6 py-4 text-center text-gray-500">
                                        No hay usuarios inscritos en este evento.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Footer -->
                <div class="modal-footer bg-gray-50 border-t px-6 py-3 flex justify-end">
                    <button type="button"
                        class="btn btn-primary   bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md"
                        onclick="closeModal(this.closest('.modal'))">
                        Cerrar
                    </button>
                </div>

            </div>
        </div>
    </div>
    <!-- Fin Modal de participantes en el evento -->

    <!-- Contenido - inicio -->
    <div class="flex justify-between items-end flex-wrap gap-4">
        <div class="flex items-start gap-4 flex-col" style="max-width: 800px;width: 100%;">
            <h2 class="text-xl font-semibold">Módulo Eventos</h2>
            <input type="text" placeholder="Buscar..." class="form-input" wire:model.live.debounce.500ms="search">
            <!-- Filtros adicionales -->
            <div class="flex flex-wrap gap-4 mt-4">
                <!-- Filtro: Orden -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fechas</label>
                    <select wire:model.live="orden"
                        class="form-select block w-full border border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                        <option value="desc">Primeras Fechas</option>
                        <option value="asc">Ultimas Fechas
                        <option>
                    </select>
                </div>

                <!-- Filtro: Modalidad -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Modalidad</label>
                    <select wire:model.live="modalidad"
                        class="form-select block w-full border border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                        <option value="">Todas</option>
                        <option value="presencial">Presencial</option>
                        <option value="virtual">Virtual</option>
                    </select>
                </div>

                <!-- Filtro: Estado -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                    <select wire:model.live="estado"
                        class="form-select block w-full border border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                        <option value="">Todos</option>
                        <option value="activo">Activo</option>
                        <option value="inactivo">Inactivo</option>
                    </select>
                </div>
            </div>
        </div>
        <button class="btn btn-primary" style="max-width: 200px;" wire:click="abrirModal('modal-home')">
            Nuevo Evento
        </button>
    </div>
    <hr style="margin-top: 20px; margin-bottom: 10px;">
    <div class="overflow-x-auto">
        <table class="table min-w-full bg-white border border-gray-200 rounded-lg shadow-sm">
            <thead class="bg-gray-100 text-gray-600 uppercase text-xs font-semibold">
                <tr>
                    <th class="px-4 py-3 text-left" style="width: 100px"></th>
                    <th class="px-4 py-3 text-left">Titulo</th>
                    <th class="px-4 py-3 text-left">Descripcion</th>
                    <th class="px-4 py-3 text-left">Hora de comienzo</th>
                    <th class="px-4 py-3 text-left">Hora Fin</th>
                    <th class="px-4 py-3 text-left">Localización</th>
                    <th class="px-4 py-3 text-left">Email</th>
                    <th class="px-4 py-3 text-left">Telefono</th>
                    <th class="px-4 py-3 text-left">Estado</th>
                    <th class="px-4 py-3 text-left">Acciones</th>
                </tr>
            </thead>
            <tbody class="text-gray-700 text-sm">
                @foreach ($records as $evento)
                <tr class="border-b hover:bg-gray-50">
                    <td class="px-4 py-3" style="width: 100px">
                        @if ($evento->main_image)
                        <img src="{{ $evento->main_image }}" alt="{{ $evento->title }}"
                            class="w-12 h-12 object-cover rounded">
                        @else
                        <img src="{{ url('/') }}/images/imagen_placeholder.avif" alt="Imagen"
                            class="w-12 h-12 object-cover rounded">
                        @endif
                    </td>
                    <td class="px-4 py-3">{{ $evento->title }}</td>
                    <td class="px-4 py-3">{{ substr($evento->description, 0, 50) }}{{ strlen($evento->description) > 50
                        ? '...' : '' }}</td>
                    <td class="px-4 py-3">{{ \Carbon\Carbon::parse($evento->start_time)->format('d/m/Y h:i A') }}</td>
                    <td class="px-4 py-3">{{ \Carbon\Carbon::parse($evento->end_time)->format('d/m/Y h:i A') }}</td>
                    <td class="px-4 py-3">{{ $evento->location }}</td>
                    <td class="px-4 py-3">{{ $evento->contact_email }}</td>
                    <td class="px-4 py-3">{{ $evento->contact_phone }}</td>
                    <td class="px-4 py-3">
                        @if ($evento->is_active)
                        <span
                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                            Activo
                        </span>
                        @else
                        <span
                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                            Inactivo
                        </span>
                        @endif
                    </td>
                    <td class="px-4 py-3 flex space-x-2 items-center">
                        <button
                            class="bg-yellow-500 text-white px-3 py-1 rounded-md hover:bg-yellow-600 transition text-sm cursor-pointer"
                            wire:click="edit('{{ $evento->id }}')">
                            Editar
                        </button>
                        <button
                            class="bg-red-500 text-white px-3 py-1 rounded-md hover:bg-red-600 transition text-sm cursor-pointer"
                            onclick="confirmarEliminar({{ $evento->id }})">
                            Eliminar
                        </button>
                        <button
                            class="bg-green-500 text-white px-3 py-1 rounded-md hover:bg-green-600 transition text-sm cursor-pointer"
                            style="max-width: 200px;" wire:click="sesiones('{{$evento->id}}')">
                            Sesiones
                        </button>
                        <button
                            class="bg-cyan-500 text-white px-3 py-1 rounded-md hover:bg-cyan-600 transition text-sm cursor-pointer"
                            style="max-width: 200px;" wire:click="participantesEventos('{{$evento->id}}')">
                            Participantes
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="mt-4">
            {{ $records->links() }}
        </div>
    </div>
    <!-- Contenido - fin -->

    <!-- Diploma template - Inicio -->
    <style>
        .diploma-container * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        .diploma-container {
            width: 1000px;
            height: 707px;
            background: linear-gradient(135deg, #e8e8e8 0%, #ffffff 100%);
            position: relative;
            overflow: hidden;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
        }

        /* Decoraciones geométricas */
        .diploma-container .corner-decoration {
            position: absolute;
            width: 0;
            height: 0;
        }

        .diploma-container .top-left {
            top: 0;
            left: 0;
            border-left: 180px solid #1ba3c6;
            border-bottom: 180px solid transparent;
        }

        .diploma-container .top-left-inner {
            top: 0;
            left: 0;
            border-left: 140px solid #1e3a8a;
            border-bottom: 140px solid transparent;
        }

        .diploma-container .bottom-right {
            bottom: 0;
            right: 0;
            border-right: 180px solid #1e3a8a;
            border-top: 180px solid transparent;
        }

        .diploma-container .bottom-right-inner {
            bottom: 0;
            right: 0;
            border-right: 140px solid #1ba3c6;
            border-top: 140px solid transparent;
        }

        /* Contenido del diploma */
        .diploma-container .diploma-content {
            position: relative;
            padding: 80px 100px;
            text-align: center;
        }

        .diploma-container .title {
            color: #1e3a8a;
            font-size: 90px;
            font-weight: 900;
            letter-spacing: 8px;
            margin-bottom: 25px;
            line-height: 1;
            margin-top: -25px;
        }

        .diploma-container .subtitle {
            color: #000;
            font-size: 32px;
            font-weight: 700;
            letter-spacing: 2px;
            margin-bottom: 30px;
        }

        .diploma-container .intro-text {
            color: #000;
            font-size: 18px;
            margin-bottom: 20px;
            font-weight: 400;
        }

        .diploma-container .recipient-name {
            font-family: 'Brush Script MT', cursive;
            font-size: 56px;
            color: #000;
            margin: 20px 0;
            padding-bottom: 10px;
            border-bottom: 2px solid #000;
            font-weight: normal;
            font-style: italic;
            white-space: nowrap;
            word-break: keep-all;
        }

        .diploma-container .description {
            color: #000;
            font-size: 14px;
            line-height: 1.6;
            margin: 20px 0;
            text-align: justify;
            font-weight: 500;
        }

        .diploma-container .university-info {
            color: #000;
            font-size: 13px;
            margin: 25px 0;
            font-weight: 500;
        }

        .diploma-container .signatures {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-top: 20px;
            padding: 0 50px;
        }

        .diploma-container .signature-block {
            text-align: center;
            flex: 1;
        }

        .diploma-container .signature-line {
            width: 200px;
            height: 1px;
            background-color: #000;
            margin: 0 auto 8px;
        }

        .diploma-container .signature-name {
            font-size: 13px;
            font-weight: 700;
            color: #000;
            line-height: 1.4;
        }

        .diploma-container .logos {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 40px;
            flex: 0 0 auto;
        }

        .diploma-container .logo-circle {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            border: 3px solid #1e3a8a;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #fff;
        }

        .diploma-container .logo-text {
            font-size: 24px;
            font-weight: 900;
            color: #1e3a8a;
            margin-bottom: 20px;
        }

        .diploma-container .university-seal {
            width: 100px;
            height: 100px;
            border: 3px solid #c41e3a;
            border-radius: 50%;
            background-color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        .diploma-container .seal-inner {
            width: 90%;
            height: 90%;
            border: 2px solid #c41e3a;
            border-radius: 50%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            font-size: 8px;
            color: #c41e3a;
            font-weight: 700;
            text-align: center;
            padding: 5px;
        }

        .diploma-container .seal-inner span {
            margin-bottom: 10px;
        }

        .diploma-container .university-logo {
            position: absolute;
            top: 75px;
            left: 15%;
            transform: translateX(-50%);
            width: 180px;
            height: auto;
        }

        .diploma-container .university-logo img {
            width: 100%;
            height: auto;
        }

        .diploma-container .qr-code {
            position: absolute;
            top: 75px;
            right: 15%;
            transform: translateX(50%);
            width: 140px;
            height: 140px;
        }

        .diploma-container .qr-code img {
            width: 100%;
            height: 100%;
        }

        .diploma-container .qr-code-text {
            position: absolute;
            top: 100%;
            left: 50%;
            transform: translate(-50%, 10px);
            font-size: 10px;
            color: #000;
            width: 160px;
            text-align: center;
        }

        .diploma-container .qr-code-text span {
            font-weight: 700;
        }

        .diploma-container .unique_code {
            position: absolute;
            bottom: 20px;
            left: 20px;
            font-size: 10px;
            color: #555;
            font-weight: 500;
        }

        .diploma-container .unique_code span {
            font-weight: 700;
        }
    </style>
    <div class="diploma-container" style="display: none">
        <!-- Decoraciones de esquinas -->
        <div class="corner-decoration top-left"></div>
        <div class="corner-decoration top-left-inner"></div>
        <div class="corner-decoration bottom-right"></div>
        <div class="corner-decoration bottom-right-inner"></div>

        <!-- Logo de la universidad -->
        <div class="university-logo">
            <img src="{{ url('/') }}/images/logoues.png" alt="Logo Universidad de El Salvador">
        </div>

        <!-- codigo QR -->
        <div class="qr-code">
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=140x140&data=https://example.com/certificados/codigo_unico"
                alt="Código QR">
            <div class="qr-code-text">Escanea este código para verificar tu diploma.</div>
        </div>

        <!-- Contenido del diploma -->
        <div class="diploma-content">
            <h1 class="title">DIPLOMA</h1>
            <h2 class="subtitle">DE RECONOCIMIENTO</h2>

            <p class="intro-text">Se le otorga el presente diploma a</p>

            <div class="recipient-name">Nombre de Ejemplo</div>

            <p class="description">
                En reconocimiento a su valiosa participación y aporte al desarrollo de
                <strong class="event_name">[Nombre del Evento]</strong>, demostrando excelencia, compromiso y entusiasmo
                en todas las
                actividades realizadas.
            </p>

            <p class="university-info">
                Universidad de El Salvador, Facultad Multidisciplinaria Oriental, <span class="date">16 de Octubre de
                    2025</span>
            </p>

            <div class="signatures">
                <div class="signature-block">
                    <div class="signature-line"></div>
                    <div class="signature-name">
                        <!-- Karla Orellana<br> -->
                        Presidente(a) de ASEIS.
                    </div>
                </div>

                <div class="logos">
                    <div class="logo-circle">
                        <div class="logo-text">ASEIS</div>
                    </div>
                    <div class="university-seal">
                        <div class="seal-inner">
                            <span>
                                UNIVERSIDAD<br>DE EL<br>SALVADOR
                            </span>
                        </div>
                    </div>
                </div>

                <div class="signature-block">
                    <div class="signature-line"></div>
                    <div class="signature-name">
                        <!-- Msc. Iván Franco<br> -->
                        Decano(a) de la UES-FMO.
                    </div>
                </div>
            </div>
        </div>

        <div class="unique_code">Código de verificación: <span class="code">[Código único]</span></div>
    </div>
    <!-- Diploma template - Fin -->
</main>


<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    async function ensureFontsLoaded() {
        if (document.fonts && document.fonts.ready) {
            try { await document.fonts.ready; } catch (e) { /* ignore */ }
        }
    }

    function autoFitRecipientName(element, maxFontSize = 56, minFontSize = 20) {
        if (!element || !element.isConnected) return;

        // guardamos estado previo
        const prevWhite = element.style.whiteSpace;
        const prevDisplay = element.style.display;

        // Forzamos single-line para medir correctamente
        element.style.whiteSpace = 'nowrap';
        element.style.display = 'inline-block'; // evita problemas de contenedor flexible

        // Calculamos ancho disponible real: ancho del contenedor padre (diploma-content)
        // menos paddings del elemento si existen.
        const parent = element.parentElement || element;
        const parentRect = parent.getBoundingClientRect();
        const parentStyle = getComputedStyle(parent);

        // ancho interior del padre (content-box)
        const parentPaddingLeft = parseFloat(parentStyle.paddingLeft || 0);
        const parentPaddingRight = parseFloat(parentStyle.paddingRight || 0);
        const availableWidth = Math.max(10, parentRect.width - parentPaddingLeft - parentPaddingRight);

        // Búsqueda binaria entre min y max
        let low = minFontSize;
        let high = maxFontSize;
        let best = minFontSize;

        // Aseguramos que al menos mid se aplique una vez
        while (low <= high) {
            const mid = Math.floor((low + high) / 2);
            element.style.fontSize = mid + 'px';

            // Forzar reflow y medir
            const textWidth = element.scrollWidth;

            if (textWidth <= availableWidth) {
                best = mid;        // mid cabe, intentar más grande
                low = mid + 1;
            } else {
                high = mid - 1;    // mid no cabe, reducir
            }
        }

        element.style.fontSize = best + 'px';

        // Fallback extra (si por redondeo sigue desbordando)
        let safety = 0;
        while (element.scrollWidth > availableWidth && parseFloat(element.style.fontSize) > minFontSize && safety < 20) {
            const cur = Math.max(minFontSize, Math.floor(parseFloat(element.style.fontSize) - 1));
            element.style.fontSize = cur + 'px';
            safety++;
        }

        // restaurar propiedades opcionales
        element.style.whiteSpace = prevWhite;
        element.style.display = prevDisplay;
    }

    async function generateDiplomas(dataArray, namefile = null) {
        const pdf = new jsPDF({ orientation: "landscape", unit: "px", format: [1000, 707] });

        await ensureFontsLoaded();

        const capitalizeName = (name) =>
            name
                .toLocaleLowerCase('es-ES')
                .replace(/\p{L}+(-\p{L}+)?/gu, (word) =>
                    word.charAt(0).toLocaleUpperCase('es-ES') + word.slice(1)
                );

        for (let i = 0; i < dataArray.length; i++) {
            const { recipient_name, event_name, date, qr_code, code } = dataArray[i];

            const template = document.querySelector(".diploma-container").cloneNode(true);
            template.querySelector(".recipient-name").textContent = capitalizeName(recipient_name);
            template.querySelector(".event_name").textContent = event_name;
            template.querySelector(".date").textContent = date;
            template.querySelector(".qr-code img").src = qr_code;
            template.querySelector(".unique_code .code").textContent = code;

            Object.assign(template.style, {
                position: "fixed",
                top: "-2000px",
                left: "0",
                display: "block"
            });

            document.body.appendChild(template);

            await new Promise(requestAnimationFrame);
            autoFitRecipientName(template.querySelector(".recipient-name"), 56, 10);
            await new Promise(requestAnimationFrame);

            const canvas = await html2canvas(template, { scale: 1.5 });
            const imgData = canvas.toDataURL("image/jpeg", 0.8);

            if (i > 0) pdf.addPage();
            pdf.addImage(imgData, "JPEG", 0, 0, 1000, 707);

            document.body.removeChild(template);
        }

        const eventSlug = dataArray[0].event_name.toLowerCase().replaceAll("-", " ").replace(/[^\w\s]/gi, '').replace(/\s+/g, '_');
        const dateNow = new Date();
        const formattedDate = `${dateNow.getFullYear()}-${dateNow.getMonth() + 1}-${("0" + dateNow.getDate()).slice(-2)}`;
        pdf.save(namefile || `diplomas_${eventSlug}_numitems_${dataArray.length}_date_${formattedDate}.pdf`);
    }

    document.addEventListener('livewire:initialized', function() {
            let is_paid = document.getElementById('is_paid');
            let precio = document.getElementById('price');

            Livewire.on('cerrar-modal', function(modal) {
                let modalElement = document.getElementById(modal[0].modal);
                if (modalElement) {
                    closeModal(modalElement);
                }
            });

            Livewire.on('inscripcion-message', (data) => {
                const idEvento = data[0];
                const message = data[1];
                const metodo = data[2];

                Swal.fire('Éxito', message, 'success').then(() => {
                    @this.call(metodo, idEvento);
                });
            });

            Livewire.on('confirmar-inscripcion', ({
                idEvento, idParticipante, idSesion, title, text, metodo
            }) => {
                Swal.fire({
                    title: title,
                    text: text,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Sí',
                    cancelButtonText: 'No',
                    customClass: {
                        container: 'swal2-container z-[9999]'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        if (idEvento !== null) {
                            Livewire.dispatch(metodo, { idEvento, idParticipante });
                        } else if (idSesion !== null) {
                            Livewire.dispatch(metodo, { idSesion, idParticipante });
                        }
                    }
                });
            });

            Livewire.on('confirmar-cancelacion', ({
                idEvento, idParticipante, idSesion, title, text, metodo
            }) => {
                Swal.fire({
                    title: title,
                    text: text,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Sí',
                    cancelButtonText: 'No',
                    customClass: {
                        container: 'swal2-container z-[9999]'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        if (idEvento !== null) {
                            Livewire.dispatch(metodo, { idEvento, idParticipante });
                        } else if (idSesion !== null) {
                            Livewire.dispatch(metodo, { idSesion, idParticipante });
                        }
                    }
                });
            });

            Livewire.on('abrir-modal', function(modal) {
                let modalElement = document.getElementById(modal[0].modal);
                if (modalElement) {
                    openModal(modalElement);
                    let modelDialog = modalElement.querySelector('.modal-dialog');
                    if (modelDialog) {
                        modelDialog.scrollTop = 0;
                    }

                    if (modalElement.querySelector('#is_paid') != null) {
                        let precio = document.getElementById('price');
                        precio.style.display = 'none';
                        if (@this.fields.is_paid == 0 || @this.fields.is_paid == '' || @this.fields.is_paid == '0' || @this.fields.is_paid == null) {
                            precio.style.display = 'none';
                        } else {
                            precio.style.display = 'block';
                        }
                    }
                }
            });

            if (is_paid) {
                if (@this.fields.is_paid == 0 || @this.fields.is_paid == '' || @this.fields.is_paid == '0' || @this.fields.is_paid == null) {
                    precio.style.display = 'none';
                    @this.fields.price = 0;
                } else {
                    precio.style.display = 'block';
                }
                
                is_paid.addEventListener('change', function() {
                    let precio = document.getElementById('price');
                    if (@this.fields.is_paid == 0 || @this.fields.is_paid == '' || @this.fields.is_paid == '0' || @this.fields.is_paid == null) {
                        precio.style.display = 'none';
                        @this.fields.price = 0;
                    } else {
                        precio.style.display = 'block';
                    }
                });
            }

            Livewire.on('generate-diplomas', async (data) => {
                data = data[0] ?? [];
                const btn = document.querySelector('#generarAllDiplomas');
                let originalText = btn.innerHTML;
                btn.disabled = true;
                btn.innerHTML = `<i class="fas fa-spinner fa-spin"></i> Generando...`;
                try {
                    await generateDiplomas(data);
                    Alert(
                        '¡Éxito!',
                        'Los diplomas se han generado correctamente.',
                        'success'
                    );
                } catch (error) {
                    console.error('Error generating diplomas:', error);
                    Alert(
                        'Error',
                        'Ocurrió un error al generar los diplomas. Por favor, inténtalo de nuevo.',
                        'error'
                    );
                } finally {
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                }
            });

            Livewire.on('generate-diploma-individual', async (data) => {
                let id_participante = parseInt(data[1] ?? 0);
                data = data[0] ?? [];
                const btn = document.querySelector('.btn-diploma[data-participante="' + id_participante + '"]');
                let originalText = btn.innerHTML;
                btn.disabled = true;
                btn.innerHTML = `<i class="fas fa-spinner fa-spin"></i> Generando...`;
                try {
                    let eventoSlug = data[0].event_name.toLowerCase().replaceAll("-", " ").replace(/[^\w\s]/gi, '').replace(/\s+/g, '_');
                    const dateNow = new Date();
                    const formattedDate = `${dateNow.getFullYear()}-${dateNow.getMonth() + 1}-${("0" + dateNow.getDate()).slice(-2)}`;
                    await generateDiplomas(data, `diploma_${eventoSlug}_${data[0].recipient_name.toLowerCase().replaceAll("-", " ").replace(/\s+/g, '-')}_date_${formattedDate}.pdf`);
                    Alert(
                        '¡Éxito!',
                        'El diploma se ha generado correctamente.',
                        'success'
                    );
                } catch (error) {
                    console.error('Error generating diploma:', error);
                    Alert(
                        'Error',
                        'Ocurrió un error al generar el diploma. Por favor, inténtalo de nuevo.',
                        'error'
                    );
                } finally {
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                }
            });
    });

        const confirmarEliminar = async id => {
            if (await window.Confirm(
                    'Eliminar',
                    '¿Estas seguro de eliminar este Eventos?',
                    'warning',
                    'Si, eliminar',
                    'Cancelar'
                )) {
                Livewire.dispatch('delete', {
                    id
                });
            }
        }

        const confirmarEliminarSesion = async id => {
            if (await window.Confirm(
                    'Eliminar',
                    '¿Estas seguro de eliminar esta Sesión?',
                    'warning',
                    'Si, eliminar',
                    'Cancelar'
                )) {
                Livewire.dispatch('deleteSesion', {
                    id
                });
            }
        }
</script>