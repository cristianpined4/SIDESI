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
    <div class="flex flex-col gap-4 mt-2" id="areaExportar">
        <!-- Encabezado mejorado -->
        <div class="flex justify-between items-center flex-wrap gap-3 mb-2">
            <div>
                <h3 class="text-2xl font-bold text-gray-800 mb-1">Panel de Control</h3>
                <p class="text-sm text-gray-500">Resumen general del sistema</p>
            </div>
            <div class="flex gap-2">
                <button wire:click="refresh" x-data="{
                        countdown: 120,
                        startCountdown() {
                            setInterval(() => {
                                if (this.countdown > 0) {
                                    this.countdown--;
                                } else {
                                    this.countdown = 120;  // Reinicia el contador
                                    $wire.refresh();       // Llama al método Livewire
                                }
                            }, 1000);
                        }
                    }" x-init="startCountdown()"
                    class="btn btn-outline-primary btn-sm px-3 py-2 flex items-center gap-1 flex-col">
                    <span class="flex items-center gap-1"><i class="fas fa-sync-alt mr-1"></i> Actualizar</span>
                    <span style="font-size: 11px;text-transform: capitalize;">(en <span x-text="countdown"></span>
                        segundos)</span>
                </button>
                <button wire:ignore id="btnExportarPDF"
                    class="btn btn-success btn-sm px-3 py-2 flex items-center gap-1">
                    <i class="fas fa-download mr-1"></i> Exportar
                </button>
            </div>
        </div>

        <!-- Cards con estadísticas mejoradas -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach ($totales as $item)
            <!-- Card {{ $item['name'] ?? '' }} -- Inicio -->
            <div class="bg-white border-l-4 shadow-md rounded-lg p-4 hover:shadow-lg transition-shadow"
                style="border-color:{{ $item['border'] }}">
                <div class="flex justify-between items-start mb-3">
                    <div class="bg-{{ $item['color'] }}-100 text-{{ $item['color'] }}-600 rounded-full p-3">
                        <i class="fas {{ $item['icon'] }} text-xl"></i>
                    </div>
                    <span
                        class="text-xs bg-{{ $item['color'] }}-100 text-{{ $item['color'] }}-700 px-2 py-1 rounded-full font-semibold">
                        <i class="fas fa-arrow-up"></i> {{ intval($item['count_active']) / max(intval($item['count']),
                        1) * 100 }}%
                    </span>
                </div>
                <h4 class="text-sm text-gray-500 mb-1">Total {{ $item['name'] ?? '' }}</h4>
                <h3 class="text-3xl font-bold text-gray-800 mb-2">{{ $item['count'] ?? 0 }}</h3>
                <div class="flex items-center text-xs text-gray-500 mt-2">
                    <i class="fas fa-circle text-green-500 mr-1" style="font-size: 6px;"></i>
                    <span>{{ $item['count_active'] ?? 0 }} {{ Str::lower($item['name'] ?? '') }} activos</span>
                </div>
            </div>
            <!-- Card {{ $item['name'] ?? '' }} -- Fin -->
            @endforeach
        </div>

        <!-- Gráficos y distribución -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            <!-- Gráfico de actividad mensual -->
            <div class="lg:col-span-2 bg-white border border-gray-200 shadow-md rounded-lg p-4">
                <div class="flex justify-between items-center mb-4">
                    <h4 class="text-lg font-semibold text-gray-800">Actividad del Sistema</h4>
                    <div class="flex gap-1">
                        <button wire:click="cambiarPeriodo('diario')"
                            class="btn btn-sm {{ $periodo === 'diario' ? 'btn-primary' : 'btn-outline-primary' }} px-2 py-1">Diario</button>
                        <button wire:click="cambiarPeriodo('mes')"
                            class="btn btn-sm {{ $periodo === 'mes' ? 'btn-primary' : 'btn-outline-primary' }} px-2 py-1">Mes</button>
                        <button wire:click="cambiarPeriodo('año')"
                            class="btn btn-sm {{ $periodo === 'año' ? 'btn-primary' : 'btn-outline-primary' }} px-2 py-1">Año</button>
                    </div>
                </div>
                <canvas id="graficoActividad" height="160"></canvas>
            </div>

            <!-- Distribución circular -->
            @php
            // Calcular total general de activos
            $total_general = collect($totales)->sum('count_active');

            // Calcular grados del gráfico circular (conic-gradient)
            $grados = 0;
            $segmentos = collect($totales)->map(function ($item) use (&$grados, $total_general) {
            $inicio = $grados;
            $fin = $grados + ($total_general > 0 ? ($item['count_active'] / $total_general) * 360 : 0);
            $grados = $fin;
            return [
            'color' => $item['border'],
            'inicio' => $inicio,
            'fin' => $fin,
            ];
            });
            @endphp

            <div class="bg-white border border-gray-200 shadow-md rounded-lg p-4">
                <h4 class="text-lg font-semibold text-gray-800 mb-4">Distribución</h4>

                <!-- GRÁFICO CIRCULAR -->
                <div class="flex items-center justify-center mb-4" style="height: 140px;">
                    @php
                    $background = $segmentos->map(fn($s) => "{$s['color']} {$s['inicio']}deg
                    {$s['fin']}deg")->implode(', ');
                    @endphp
                    <div
                        style="width: 140px; height: 140px; border-radius: 50%; background: conic-gradient({{ $background }}); position: relative; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                        <div
                            style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 90px; height: 90px; background: white; border-radius: 50%; display: flex; flex-direction: column; align-items: center; justify-content: center;">
                            <div class="text-2xl font-bold text-gray-800">{{ $total_general }}</div>
                            <small class="text-gray-500 text-xs">Total activos</small>
                        </div>
                    </div>
                </div>

                <!-- LEYENDA -->
                <div class="space-y-2">
                    @foreach ($totales as $item)
                    <div class="flex items-center justify-between text-sm">
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full" style="background-color: {{ $item['border'] }}"></span>
                            <span class="text-gray-600">{{ $item['name'] }}</span>
                        </div>
                        <span class="font-semibold text-gray-800">{{ $item['count_active'] }}</span>
                    </div>
                    @endforeach
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
                    @forelse($last10Logs as $act)
                    <div class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-50 transition-colors">
                        <div class="rounded-full p-2 flex-shrink-0"
                            style="background-color: {{ $act['bg-color'] }} !important; color: {{ $act['text-color'] }} !important;">
                            <i class="fas {{ $act['icon'] }}"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-gray-800">{{ $act['titulo'] }}</p>
                            <p class="text-xs text-gray-500 truncate">Hecho por: <b>{{ $act['user'] }}</b></p>
                        </div>
                        <span class="text-xs text-gray-400 flex-shrink-0">{{ $act['tiempo'] }}</span>
                    </div>
                    @empty
                    <p class="text-sm text-gray-500">No hay actividad reciente.</p>
                    @endforelse
                </div>
            </div>

            <!-- Eventos próximos -->
            <div class="bg-white border border-gray-200 shadow-md rounded-lg p-4">
                <div class="flex justify-between items-center mb-4">
                    <h4 class="text-lg font-semibold text-gray-800">Eventos Próximos</h4>
                    <a href="#" class="text-blue-500 text-sm hover:underline">Ver todos</a>
                </div>
                <div class="space-y-3">
                    @forelse($last10Eventos as $evt)
                    <div class="border-l-4 p-3 rounded-r-lg hover:shadow-md transition-shadow"
                        style="background-color: {{ $evt['bg-color'] }} !important; border-color: {{ $evt['text-color'] }} !important;">
                        <div class="flex justify-between items-start mb-2">
                            <h5 class="font-semibold text-gray-800 text-sm">{{ $evt['titulo'] }}</h5>
                            <span class="text-xs px-2 py-1 rounded-full font-semibold"
                                style="background-color: {{ $evt['bg-color'] }} !important; color: {{ $evt['text-color'] }} !important;">
                                {{ $evt['personas'] }} <i class="fas fa-users"></i>
                            </span>
                        </div>
                        <div class="flex items-center gap-3 text-xs text-gray-600">
                            <span><i class="fas fa-calendar mr-1"></i>{{ $evt['fecha'] }}</span>
                            <span><i class="fas fa-clock mr-1"></i>{{ $evt['hora'] }}</span>
                        </div>
                    </div>
                    @empty
                    <p class="text-sm text-gray-500">No hay eventos pr&oacute;ximos</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Accesos rápidos -->
        <div class="bg-white border border-gray-200 shadow-md rounded-lg p-4">
            <h4 class="text-lg font-semibold text-gray-800 mb-4">Accesos Rápidos</h4>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                <a href="{{ route('admin.eventos') }}"
                    class="flex flex-col items-center justify-center bg-blue-50 hover:bg-blue-100 border border-blue-200 rounded-lg p-4 transition-colors text-decoration-none">
                    <i class="fas fa-calendar-plus text-blue-500 text-2xl mb-2"></i>
                    <span class="text-sm font-semibold text-blue-700">Crear Evento</span>
                </a>
                <a href="{{ route('admin.usuarios') }}"
                    class="flex flex-col items-center justify-center bg-green-50 hover:bg-green-100 border border-green-200 rounded-lg p-4 transition-colors text-decoration-none">
                    <i class="fas fa-user-plus text-green-500 text-2xl mb-2"></i>
                    <span class="text-sm font-semibold text-green-700">Nuevo Usuario</span>
                </a>
                <a href="{{ route('admin.noticias') }}"
                    class="flex flex-col items-center justify-center bg-indigo-50 hover:bg-indigo-100 border border-indigo-200 rounded-lg p-4 transition-colors text-decoration-none">
                    <i class="fas fa-newspaper text-indigo-500 text-2xl mb-2"></i>
                    <span class="text-sm font-semibold text-indigo-700">Publicar Noticia</span>
                </a>
                <a href="{{ route('admin.documentos') }}"
                    class="flex flex-col items-center justify-center bg-yellow-50 hover:bg-yellow-100 border border-yellow-200 rounded-lg p-4 transition-colors text-decoration-none">
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
                    <button wire:click="resumenExportarExcel"
                        class="btn btn-success btn-sm px-3 py-1 flex items-center gap-1">
                        <i class="fas fa-file-excel mr-1"></i> Excel
                    </button>
                    <button wire:click="resumenExportarPDF"
                        class="btn btn-danger btn-sm px-3 py-1 flex items-center gap-1">
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
                        @forelse($totales as $item)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <div
                                        class="bg-{{ $item['color'] }}-100 text-{{ $item['color'] }}-600 rounded-full p-2">
                                        <i class="fas {{ $item['icon'] }} text-sm"></i>
                                    </div>
                                    <span class="font-semibold">{{ $item['name'] }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-3 font-semibold">{{ $item['count'] ?? 0 }}</td>
                            <td class="px-4 py-3">{{ $item['count_active'] ?? 0 }}</td>
                            <td class="px-4 py-3 text-xs text-gray-500">{{
                                \Carbon\Carbon::parse($item['last_updated_max'])->format('d/m/Y h:i A') ?? 'Desconocida'
                                }}
                            </td>
                            <td class="px-4 py-3">
                                @if (boolval($item['have_items_active']))
                                <span class="px-2 py-1 bg-green-100 text-green-700 text-xs font-semibold rounded-full">
                                    <i class="fas fa-check-circle"></i> Activo
                                </span>
                                @else
                                <span class="px-2 py-1 bg-red-100 text-red-700 text-xs font-semibold rounded-full">
                                    <i class="fas fa-times-circle"></i> Inactivo
                                </span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <a href="{{ route('admin.' . strtolower($item['name'])) }}"
                                    class="text-blue-500 hover:underline text-xs">
                                    Ver Módulo
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-4 py-3 text-center text-gray-500">
                                No hay registros disponibles.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<script>
    document.addEventListener('livewire:initialized', function() {
        const etiquetas = @json($actividadGrafico['meses']);
        const valores = Object.values(@json($actividadGrafico['valores']));
        window.chartJs("#graficoActividad", etiquetas, valores);

        Livewire.on('cerrar-modal', function(modal) {
            let modalElement = document.getElementById(modal[0].modal);
            if (modalElement) closeModal(modalElement);
        });

        Livewire.on('abrir-modal', function(modal) {
            let modalElement = document.getElementById(modal[0].modal);
            if (modalElement) openModal(modalElement);
        });

        Livewire.on('render-grafico', (data) => {
            const nuevasEtiquetas = data[0].meses;
            const nuevosDatos = Object.values(data[0].valores);
            /* limpiar grafico */
            let graficoActividad = document.getElementById('graficoActividad');
            if (graficoActividad) {
                let canvas = document.createElement('canvas');
                canvas.id = 'graficoActividad';
                canvas.height = 160;
                document.querySelector('#graficoActividad').parentNode.appendChild(canvas);
                graficoActividad.remove();
            }
            window.chartJs("#graficoActividad", nuevasEtiquetas, nuevosDatos);
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

    function formatDateForPDF(date) {
        const pad = (n) => n.toString().padStart(2, '0');
        
        const year = date.getFullYear();
        const month = pad(date.getMonth() + 1); // Los meses van de 0 a 11
        const day = pad(date.getDate());
        
        let hours = date.getHours();
        const minutes = pad(date.getMinutes());
        const ampm = hours >= 12 ? 'PM' : 'AM';
        hours = hours % 12;
        hours = hours ? hours : 12; // 0 -> 12
        hours = pad(hours);

        return `reporte_${year}-${month}-${day}_${hours}-${minutes}_${ampm}.pdf`;
    }

    document.getElementById('btnExportarPDF').addEventListener('click', async () => {
        const elemento = document.querySelector('#areaExportar'); // 👈 El contenedor que quieres imprimir
        if (!elemento) {
            window.alert('Error: No se encontró el área a exportar.');
            return;
        }

        // Mostrar mensaje temporal
        const btn = document.getElementById('btnExportarPDF');
        const originalText = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generando...';
        btn.disabled = true;

        try {
            // Captura con html2canvas
            // Captura con html2canvas
            const canvas = await html2canvas(elemento, {
                scale: 1.5, // ajusta según necesidad
                backgroundColor: '#ffffff',
                logging: false,
                useCORS: true
            });
            const imgData = canvas.toDataURL('image/png');

            // Crear PDF
            const pdf = new jsPDF('p', 'mm', 'a4');
            const pageWidth = pdf.internal.pageSize.getWidth();
            const pageHeight = pdf.internal.pageSize.getHeight();
            const padding = 10; // mm

            // Escalar la imagen para que quepa en una sola página
            const imgRatio = canvas.width / canvas.height;
            let imgWidth = pageWidth - padding * 2;
            let imgHeight = imgWidth / imgRatio;

            // Si la altura excede la página, ajustamos según altura
            if (imgHeight > pageHeight - padding * 2) {
                imgHeight = pageHeight - padding * 2;
                imgWidth = imgHeight * imgRatio;
            }

            const x = (pageWidth - imgWidth) / 2; // centrar horizontalmente
            const y = (pageHeight - imgHeight) / 2; // centrar verticalmente

            pdf.addImage(imgData, 'PNG', x, y, imgWidth, imgHeight);
            pdf.save(formatDateForPDF(new Date()));
            await Livewire.dispatch('pdfResult', {
                data : {
                    success: true,
                    message: 'PDF generado correctamente.'
                }
            });
        } catch (err) {
            console.error(err);
            await Livewire.dispatch('pdfResult', {
                data : {
                    success: false,
                    message: 'Error al generar el PDF: '+err.message
                }
            });
            window.Alert('Error', 'Error al generar el PDF', 'error');
        } finally {
            // Restaurar botón
            btn.innerHTML = originalText;
            btn.disabled = false;
        }
    });
</script>