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
                        <input wire:model="fields.end_time" type="datetime-local"
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
                            class="form-control @error('fields.contact_phone') was-validated is-invalid @enderror">
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

                    <div class="form-group mb-2" id="price" style="display: none;" wire:ignore>
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
                                    <th class="px-4 py-3 text-left">ID</th>
                                    <th class="px-4 py-3 text-left">Título</th>
                                    <th class="px-4 py-3 text-left">Descripción</th>
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
                                    <td class="px-4 py-3">{{ $sesion->id }}</td>
                                    <td class="px-4 py-3">{{ $sesion->title }}</td>
                                    <td class="px-4 py-3">{{ substr($sesion->description, 0, 50) }}{{
                                        strlen($sesion->description) > 50
                                        ? '...' : '' }}</td>
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
    <!-- Contenido - inicio -->
    <div class="flex justify-between items-end flex-wrap gap-4">
        <div class="flex items-start gap-4 flex-col" style="max-width: 800px;width: 100%;">
            <h2 class="text-xl font-semibold">Módulo Eventos</h2>
            <input type="text" placeholder="Buscar..." class="form-input" wire:model.live.debounce.500ms="search">
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
                    <th class="px-4 py-3 text-left">ID</th>
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
                    <td class="px-4 py-3">{{ $evento->id }}</td>
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
</main>

<script>
    document.addEventListener('livewire:initialized', function() {
            let is_paid = document.getElementById('is_paid');
            let precio = document.getElementById('price');

            Livewire.on('cerrar-modal', function(modal) {
                let modalElement = document.getElementById(modal[0].modal);
                if (modalElement) {
                    closeModal(modalElement);
                }
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