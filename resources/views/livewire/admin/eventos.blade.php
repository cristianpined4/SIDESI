    @section('title', "Eventos")

    <main style="width: 100%;">
    <div class="loading" wire:loading.attr="show" show="false">
        <div class="loader"></div>
        <p class="loading-text">Cargando...</p>
    </div>
    <!-- modales -->
    <div id="modal-home" class="modal" wire:ignore.self>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="userLabel">{{ $record_id ? 'Editar evento' : 'Nuevo evento' }}</h5>
                    <button type="button" class="btn-close" aria-label="Cerrar" onclick="closeModal(this.closest('.modal'))">&times;</button>
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
                        <select wire:model="fields.is_paid"
                            class="form-control @error('fields.is_paid') was-validated is-invalid @enderror">
                            <option value="">Seleccione...</option>
                            <option value="1">Sí</option>
                            <option value="0">No</option>
                        </select>
                        <div class="invalid-feedback">@error('fields.is_paid') {{$message}} @enderror</div>
                    </div>

                    <div class="form-group mb-2">
                        <label class="form-label">Precio ($)</label>
                        <input wire:model="fields.price" type="number" min="0" step="0.01" placeholder="0.00"
                            class="form-control @error('fields.price') was-validated is-invalid @enderror">
                        <div class="invalid-feedback">@error('fields.price') {{$message}} @enderror</div>
                    </div>

                <div class="form-group mb-2">
                        <label class="form-label">ID del Organizador</label>
                        <select wire:model="fields.organizer_id"
                            class="form-control @error('fields.organizer_id') was-validated is-invalid @enderror">
                            <option value="">Seleccione un organizador...</option>
                            @foreach ($recordsUsers as $organizador)
                                <option value="{{ $organizador->id }}">{{ $organizador->name }} {{ $organizador->lastname }}</option>
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
                    <button type="button" class="btn btn-secondary" onclick="closeModal(this.closest('.modal'))">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
    <!-- fin modales -->

    <!-- Contenido - inicio -->
    <div class="flex justify-between items-end flex-wrap gap-4">
        <div class="flex items-start gap-4 flex-col" style="max-width: 800px;width: 100%;">
            <h2 class="text-xl font-semibold">Módulo Eventos</h2>
            <input type="text" placeholder="Buscar..." class="form-input" wire:model.live.debounce.500ms="search">
        </div>
        <button class="btn btn-primary" style="max-width: 200px;" wire:click="abrirModal('modal-home')">
            Nuevo Eventos
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
                    <td class="px-4 py-3">{{ $evento->description }}</td>
                    <td class="px-4 py-3">{{ $evento->start_time }}</td>
                    <td class="px-4 py-3">{{ $evento->end_time }}</td>
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
                            wire:click="edit('{{ $evento->id }}')">Editar</button>
                        <button class="bg-red-500 text-white px-3 py-1 rounded-md hover:bg-red-600 transition text-sm cursor-pointer"
                            onclick="confirmarEliminar({{ $evento->id }})">Eliminar</button>
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
        document.addEventListener('livewire:initialized', function () {
            Livewire.on('cerrar-modal', function (modal) {
                let modalElement = document.getElementById(modal[0].modal);
                if (modalElement) {
                    closeModal(modalElement);
                }
            });

            Livewire.on('abrir-modal', function (modal) {
                let modalElement = document.getElementById(modal[0].modal);
                if (modalElement) {
                    openModal(modalElement);
                    let modelDialog = modalElement.querySelector('.modal-dialog');
                    if (modelDialog) {
                        modelDialog.scrollTop = 0;
                    }
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
                Livewire.dispatch('delete', { id });
            }
        }
    </script>