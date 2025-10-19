@section('title', "Dashboard")

<main style="width: 100%;">
    <div class="loading" wire:loading.attr="show" show="false">
        <div class="loader"></div>
        <p class="loading-text">Cargando...</p>
    </div>

    <!-- MODAL GENERICO -->
    <div id="modal-home" class="modal" wire:ignore.self>
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ $record_id ? 'Editar registro' : 'Nuevo registro' }}</h5>
                    <button type="button" class="btn-close" aria-label="Cerrar"
                        onclick="closeModal(this.closest('.modal'))">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">Nombre</label>
                        <input wire:model="fields.name" type="text" placeholder="Nombre"
                            class="form-control @error('fields.name') was-validated is-invalid @enderror">
                        <div class="invalid-feedback">@error('fields.name') {{$message}} @enderror</div>
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

    <!-- CONTENIDO PRINCIPAL -->
    <div class="flex flex-col gap-4 mt-2">
        <!-- Encabezado mejorado -->
        <div class="flex justify-between items-center flex-wrap gap-3 mb-2">
            <div>
                <h3 class="text-2xl font-bold text-gray-800 mb-1">Panel de Control</h3>
                <p class="text-sm text-gray-500">Resumen general del sistema</p>
            </div>
            <div class="flex gap-2">
                <button wire:click="refrescarDatos" class="btn btn-outline-primary btn-sm px-3 py-2">
                    <i class="fas fa-sync-alt mr-1"></i> Actualizar
                </button>
                <button wire:click="exportarReporte" class="btn btn-success btn-sm px-3 py-2">
                    <i class="fas fa-download mr-1"></i> Exportar
                </button>
            </div>
        </div>

        <!-- Cards con estadísticas mejoradas -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Card Eventos -->
            <div class="bg-white border-l-4 border-blue-500 shadow-md rounded-lg p-4 hover:shadow-lg transition-shadow">
                <div class="flex justify-between items-start mb-3">
                    <div class="bg-blue-100 text-blue-600 rounded-full p-3">
                        <i class="fas fa-calendar-alt text-xl"></i>
                    </div>
                    <span class="text-xs bg-green-100 text-green-700 px-2 py-1 rounded-full font-semibold">
                        <i class="fas fa-arrow-up"></i> +12%
                    </span>
                </div>
                <h4 class="text-sm text-gray-500 mb-1">Total Eventos</h4>
                <h3 class="text-3xl font-bold text-gray-800 mb-2">{{ $total_eventos ?? 0 }}</h3>
                <div class="flex items-center text-xs text-gray-500 mt-2">
                    <i class="fas fa-circle text-green-500 mr-1" style="font-size: 6px;"></i>
                    <span>{{ $eventos_activos ?? 0 }} eventos activos</span>
                </div>
            </div>

            <!-- Card Usuarios -->
            <div class="bg-white border-l-4 border-green-500 shadow-md rounded-lg p-4 hover:shadow-lg transition-shadow">
                <div class="flex justify-between items-start mb-3">
                    <div class="bg-green-100 text-green-600 rounded-full p-3">
                        <i class="fas fa-users text-xl"></i>
                    </div>
                    <span class="text-xs bg-red-100 text-red-700 px-2 py-1 rounded-full font-semibold">
                        <i class="fas fa-arrow-down"></i> -3%
                    </span>
                </div>
                <h4 class="text-sm text-gray-500 mb-1">Total Usuarios</h4>
                <h3 class="text-3xl font-bold text-gray-800 mb-2">{{ $total_usuarios ?? 0 }}</h3>
                <div class="flex items-center text-xs text-gray-500 mt-2">
                    <i class="fas fa-circle text-blue-500 mr-1" style="font-size: 6px;"></i>
                    <span>{{ $usuarios_activos ?? 0 }} usuarios activos</span>
                </div>
            </div>

            <!-- Card Noticias -->
            <div class="bg-white border-l-4 border-indigo-500 shadow-md rounded-lg p-4 hover:shadow-lg transition-shadow">
                <div class="flex justify-between items-start mb-3">
                    <div class="bg-indigo-100 text-indigo-600 rounded-full p-3">
                        <i class="fas fa-newspaper text-xl"></i>
                    </div>
                    <span class="text-xs bg-green-100 text-green-700 px-2 py-1 rounded-full font-semibold">
                        <i class="fas fa-arrow-up"></i> +8%
                    </span>
                </div>
                <h4 class="text-sm text-gray-500 mb-1">Total Noticias</h4>
                <h3 class="text-3xl font-bold text-gray-800 mb-2">{{ $total_noticias ?? 0 }}</h3>
                <div class="flex items-center text-xs text-gray-500 mt-2">
                    <i class="fas fa-circle text-yellow-500 mr-1" style="font-size: 6px;"></i>
                    <span>{{ $noticias_publicadas ?? 0 }} publicadas</span>
                </div>
            </div>

            <!-- Card Documentos -->
            <div class="bg-white border-l-4 border-yellow-500 shadow-md rounded-lg p-4 hover:shadow-lg transition-shadow">
                <div class="flex justify-between items-start mb-3">
                    <div class="bg-yellow-100 text-yellow-600 rounded-full p-3">
                        <i class="fas fa-file-alt text-xl"></i>
                    </div>
                    <span class="text-xs bg-green-100 text-green-700 px-2 py-1 rounded-full font-semibold">
                        <i class="fas fa-arrow-up"></i> +15%
                    </span>
                </div>
                <h4 class="text-sm text-gray-500 mb-1">Total Documentos</h4>
                <h3 class="text-3xl font-bold text-gray-800 mb-2">{{ $total_documentos ?? 0 }}</h3>
                <div class="flex items-center text-xs text-gray-500 mt-2">
                    <i class="fas fa-circle text-indigo-500 mr-1" style="font-size: 6px;"></i>
                    <span>{{ $documentos_recientes ?? 0 }} este mes</span>
                </div>
            </div>
        </div>

        <!-- Gráficos y distribución -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            <!-- Gráfico de actividad mensual -->
            <div class="lg:col-span-2 bg-white border border-gray-200 shadow-md rounded-lg p-4">
                <div class="flex justify-between items-center mb-4">
                    <h4 class="text-lg font-semibold text-gray-800">Actividad del Sistema</h4>
                    <div class="flex gap-1">
                        <button wire:click="cambiarPeriodo('mes')" class="btn btn-sm {{ ($periodo ?? 'mes') === 'mes' ? 'btn-primary' : 'btn-outline-primary' }} px-2 py-1">Mes</button>
                        <button wire:click="cambiarPeriodo('trimestre')" class="btn btn-sm {{ ($periodo ?? '') === 'trimestre' ? 'btn-primary' : 'btn-outline-primary' }} px-2 py-1">Trim</button>
                        <button wire:click="cambiarPeriodo('año')" class="btn btn-sm {{ ($periodo ?? '') === 'año' ? 'btn-primary' : 'btn-outline-primary' }} px-2 py-1">Año</button>
                    </div>
                </div>
                <div class="flex items-end justify-between gap-1" style="height: 200px;">
                    @php
                        $meses = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct'];
                        $valores = [65, 78, 85, 72, 90, 95, 88, 92, 80, 87];
                    @endphp
                    @foreach($meses as $idx => $mes)
                        <div class="flex-1 flex flex-col items-center">
                            <div class="w-full bg-gradient-to-t from-blue-500 to-blue-300 rounded-t transition-all hover:from-blue-600 hover:to-blue-400" 
                                 style="height: {{ $valores[$idx] }}%; min-height: 20px;"></div>
                            <small class="text-gray-500 text-xs mt-2">{{ $mes }}</small>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Distribución circular -->
            <div class="bg-white border border-gray-200 shadow-md rounded-lg p-4">
                <h4 class="text-lg font-semibold text-gray-800 mb-4">Distribución</h4>
                <div class="flex items-center justify-center mb-4" style="height: 140px;">
                    <div style="width: 140px; height: 140px; border-radius: 50%; background: conic-gradient(
                        #3b82f6 0deg 130deg,
                        #10b981 130deg 230deg,
                        #6366f1 230deg 300deg,
                        #eab308 300deg 360deg
                    ); position: relative; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                        <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 90px; height: 90px; background: white; border-radius: 50%; display: flex; flex-direction: column; align-items: center; justify-content: center;">
                            <div class="text-2xl font-bold text-gray-800">{{ ($total_eventos ?? 0) + ($total_noticias ?? 0) + ($total_documentos ?? 0) + ($total_usuarios ?? 0) }}</div>
                            <small class="text-gray-500 text-xs">Total</small>
                        </div>
                    </div>
                </div>
                <div class="space-y-2">
                    <div class="flex items-center justify-between text-sm">
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 bg-blue-500 rounded-full"></span>
                            <span class="text-gray-600">Eventos</span>
                        </div>
                        <span class="font-semibold text-gray-800">{{ $total_eventos ?? 0 }}</span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 bg-green-500 rounded-full"></span>
                            <span class="text-gray-600">Usuarios</span>
                        </div>
                        <span class="font-semibold text-gray-800">{{ $total_usuarios ?? 0 }}</span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 bg-indigo-500 rounded-full"></span>
                            <span class="text-gray-600">Noticias</span>
                        </div>
                        <span class="font-semibold text-gray-800">{{ $total_noticias ?? 0 }}</span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 bg-yellow-500 rounded-full"></span>
                            <span class="text-gray-600">Documentos</span>
                        </div>
                        <span class="font-semibold text-gray-800">{{ $total_documentos ?? 0 }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actividad reciente y eventos próximos -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            <!-- Actividad reciente -->
            <div class="bg-white border border-gray-200 shadow-md rounded-lg p-4">
                <div class="flex justify-between items-center mb-4">
                    <h4 class="text-lg font-semibold text-gray-800">Actividad Reciente</h4>
                    <a href="#" class="text-blue-500 text-sm hover:underline">Ver todo</a>
                </div>
                <div class="space-y-3">
                    @php
                        $actividades = [
                            ['icon' => 'fa-calendar-plus', 'color' => 'blue', 'titulo' => 'Nuevo evento creado', 'desc' => 'Conferencia Anual 2025', 'tiempo' => '5 min'],
                            ['icon' => 'fa-user-plus', 'color' => 'green', 'titulo' => 'Usuario registrado', 'desc' => 'Juan Pérez Martínez', 'tiempo' => '15 min'],
                            ['icon' => 'fa-newspaper', 'color' => 'indigo', 'titulo' => 'Noticia publicada', 'desc' => 'Actualización del sistema', 'tiempo' => '1 hora'],
                            ['icon' => 'fa-file-upload', 'color' => 'yellow', 'titulo' => 'Documento subido', 'desc' => 'Informe mensual.pdf', 'tiempo' => '2 horas'],
                            ['icon' => 'fa-edit', 'color' => 'orange', 'titulo' => 'Evento actualizado', 'desc' => 'Taller de Innovación', 'tiempo' => '3 horas'],
                        ];
                    @endphp
                    @foreach($actividades as $act)
                        <div class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-50 transition-colors">
                            <div class="bg-{{ $act['color'] }}-100 text-{{ $act['color'] }}-600 rounded-full p-2 flex-shrink-0">
                                <i class="fas {{ $act['icon'] }}"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-gray-800">{{ $act['titulo'] }}</p>
                                <p class="text-xs text-gray-500 truncate">{{ $act['desc'] }}</p>
                            </div>
                            <span class="text-xs text-gray-400 flex-shrink-0">{{ $act['tiempo'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Eventos próximos -->
            <div class="bg-white border border-gray-200 shadow-md rounded-lg p-4">
                <div class="flex justify-between items-center mb-4">
                    <h4 class="text-lg font-semibold text-gray-800">Eventos Próximos</h4>
                    <a href="#" class="text-blue-500 text-sm hover:underline">Ver todos</a>
                </div>
                <div class="space-y-3">
                    @php
                        $eventos = [
                            ['titulo' => 'Conferencia Anual 2025', 'fecha' => '25 Oct', 'hora' => '09:00 AM', 'personas' => 150, 'color' => 'blue'],
                            ['titulo' => 'Taller de Innovación', 'fecha' => '28 Oct', 'hora' => '02:00 PM', 'personas' => 80, 'color' => 'green'],
                            ['titulo' => 'Seminario Virtual', 'fecha' => '01 Nov', 'hora' => '10:00 AM', 'personas' => 200, 'color' => 'purple'],
                            ['titulo' => 'Capacitación Técnica', 'fecha' => '05 Nov', 'hora' => '03:00 PM', 'personas' => 120, 'color' => 'orange'],
                        ];
                    @endphp
                    @foreach($eventos as $evt)
                        <div class="border-l-4 border-{{ $evt['color'] }}-500 bg-{{ $evt['color'] }}-50 p-3 rounded-r-lg hover:shadow-md transition-shadow">
                            <div class="flex justify-between items-start mb-2">
                                <h5 class="font-semibold text-gray-800 text-sm">{{ $evt['titulo'] }}</h5>
                                <span class="text-xs bg-{{ $evt['color'] }}-100 text-{{ $evt['color'] }}-700 px-2 py-1 rounded-full font-semibold">
                                    {{ $evt['personas'] }} <i class="fas fa-users"></i>
                                </span>
                            </div>
                            <div class="flex items-center gap-3 text-xs text-gray-600">
                                <span><i class="fas fa-calendar mr-1"></i>{{ $evt['fecha'] }}</span>
                                <span><i class="fas fa-clock mr-1"></i>{{ $evt['hora'] }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Accesos rápidos -->
        <div class="bg-white border border-gray-200 shadow-md rounded-lg p-4">
            <h4 class="text-lg font-semibold text-gray-800 mb-4">Accesos Rápidos</h4>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                <a href="#" wire:click.prevent="abrirModalEvento" class="flex flex-col items-center justify-center bg-blue-50 hover:bg-blue-100 border border-blue-200 rounded-lg p-4 transition-colors text-decoration-none">
                    <i class="fas fa-calendar-plus text-blue-500 text-2xl mb-2"></i>
                    <span class="text-sm font-semibold text-blue-700">Crear Evento</span>
                </a>
                <a href="#" wire:click.prevent="abrirModalUsuario" class="flex flex-col items-center justify-center bg-green-50 hover:bg-green-100 border border-green-200 rounded-lg p-4 transition-colors text-decoration-none">
                    <i class="fas fa-user-plus text-green-500 text-2xl mb-2"></i>
                    <span class="text-sm font-semibold text-green-700">Nuevo Usuario</span>
                </a>
                <a href="#" wire:click.prevent="abrirModalNoticia" class="flex flex-col items-center justify-center bg-indigo-50 hover:bg-indigo-100 border border-indigo-200 rounded-lg p-4 transition-colors text-decoration-none">
                    <i class="fas fa-newspaper text-indigo-500 text-2xl mb-2"></i>
                    <span class="text-sm font-semibold text-indigo-700">Publicar Noticia</span>
                </a>
                <a href="#" wire:click.prevent="abrirModalDocumento" class="flex flex-col items-center justify-center bg-yellow-50 hover:bg-yellow-100 border border-yellow-200 rounded-lg p-4 transition-colors text-decoration-none">
                    <i class="fas fa-file-upload text-yellow-500 text-2xl mb-2"></i>
                    <span class="text-sm font-semibold text-yellow-700">Subir Documento</span>
                </a>
            </div>
        </div>

        <!-- Tabla resumen mejorada -->
        <div class="bg-white border border-gray-200 shadow-md rounded-lg p-4">
            <div class="flex justify-between items-center mb-4">
                <h4 class="text-lg font-semibold text-gray-800">Resumen del Sistema</h4>
                <div class="flex gap-2">
                    <button wire:click="exportarExcel" class="btn btn-success btn-sm px-3 py-1">
                        <i class="fas fa-file-excel mr-1"></i> Excel
                    </button>
                    <button wire:click="exportarPDF" class="btn btn-danger btn-sm px-3 py-1">
                        <i class="fas fa-file-pdf mr-1"></i> PDF
                    </button>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="table min-w-full border border-gray-200 text-sm rounded-md">
                    <thead class="bg-gray-100 text-gray-600 uppercase text-xs font-semibold">
                        <tr>
                            <th class="px-4 py-3 text-left">Módulo</th>
                            <th class="px-4 py-3 text-left">Total</th>
                            <th class="px-4 py-3 text-left">Activos</th>
                            <th class="px-4 py-3 text-left">Última actualización</th>
                            <th class="px-4 py-3 text-left">Estado</th>
                            <th class="px-4 py-3 text-left">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-calendar-alt text-blue-500"></i>
                                    <span class="font-semibold">Eventos</span>
                                </div>
                            </td>
                            <td class="px-4 py-3 font-semibold">{{ $total_eventos ?? 0 }}</td>
                            <td class="px-4 py-3">{{ $eventos_activos ?? 0 }}</td>
                            <td class="px-4 py-3 text-xs text-gray-500">{{ now()->format('d/m/Y h:i A') }}</td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 bg-green-100 text-green-700 text-xs font-semibold rounded-full">
                                    <i class="fas fa-check-circle"></i> Activo
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <a href="#" class="text-blue-500 hover:underline text-xs">Ver módulo</a>
                            </td>
                        </tr>
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-newspaper text-indigo-500"></i>
                                    <span class="font-semibold">Noticias</span>
                                </div>
                            </td>
                            <td class="px-4 py-3 font-semibold">{{ $total_noticias ?? 0 }}</td>
                            <td class="px-4 py-3">{{ $noticias_publicadas ?? 0 }}</td>
                            <td class="px-4 py-3 text-xs text-gray-500">{{ now()->format('d/m/Y h:i A') }}</td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 bg-green-100 text-green-700 text-xs font-semibold rounded-full">
                                    <i class="fas fa-check-circle"></i> Activo
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <a href="#" class="text-blue-500 hover:underline text-xs">Ver módulo</a>
                            </td>
                        </tr>
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-users text-green-500"></i>
                                    <span class="font-semibold">Usuarios</span>
                                </div>
                            </td>
                            <td class="px-4 py-3 font-semibold">{{ $total_usuarios ?? 0 }}</td>
                            <td class="px-4 py-3">{{ $usuarios_activos ?? 0 }}</td>
                            <td class="px-4 py-3 text-xs text-gray-500">{{ now()->format('d/m/Y h:i A') }}</td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 bg-green-100 text-green-700 text-xs font-semibold rounded-full">
                                    <i class="fas fa-check-circle"></i> Activo
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <a href="#" class="text-blue-500 hover:underline text-xs">Ver módulo</a>
                            </td>
                        </tr>
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-file-alt text-yellow-500"></i>
                                    <span class="font-semibold">Documentos</span>
                                </div>
                            </td>
                            <td class="px-4 py-3 font-semibold">{{ $total_documentos ?? 0 }}</td>
                            <td class="px-4 py-3">{{ $documentos_activos ?? 0 }}</td>
                            <td class="px-4 py-3 text-xs text-gray-500">{{ now()->format('d/m/Y h:i A') }}</td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 bg-green-100 text-green-700 text-xs font-semibold rounded-full">
                                    <i class="fas fa-check-circle"></i> Activo
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <a href="#" class="text-blue-500 hover:underline text-xs">Ver módulo</a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<script>
    document.addEventListener('livewire:initialized', function() {
        Livewire.on('cerrar-modal', function(modal) {
            let modalElement = document.getElementById(modal[0].modal);
            if (modalElement) closeModal(modalElement);
        });

        Livewire.on('abrir-modal', function(modal) {
            let modalElement = document.getElementById(modal[0].modal);
            if (modalElement) openModal(modalElement);
        });
    });

    const confirmarEliminar = async id => {
        if (await window.Confirm(
                'Eliminar',
                '¿Estas seguro de eliminar este registro?',
                'warning',
                'Si, eliminar',
                'Cancelar'
            )) {
            Livewire.dispatch('delete', {
                id
            });
        }
    }
</script>