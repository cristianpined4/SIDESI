@section('title', "Documentos")

<main style="width: 100%;">
    <div class="loading" wire:loading.attr="show" show="false">
        <div class="loader"></div>
        <p class="loading-text">Cargando...</p>
    </div>

    <div id="modal-home" style="z-index: 3" class="modal" wire:ignore.self>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="userLabel">{{ $record_id ? 'Editar documento' : 'Nuevo documento' }}</h5>
                    <button type="button" class="btn-close" aria-label="Cerrar" onclick="closeModal(this.closest('.modal'))">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group mb-2">
                        <label class="form-label">Nombre del documento</label>
                        <input wire:model="fields.title" type="text" placeholder="Nombre del documento" class="form-control @error('fields.title') was-validated is-invalid @enderror">
                        <div class="invalid-feedback">@error('fields.title') {{$message}} @enderror</div>
                    </div>
                    <div class="form-group mb-2">
                        <label class="form-label">Descripción</label>
                        <input wire:model="fields.description" type="text" placeholder="Descripción" class="form-control @error('fields.description') was-validated is-invalid @enderror">
                        <div class="invalid-feedback">@error('fields.description') {{$message}} @enderror</div>
                    </div>
                    <div class="form-group mb-2">
                        <label class="form-label">Archivo</label>
                        <input wire:model="file" type="file" class="form-control @error('file') was-validated is-invalid @enderror">
                        <div class="invalid-feedback">@error('file') {{$message}} @enderror</div>
                    </div>
                    <div class="form-group mb-2">
                        <label class="form-label">Visibilidad</label>
                        <select wire:model="fields.visibility" class="form-control @error('fields.visibility') was-validated is-invalid @enderror">
                            <option value="">Seleccione...</option>
                            <option value="publico">Público</option>
                            <option value="privado">Privado</option>
                        </select>
                        <div class="invalid-feedback">@error('fields.visibility') {{$message}} @enderror</div>
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

    <div class="flex justify-between items-end flex-wrap gap-4">
        <div class="flex items-start gap-4 flex-col" style="max-width: 800px;width: 100%;">
            <h2 class="text-xl font-semibold">Módulo Documentos</h2>
            <input type="text" placeholder="Buscar documento..." class="form-input" wire:model.live.debounce.500ms="search">
        </div>
        <button class="btn btn-primary" style="max-width: 200px;" wire:click="abrirModal('modal-home')">Nuevo Documento</button>
    </div>
    <hr style="margin-top: 20px; margin-bottom: 10px;">
    <div class="overflow-x-auto">
        <table class="table w-full bg-white border border-gray-200 rounded-lg shadow-sm table-fixed">
            <thead class="bg-gray-100 text-gray-600 uppercase text-xs font-semibold">
                <tr>
                    <th class="px-4 py-3 text-left">Nombre</th>
                    <th class="px-4 py-3 text-left">Descripción</th>
                    <th class="px-4 py-3 text-left">Tipo</th>
                    <th class="px-4 py-3 text-left">Visibilidad</th>
                    <th class="px-4 py-3 text-left">Fecha</th>
                    <th class="px-4 py-3 text-center">Acciones</th>
                </tr>
            </thead>
            <tbody class="text-gray-700 text-sm">
                @foreach ($records as $doc)
                <tr class="border-b hover:bg-gray-50">
                    <td class="px-4 py-3" title="{{ $doc->name }}">{{ substr($doc->name, 0, 100) }}{{
                                        strlen($doc->name) > 100
                                        ? '...' : '' }}</td>
                    <td class="px-4 py-3" title="{{ $doc->description }}">{{ substr($doc->description, 0, 100) }}{{
                                        strlen($doc->description) > 100
                                        ? '...' : '' }}</td>
                    <td class="px-4 py-3">{{ strtoupper($doc->type) }}</td>
                    <td class="px-4 py-3">{{ ucfirst($doc->visibility) }}</td>
                    <td class="px-4 py-3">{{ $doc->created_at->format('d/m/Y H:i') }}</td>
                    <td class="px-4 py-3 flex space-x-2 justify-center items-center whitespace-nowrap text-center">
                        <button class="bg-yellow-500 text-white px-3 py-1 rounded-md hover:bg-yellow-600 transition text-sm cursor-pointer" wire:click="edit('{{ $doc->id }}')">Editar</button>
                        <button class="bg-red-500 text-white px-3 py-1 rounded-md hover:bg-red-600 transition text-sm cursor-pointer" onclick="confirmarEliminar({{ $doc->id }})">Eliminar</button>
                        <a class="bg-green-500 text-white px-3 py-1 rounded-md hover:bg-green-600 transition text-sm cursor-pointer" style="max-width: 200px;" href="{{ asset($doc->path) }}" target="_blank">Ver</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @if (!empty($records) && $records->count() && method_exists($records, 'links'))
        <div class="mt-4">
            {{ $records->links() }}
        </div>
        @endif
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
                '¿Estas seguro de eliminar este Documento?',
                'warning',
                'Si, eliminar',
                'Cancelar'
            )) {
            Livewire.dispatch('delete', { id });
        }
    }
</script>
</div>-->

