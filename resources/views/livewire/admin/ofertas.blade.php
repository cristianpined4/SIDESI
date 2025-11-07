@section('title', "Ofertas de Empleo")

<main style="width: 100%;">
    <div class="loading" wire:loading.attr="show" show="false">
        <div class="loader"></div>
        <p class="loading-text">Cargando...</p>
    </div>

    <!-- Modal Crear/Editar Oferta -->
    <div id="modal-home" style="z-index: 3" class="modal" wire:ignore.self>
        <div class="modal-dialog" style="max-width: 1000px">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="userLabel">{{ $record_id ? 'Editar Oferta' : 'Nueva Oferta' }}</h5>
                    <button type="button" class="btn-close" aria-label="Cerrar"
                        onclick="closeModal(this.closest('.modal'))">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div class="form-group mb-2">
                            <label class="form-label">Título</label>
                            <input wire:model="fields.title" type="text" placeholder="Título de la oferta"
                                class="form-control @error('fields.title') was-validated is-invalid @enderror">
                            <div class="invalid-feedback">@error('fields.title') {{$message}} @enderror</div>
                        </div>

                        <div class="form-group mb-2">
                            <label class="form-label">Empresa</label>
                            <input wire:model="fields.company_name" type="text" placeholder="Nombre de la empresa"
                                class="form-control @error('fields.company_name') was-validated is-invalid @enderror">
                            <div class="invalid-feedback">@error('fields.company_name') {{$message}} @enderror</div>
                        </div>

                        <div class="form-group mb-2">
                            <label class="form-label">Ubicación</label>
                            <input wire:model="fields.location" type="text" placeholder="Ciudad / Región"
                                class="form-control @error('fields.location') was-validated is-invalid @enderror">
                            <div class="invalid-feedback">@error('fields.location') {{$message}} @enderror</div>
                        </div>

                        <div class="form-group mb-2">
                            <label class="form-label">Correo de contacto</label>
                            <input wire:model="fields.contact_email" type="email" placeholder="email@ejemplo.com"
                                class="form-control @error('fields.contact_email') was-validated is-invalid @enderror">
                            <div class="invalid-feedback">@error('fields.contact_email') {{$message}} @enderror</div>
                        </div>

                        <div class="form-group mb-2">
                            <label class="form-label">Teléfono de contacto</label>
                            <input id="phone" wire:model="fields.contact_phone" type="text" placeholder="Número de contacto"
                                class="form-control @error('fields.contact_phone') was-validated is-invalid @enderror"
                                onkeypress="return /[0-9]/.test(String.fromCharCode(event.which || event.keyCode));">
                                
                            <div class="invalid-feedback">@error('fields.contact_phone') {{$message}} @enderror</div>
                        </div>

                        <div class="form-group mb-2">
                            <label class="form-label">Salario</label>
                            <input wire:model="fields.salary" type="number" step="0.01" min="0"
                                placeholder="Ej. 10000.00"
                                class="form-control @error('fields.salary') was-validated is-invalid @enderror">
                            <div class="invalid-feedback">@error('fields.salary') {{$message}} @enderror</div>
                        </div>

                        <div class="form-group mb-2">
                            <label class="form-label">Vacantes</label>
                            <input wire:model="fields.vacancies" type="number" min="1"
                                class="form-control @error('fields.vacancies') was-validated is-invalid @enderror">
                            <div class="invalid-feedback">@error('fields.vacancies') {{$message}} @enderror</div>
                        </div>

                        <div class="form-group mb-2">
                            <label class="form-label">Imagen Principal</label>
                            <input wire:model="file" type="file" accept="image/*"
                                class="form-control @error('file') was-validated is-invalid @enderror">
                            <div class="invalid-feedback">@error('file') {{$message}} @enderror</div>
                        </div>

                        <div class="form-group mb-2">
                            <label class="form-label">Fecha límite de postulación</label>
                            <input wire:model="fields.application_deadline" type="datetime-local"
                                class="form-control @error('fields.application_deadline') was-validated is-invalid @enderror">
                            <div class="invalid-feedback">@error('fields.application_deadline') {{$message}} @enderror
                            </div>
                        </div>

                        <div class="form-group mb-2">
                            <label class="form-label">Estado</label>
                            <select wire:model="fields.is_active"
                                class="form-control @error('fields.is_active') was-validated is-invalid @enderror">
                                <option value="0">Inactiva</option>
                                <option value="1">Activa</option>
                            </select>
                            <div class="invalid-feedback">@error('fields.is_active') {{$message}} @enderror</div>
                        </div>
                    </div>

                    <div class="mb-2 w-full flex items-center justify-center">
                        @if (isset($fields['main_image']) && $file == null)
                        <img class="w-1/2" src="{{ $fields['main_image'] }}" alt="imagen" />
                        @elseif ($file != null)
                        <img class="w-1/2" src="{{ $file->temporaryUrl() }}" alt="imagen" />
                        @else
                        <img class="w-1/2" src="{{ url('/') }}/images/imagen_placeholder.avif" alt="imagen" />
                        @endif
                    </div>

                    <div class="form-group mb-2">
                        <label class="form-label">Descripción</label>
                        <textarea wire:model="fields.description" placeholder="Descripción de la oferta"
                            class="form-control @error('fields.description') was-validated is-invalid @enderror"
                            rows="5"></textarea>
                        <div class="invalid-feedback">@error('fields.description') {{$message}} @enderror</div>
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
    <!-- Fin Modal -->

    <!-- Encabezado y acciones -->
    <div class="flex justify-between items-end flex-wrap gap-4">
        <div class="flex items-start gap-4 flex-col" style="max-width: 800px;width: 100%;">
            <h2 class="text-xl font-semibold">Módulo Ofertas de Empleo</h2>
            <input type="text" placeholder="Buscar por título, empresa o ubicación..." class="form-input"
                wire:model.live.debounce.500ms="search">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3 w-full">
                <div>
                    <label class="form-label">Filtrar por estado</label>
                    <select class="form-control" wire:model.live="filter_active">
                        <option value="">Todos</option>
                        <option value="1">Activas</option>
                        <option value="0">Inactivas</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">Orden</label>
                    <select class="form-control" wire:model.live="order_direction">
                        <option value="desc">Más reciente primero</option>
                        <option value="asc">Más antiguo primero</option>
                    </select>
                </div>
            </div>
        </div>
        <button class="btn btn-primary" style="max-width: 200px;" wire:click="abrirModal('modal-home')">
            Nueva Oferta
        </button>
    </div>
    <hr style="margin-top: 20px; margin-bottom: 10px;">

    <!-- Listado -->
    <div class="overflow-x-auto">
        <table class="table min-w-full bg-white border border-gray-200 rounded-lg shadow-sm">
            <thead class="bg-gray-100 text-gray-600 uppercase text-xs font-semibold">
                <tr>
                    <th class="px-4 py-3 text-left" style="width: 100px"></th>
                    <th class="px-4 py-3 text-left">Título</th>
                    <th class="px-4 py-3 text-left">Empresa</th>
                    <th class="px-4 py-3 text-left">Ubicación</th>
                    <th class="px-4 py-3 text-left">Vacantes</th>
                    <th class="px-4 py-3 text-left">Salario</th>
                    <th class="px-4 py-3 text-left">Estado</th>
                    <th class="px-4 py-3 text-left">Límite</th>
                    <th class="px-4 py-3 text-left">Publicado</th>
                    <th class="px-4 py-3 text-left">Acciones</th>
                </tr>
            </thead>
            <tbody class="text-gray-700 text-sm">
                @foreach ($records as $oferta)
                <tr class="border-b hover:bg-gray-50">
                    <td class="px-4 py-3" style="width: 100px">
                        @if ($oferta->main_image)
                        <img src="{{ $oferta->main_image }}" alt="{{ $oferta->title }}"
                            class="w-12 h-12 object-cover rounded">
                        @else
                        <img src="{{ url('/') }}/images/imagen_placeholder.avif" alt="Imagen"
                            class="w-12 h-12 object-cover rounded">
                        @endif
                    </td>
                    <td class="px-4 py-3">{{ $oferta->title }}</td>
                    <td class="px-4 py-3">{{ $oferta->company_name }}</td>
                    <td class="px-4 py-3">{{ $oferta->location }}</td>
                    <td class="px-4 py-3">{{ $oferta->vacancies }}</td>
                    <td class="px-4 py-3">{{ $oferta->salary ? number_format($oferta->salary, 2) : '-' }}</td>
                    <td class="px-4 py-3">
                        @if ($oferta->is_active)
                        <span
                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Activa</span>
                        @else
                        <span
                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-200 text-gray-700">Inactiva</span>
                        @endif
                    </td>
                    <td class="px-4 py-3">{{ optional($oferta->application_deadline)->format('d/m/Y h:i A') }}</td>
                    <td class="px-4 py-3">{{ optional($oferta->created_at)->format('d/m/Y h:i A') }}</td>
                    <td class="px-4 py-3 flex space-x-2 items-center">
                        <button
                            class="bg-yellow-500 text-white px-3 py-1 rounded-md hover:bg-yellow-600 transition text-sm cursor-pointer"
                            wire:click="edit('{{ $oferta->id }}')">Editar</button>
                        <button
                            class="bg-red-500 text-white px-3 py-1 rounded-md hover:bg-red-600 transition text-sm cursor-pointer"
                            onclick="confirmarEliminar({{ $oferta->id }})">Eliminar</button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="mt-4">
            {{ $records->links() }}
        </div>
    </div>
</main>

<script>
    document.addEventListener('livewire:initialized', function() {
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
            }
        });
    });

    const confirmarEliminar = async id => {
        if (await window.Confirm(
            'Eliminar',
            '¿Estas seguro de eliminar esta Oferta?',
            'warning',
            'Si, eliminar',
            'Cancelar'
        )) {
            Livewire.dispatch('delete', { id });
        }
    }
</script>