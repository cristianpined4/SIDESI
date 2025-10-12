    @section('title', "Documentos")

    <main>
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
     <header class="encabezado">
  <div class="encabezado-contenido">
    <div class="logo-contenedor">
      <img src="img/logo.png" alt="Logo" class="logo">
      <h1 class="titulo">Documentos</h1>
    </div>
    <p class="contador">Total: 2 documentos</p>
  </div>
</header>


  <main class="contenedor-tarjetas">

    <div class="tarjeta-documento">
      <div class="icono-pdf">📄</div>
      <h3 class="titulo-doc">Manual de Usuario</h3>
      <p class="descripcion-doc">Docomento con instrucciones básiccas de uso.</p>
      <div class="botones">
        <a class="boton ver" href="docs/manual_usuario.pdf" target="_blank">Ver</a>
        <a class="boton descargar" href="docs/manual_usuario.pdf" download>Descargar</a>
      </div>
    </div>

    <div class="tarjeta-documento">
      <div class="icono-pdf">📄</div>
      <h3 class="titulo-doc">Informe Técnico</h3>
      <p class="descripcion-doc">Resumen del rendimiento del sistema .</p>
      <div class="botones">
        <a class="boton ver" href="docs/informe_tecnico.pdf" target="_blank">Ver</a>
        <a class="boton descargar" href="docs/informe_tecnico.pdf" download>Descargar</a>
      </div>
    </div>

  </main>
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
                }
            });
        });

        const confirmarEliminar = async id => {
            if (await window.Confirm(
                'Eliminar',
                '¿Estas seguro de eliminar este Documentos?',
                'warning',
                'Si, eliminar',
                'Cancelar'
            )) {
                Livewire.dispatch('delete', { id });
            }
        }
    </script>