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
                    <h5 class="modal-title" id="userLabel">{{ $record_id ? 'Editar usuario' : 'Nuevo usuario' }}</h5>
                    <button type="button" class="btn-close" aria-label="Cerrar" onclick="closeModal(this.closest('.modal'))">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">Nombre Completo</label>
                        <input wire:model="fields.name" type="text" placeholder="Nombre" id="nombre"
                            class="form-control @error('fields.name') was-validated is-invalid @enderror"
                            oninput="this.value = this.value.toUpperCase();">
                        <div class="invalid-feedback">@error('fields.name') {{$message}} @enderror</div>
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
        <table class="table-auto w-full">
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