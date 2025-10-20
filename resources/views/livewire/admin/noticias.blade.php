@section('title', "Noticias")

<main style="width: 100%;">
    <div class="loading" wire:loading.attr="show" show="false">
        <div class="loader"></div>
        <p class="loading-text">Cargando...</p>
    </div>

    <!-- Modal Crear/Editar Noticia -->
    <div id="modal-home" style="z-index: 3" class="modal" wire:ignore.self>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="userLabel">{{ $record_id ? 'Editar Contenido' : 'Nuevo Contenido' }}</h5>
                    <button type="button" class="btn-close" aria-label="Cerrar"
                        onclick="closeModal(this.closest('.modal'))">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group mb-2">
                        <label class="form-label">Título</label>
                        <input wire:model="fields.title" type="text" placeholder="Título del contenido"
                            class="form-control @error('fields.title') was-validated is-invalid @enderror">
                        <div class="invalid-feedback">@error('fields.title') {{$message}} @enderror</div>
                    </div>

                    <div class="form-group mb-2">
                        <label class="form-label">Descripción</label>
                        <textarea wire:model="fields.description" placeholder="Descripción corta"
                            class="form-control @error('fields.description') was-validated is-invalid @enderror" rows="3"></textarea>
                        <div class="invalid-feedback">@error('fields.description') {{$message}} @enderror</div>
                    </div>

                    <div class="form-group mb-2">
                        <label class="form-label">Tipo de contenido</label>
                        <select wire:model="fields.content_type" class="form-control @error('fields.content_type') was-validated is-invalid @enderror">
                            <option value="">Seleccione...</option>
                            <option value="Evento">Evento</option>
                            <option value="Convocatoria">Convocatoria</option>
                            <option value="Noticia">Noticia</option>
                            <option value="Información">Información</option>
                        </select>
                        <div class="invalid-feedback">@error('fields.content_type') {{$message}} @enderror</div>
                    </div>

                    <div class="form-group mb-2">
                        <label class="form-label">Estado</label>
                        <select wire:model="fields.status" class="form-control @error('fields.status') was-validated is-invalid @enderror">
                            <option value="">Seleccione...</option>
                            <option value="draft">Borrador</option>
                            <option value="published">Publicado</option>
                            <option value="archived">Archivado</option>
                        </select>
                        <div class="invalid-feedback">@error('fields.status') {{$message}} @enderror</div>
                    </div>

                    <div class="form-group mb-2">
                        <label class="form-label">Imagen Principal</label>
                        <input wire:model="file" type="file" accept="image/*"
                            class="form-control @error('file') was-validated is-invalid @enderror">
                        <div class="invalid-feedback">@error('file') {{$message}} @enderror</div>
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
                        <label class="form-label">Contenido (cuerpo)</label>
                        <textarea wire:model="body" placeholder="Contenido largo del contenido"
                            class="form-control" rows="6"></textarea>
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
    <!-- Fin Modal -->

    <!-- Encabezado y acciones -->
    <div class="flex justify-between items-end flex-wrap gap-4">
        <div class="flex items-start gap-4 flex-col" style="max-width: 800px;width: 100%;">
            <h2 class="text-xl font-semibold">Módulo Contenidos</h2>
            <input type="text" placeholder="Buscar..." class="form-input" wire:model.live.debounce.500ms="search">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3 w-full">
                <div>
                    <label class="form-label">Filtrar por tipo</label>
                    <select class="form-control" wire:model.live="filter_type">
                        <option value="">Todos</option>
                        <option value="Evento">Evento</option>
                        <option value="Convocatoria">Convocatoria</option>
                        <option value="Noticia">Noticia</option>
                        <option value="Información">Información</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">Filtrar por estado</label>
                    <select class="form-control" wire:model.live="filter_status">
                        <option value="">Todos</option>
                        <option value="published">Publicado</option>
                        <option value="draft">Borrador</option>
                        <option value="archived">Archivado</option>
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
            Nuevo Contenido
        </button>
    </div>
    <hr style="margin-top: 20px; margin-bottom: 10px;">

    <!-- Listado -->
    <div class="overflow-x-auto">
        <table class="table min-w-full bg-white border border-gray-200 rounded-lg shadow-sm">
            <thead class="bg-gray-100 text-gray-600 uppercase text-xs font-semibold">
                <tr>
                    <th class="px-4 py-3 text-left"></th>
                    <th class="px-4 py-3 text-left">Título</th>
                    <th class="px-4 py-3 text-left">Descripción</th>
                    <th class="px-4 py-3 text-left">Tipo</th>
                    <th class="px-4 py-3 text-left">Estado</th>
                    <th class="px-4 py-3 text-left">Publicado</th>
                    <th class="px-4 py-3 text-left">Acciones</th>
                </tr>
            </thead>
            <tbody class="text-gray-700 text-sm">
                @foreach ($records as $noticia)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-4 py-3">
                            @if ($noticia->main_image)
                                <img src="{{ $noticia->main_image }}" alt="{{ $noticia->title }}" class="w-12 h-12 object-cover rounded">
                            @else
                                <img src="{{ url('/') }}/images/imagen_placeholder.avif" alt="Imagen" class="w-12 h-12 object-cover rounded">
                            @endif
                        </td>
                        <td class="px-4 py-3">{{ $noticia->title }}</td>
                        <td class="px-4 py-3">{{ substr($noticia->description, 0, 70) }}{{ strlen($noticia->description) > 70 ? '...' : '' }}</td>
                        <td class="px-4 py-3">{{ $noticia->content_type }}</td>
                        <td class="px-4 py-3">
                            @if ($noticia->status === 'published')
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Publicado</span>
                            @elseif ($noticia->status === 'draft')
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Borrador</span>
                            @else
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-200 text-gray-700">Archivado</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">{{ optional($noticia->created_at)->format('d/m/Y h:i A') }}</td>
                        <td class="px-4 py-3 flex space-x-2 items-center">
                            <button class="bg-yellow-500 text-white px-3 py-1 rounded-md hover:bg-yellow-600 transition text-sm cursor-pointer"
                                wire:click="edit('{{ $noticia->id }}')">Editar</button>
                            <button class="bg-red-500 text-white px-3 py-1 rounded-md hover:bg-red-600 transition text-sm cursor-pointer"
                                onclick="confirmarEliminar({{ $noticia->id }})">Eliminar</button>
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
            '¿Estas seguro de eliminar esta Noticia?',
            'warning',
            'Si, eliminar',
            'Cancelar'
        )) {
            Livewire.dispatch('delete', { id });
        }
    }
</script>
