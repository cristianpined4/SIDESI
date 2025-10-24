@section('title', "Eventos")



<main>
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
                    <button type="button" class="btn-close" aria-label="Cerrar"
                        onclick="closeModal(this.closest('.modal'))">&times;</button>
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
                    <button type="button" class="btn btn-secondary"
                        onclick="closeModal(this.closest('.modal'))">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Detalles del Evento -->
    <div id="event-modal" class="news-modal modal" wire:ignore.self>
        <div class="modal-content">
            <div class="modal-header">
                <img id="event-image"
                    src="https://images.unsplash.com/photo-1540575467063-178a50c2df87?w=800&h=500&fit=crop" alt="Evento"
                    class="modal-image">
                <button class="modal-close" onclick="closeModal(this.closest('.modal'))">×</button>
            </div>
            <div class="modal-body">
                <div class="modal-meta">
                    <span
                        class="inline-block text-xs font-semibold px-3 py-1 rounded-full 
                        {{ $records_event?->mode === 'virtual' ? 'bg-blue-100 text-blue-600' : ($records_event?->mode === 'presencial' ? 'bg-green-100 text-green-600' : 'bg-purple-100 text-purple-600') }}">
                        {{ ucfirst($records_event?->mode ?? 'Desconocido') }}
                    </span>
                    <span class="text-gray-500 text-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="inline-block w-4 h-4 mr-1" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10m-11 6h12a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        {{ \Carbon\Carbon::parse($records_event?->start_time)->format('d/m/Y h:i A') }}
                    </span>
                </div>

                <h2 class="modal-title">{{ $records_event?->title }}</h2>
                <p id="event-description" class="modal-description"></p>

                <div class="modal-details">
                    <table class="w-full text-sm leading-relaxed">
                        <tbody>
                            <tr>
                                <td class="py-2 align-top font-medium">Ubicación:</td>
                                <td class="py-2">{{ $records_event?->location }}</td>
                            </tr>

                            <tr>
                                <td class="py-2 align-top font-medium">Inicio:</td>
                                <td class="py-2">
                                    {{ \Carbon\Carbon::parse($records_event?->start_time)->format('d/m/Y h:i A') }}
                                </td>
                            </tr>

                            <tr>
                                <td class="py-2 align-top font-medium">Fin:</td>
                                <td class="py-2">
                                    {{ \Carbon\Carbon::parse($records_event?->end_time)->format('d/m/Y h:i A') }}
                                </td>
                            </tr>

                            <tr>
                                <td class="py-2 align-top font-medium">Email:</td>
                                <td class="py-2">{{ $records_event?->contact_email }}</td>
                            </tr>
                            <tr>
                                <td class="py-2 align-top font-medium">Teléfono:</td>
                                @php
                                $rawPhone = preg_replace('/\D/', '', $records_event?->contact_phone ?? '');
                                // Si ya empieza con 503, lo dejamos; si no, lo agregamos
                                if (!str_starts_with($rawPhone, '503')) {
                                $rawPhone = '503' . $rawPhone;
                                }
                                // Quitamos el código país para darle formato al resto
                                $number = substr($rawPhone, 3);
                                // Aseguramos que tenga 8 dígitos
                                $number = str_pad(substr($number, 0, 8), 8, '0');
                                // Aplicamos formato xxxx-xxxx
                                $formatted = '+503 ' . substr($number, 0, 4) . '-' . substr($number, 4, 4);
                                @endphp
                                <td class="py-2">{{ $formatted }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                @auth
                    {{-- Si el usuario es organizador --}}
                    @if ($is_organizer)
                        <div class="flex justify-center items-center py-6">
                            <p class="text-xl font-semibold text-green-600 bg-green-100 px-6 py-3 rounded-lg shadow-sm">
                                Eres el organizador de este evento
                            </p>
                        </div>
                    @else
                        {{-- Si el evento está activo y permite inscripciones --}}
                        @if ($records_event?->is_active && $records_event?->inscriptions_enabled)
                            
                            {{-- ¿El usuario está inscrito en el evento? --}}
                            @if ($is_registered_evento)

                                {{-- Si la inscripción está pendiente --}}
                                @if ($pendiente)
                                    <button 
                                        type="button"
                                        class="btn bg-yellow-500 text-white px-4 py-2 rounded-md cursor-not-allowed opacity-75"
                                        wire:click="cancelarInscripcion({{ $records_event?->id }})"
                                    >
                                        Inscripción pendiente (Cancelar)
                                    </button>
                                @else
                                    <button 
                                        type="button"
                                        class="btn bg-gray-400 text-white px-4 py-2 rounded-md hover:bg-gray-500"
                                        wire:click="cancelarInscripcion({{ $records_event?->id }})"
                                    >
                                        Ya inscrito (Cancelar)
                                    </button>
                                @endif

                            @else
                                {{-- Botón para inscribirse --}}
                                <button 
                                    type="button"
                                    class="btn bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-md"
                                    wire:click="inscribir({{ $records_event?->id }})"
                                >
                                    Inscribirse
                                </button>
                            @endif

                        @endif
                    @endif
                @endauth

                <h2 class="modal-title mt-10">Sesiones</h2>

                <div class="container mx-auto px-4 py-12">
                    @if ($records_sesiones && $records_sesiones->count() > 0)
                    <div class="grid md:grid-cols-2 lg:grid-cols-2 gap-8">
                        @foreach($records_sesiones as $sesion)
                        {{-- Cards de sesiones --}}
                        <div class="bg-white rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-shadow duration-300 cursor-pointer group"
                            wire:click="sesion({{$sesion->id}})">
                            <!-- Imagen -->
                            <img src="https://images.unsplash.com/photo-1552664730-d307ca884978?w=800&h=500&fit=crop"
                                alt="{{ $sesion->title }}"
                                class="w-full h-48 object-cover transition-transform duration-300 group-hover:scale-105">

                            <!-- Contenido -->
                            <div class="p-6 flex flex-col gap-3">
                                <div class="flex justify-between items-center">
                                    <span class="
                                                px-2 py-1 rounded text-sm font-medium
                                                {{ $sesion->mode === 'taller' ? 'bg-blue-100 text-blue-600' : 
                                                ($sesion->mode === 'ponencia' ? 'bg-green-100 text-green-600' : 
                                                ($sesion->mode === 'panel' ? 'bg-purple-100 text-purple-600' : 
                                                ($sesion->mode === 'otro' ? 'bg-yellow-100 text-yellow-600' : 
                                                'bg-gray-100 text-gray-600'))) }}
                                            ">
                                        {{ ucfirst($sesion->mode ?? 'Desconocido') }}
                                    </span>
                                    <span class="text-gray-500 text-sm">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="inline-block w-4 h-4 mr-1"
                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 7V3m8 4V3m-9 8h10m-11 6h12a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                        </svg>
                                        {{ \Carbon\Carbon::parse($sesion->start_time)->format('d/m/Y h:i A') }}
                                    </span>
                                </div>

                                <h3
                                    class="text-lg font-semibold text-gray-800 group-hover:text-blue-600 transition-colors">
                                    {{ $sesion->title }}
                                </h3>
                                <p class="text-gray-600 text-sm line-clamp-2">
                                    {{ $sesion->description }}
                                </p>

                                <div class="flex items-center justify-between text-sm text-gray-500 mt-2">
                                    <span class="flex items-center gap-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M21 10c0 7-9 13-9 13S3 17 3 10a9 9 0 1118 0z" />
                                            <circle cx="12" cy="10" r="3" />
                                        </svg>
                                        {{ $sesion->location ?: 'Por definir' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        {{-- Fin cards de sesiones --}}
                        @endforeach
                    </div>
                    @else
                    <!-- Si no hay sesiones -->
                    <div class="text-center py-16">
                        <h3 class="text-xl font-semibold mb-2">No hay sesiones disponibles</h3>
                        <p class="text-gray-500">Vuelve más tarde.</p>
                    </div>
                    @endif
                </div>

            </div>
        </div>
    </div>
    <!-- Fin Modal para Detalles del Evento -->

    <!-- Modal para Detalles de la Sesion -->
    <div id="sesion-modal" class="news-modal modal" wire:ignore.self>
        <div class="modal-content">
            <div class="modal-header">
                <img id="event-image"
                    src="https://images.unsplash.com/photo-1540575467063-178a50c2df87?w=800&h=500&fit=crop" alt="sesion"
                    class="modal-image">
                <button class="modal-close" onclick="closeModal(this.closest('.modal'))">×</button>
            </div>
            <div class="modal-body">
                <div class="modal-meta">
                    <span class="
                        px-2 py-1 rounded text-sm font-medium
                        {{ $records_sesion?->mode === 'taller' ? 'bg-blue-100 text-blue-600' : 
                        ($records_sesion?->mode === 'ponencia' ? 'bg-green-100 text-green-600' : 
                        ($records_sesion?->mode === 'panel' ? 'bg-purple-100 text-purple-600' : 
                        ($records_sesion?->mode === 'otro' ? 'bg-yellow-100 text-yellow-600' : 
                        'bg-gray-100 text-gray-600'))) }}
                    ">
                        {{ ucfirst($records_sesion->mode ?? 'Desconocido') }}
                    </span>
                    <span class="text-gray-500 text-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="inline-block w-4 h-4 mr-1" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10m-11 6h12a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        {{ \Carbon\Carbon::parse($records_sesion?->start_time)->format('d/m/Y h:i A') }}
                    </span>
                </div>

                <h2 class="modal-title">{{ $records_sesion?->title }}</h2>
                <p id="event-description" class="modal-description"></p>

                <div class="modal-details">
                    <table class="w-full text-sm leading-relaxed">
                        <tbody>
                            <tr>
                                <td class="py-2 align-top font-medium">Ubicación:</td>
                                <td class="py-2">{{ $records_sesion?->location }}</td>
                            </tr>
                            <tr>
                                <td class="py-2 align-top font-medium">Inicio:</td>
                                <td class="py-2">
                                    {{ \Carbon\Carbon::parse($records_sesion?->start_time)->format('d/m/Y h:i A') }}
                                </td>
                            </tr>
                            <tr>
                                <td class="py-2 align-top font-medium">Fin:</td>
                                <td class="py-2">
                                    {{ \Carbon\Carbon::parse($records_sesion?->end_time)->format('d/m/Y h:i A') }}
                                </td>
                            </tr>
                            <tr>
                                <td class="py-2 align-top font-medium">Ponente:</td>
                                <td class="py-2">{{ $records_ponente?->name }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                @auth
                    {{-- Si el usuario es ponente --}}
                    @if ($is_ponente)
                        <div class="flex justify-center items-center py-6">
                            <p class="text-xl font-semibold text-blue-600 bg-blue-100 px-6 py-3 rounded-lg shadow-sm">
                                Eres el ponente de esta sesión
                            </p>
                        </div>
                    @else
                        {{-- Filtro para saber si el usuario está inscrito en el evento --}}
                        @if ($is_registered_evento && $pendiente === false)
                            {{-- ¿El usuario está inscrito en la sesión? --}}
                            @if ($is_registered_sesion)
                                <button 
                                    type="button"
                                    class="btn bg-gray-400 text-white px-4 py-2 rounded-md hover:bg-gray-500"
                                    wire:click="cancelarInscripcionSesion({{ $records_sesion?->id }})"
                                >
                                    Ya inscrito (Cancelar)
                                </button>
                            @else
                                {{-- Botón para inscribirse a la sesión --}}
                                <button 
                                    type="button"
                                    class="btn bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-md"
                                    wire:click="inscribirSesion({{ $records_sesion?->id }})"
                                >
                                    Inscribirse
                                </button>
                            @endif
                        @endif
                    @endif
                @endauth
            </div>
        </div>
    </div>
    <!-- Fin Modal para Detalles de la sesion -->

    <!-- fin modales -->

    <!-- Contenido - inicio -->
    <!-- Hero Section -->
    <div class="bg-gradient-to-br from-gray-100 via-gray-50 to-white py-20 px-4">
        <div class="max-w-7xl mx-auto text-center">
            <h1 class="text-5xl md:text-6xl font-bold text-balance mb-4">
                Eventos y <span class="text-blue-500">Actividades</span>
            </h1>
            <p class="text-gray-600 text-lg md:text-xl max-w-3xl mx-auto leading-relaxed">
                Participa en congresos, talleres, seminarios y actividades diseñadas para impulsar tu desarrollo
                académico y profesional.
            </p>
        </div>
    </div>

    <div class="w-full pb-8">
        <!-- Búsqueda y Filtros -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-8 relative z-10">
            <div class="flex flex-col lg:flex-row gap-4 items-center">
                <!-- Búsqueda -->
                <div class="relative w-full lg:w-72">
                    <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400 pointer-events-none"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <input type="text" wire:model.live="search" placeholder="Buscar eventos..."
                        class="w-full pl-12 pr-4 py-3 border border-gray-300 rounded-lg text-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <!-- Filtro Tipo de Evento -->
              

                <!-- Filtro Modalidad -->
                <div class="w-full lg:w-auto">
                    <div class="relative">
                        <select wire:model.live="modalidad"
                            class="w-full lg:w-48 pl-4 pr-10 py-3 border border-gray-300 rounded-lg bg-white text-gray-700 appearance-none focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Modalidad</option>
                            <option value="presencial">Presencial</option>
                            <option value="virtual">Virtual</option>
                            <option value="hibrido">Híbrido</option>
                        </select>
                        <svg class="absolute right-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400 pointer-events-none"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7">
                            </path>
                        </svg>
                    </div>
                </div>

                <!-- Contador -->
                <div class="w-full lg:w-auto text-center lg:text-right ml-auto">
                    <p class="text-sm text-gray-700 leading-5 dark:text-gray-400">
                        <span>Mostrando</span>
                        <span class="font-medium">{{ $records->firstItem() ?? 0 }}</span>
                        <span>de</span>
                        <span class="font-medium">{{ $records->lastItem() ?? 0 }}</span>
                        <span>de</span>
                        <span class="font-medium">{{ $records->total() ?? 0 }}</span>
                        <span>resultados</span>
                    </p>
                </div>
            </div>
        </div>

        <!-- Pestañas de Eventos -->
        <div class="flex justify-center mb-10">
            <div class="inline-flex shadow-md rounded-[10px]">
                <button wire:click="$set('tab', 'proximos')"
                    class="px-12 py-2 text-sm font-medium text-blue-600 bg-blue-100 hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 rounded-l-[10px]">
                    Próximos Eventos
                </button>
                <button wire:click="$set('tab', 'pasados')"
                    class="px-12 py-2 text-sm font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 rounded-r-[10px]">
                    Eventos Pasados
                </button>
            </div>
        </div>
        <!-- Grid de Cards -->
        <div class="container mx-auto px-4 py-12">
            <!-- Título de Sección -->
            <div class="mb-8">
                <h2 class="text-3xl font-bold text-gray-800 ">Todos los Eventos</h2>
            </div>
            @if(count($records) > 0)
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($records as $event)
                <div class="bg-white rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-shadow duration-300 cursor-pointer group"
                    wire:click="sesiones({{ $event->id }})">

                    <!-- Imagen -->
                    <img src="https://images.unsplash.com/photo-1552664730-d307ca884978?w=800&h=500&fit=crop"
                        alt="{{ $event->title }}"
                        class="w-full h-48 object-cover transition-transform duration-300 group-hover:scale-105">

                    <!-- Contenido -->
                    <div class="p-6 flex flex-col gap-3">
                        <div class="flex justify-between items-center">
                            <span
                                class="inline-block text-xs font-semibold px-3 py-1 rounded-full 
                                {{ $event->mode === 'virtual' ? 'bg-blue-100 text-blue-600' : ($event->mode === 'presencial' ? 'bg-green-100 text-green-600' : 'bg-purple-100 text-purple-600') }}">
                                {{ ucfirst($event->mode ?? 'Desconocido') }}
                            </span>
                            <span class="text-gray-500 text-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" class="inline-block w-4 h-4 mr-1" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10m-11 6h12a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                                {{ \Carbon\Carbon::parse($event->start_time)->format('d/m/Y h:i A') }}
                            </span>
                        </div>

                        <h3 class="text-lg font-semibold text-gray-800 group-hover:text-blue-600 transition-colors">
                            {{ $event->title }}
                        </h3>
                        <p class="text-gray-600 text-sm line-clamp-2">
                            {{ $event->description }}
                        </p>

                        <div class="flex items-center justify-between text-sm text-gray-500 mt-2">
                            <span class="flex items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M21 10c0 7-9 13-9 13S3 17 3 10a9 9 0 1118 0z" />
                                    <circle cx="12" cy="10" r="3" />
                                </svg>
                                {{ $event->location ?: 'Por definir' }}
                            </span>
                            @if($event->is_paid && doubleval($event->price) > 0)
                            <span class="text-blue-600 font-medium">${{ number_format($event->price, 2) }}</span>
                            @else
                            <span class="text-green-600 font-medium">Gratis</span>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Paginación -->
            <div class="mt-10">
                {{ $records->links() }}
            </div>

            @else
            <!-- Si no hay eventos -->
            <div class="text-center py-16">
                <h3 class="text-xl font-semibold mb-2">No hay eventos disponibles</h3>
                <p class="text-gray-500">Intenta cambiar los filtros o vuelve más tarde.</p>
            </div>
            @endif
        </div>
    </div>
    <!-- Contenido - fin -->
</main>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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

        Livewire.on('confirmar-cancelacion', ({
            idEvento, idSesion, title, text, metodoCancelacion, metodo
        }) => {
            Swal.fire({
                title: title,
                text: text,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, cancelar',
                cancelButtonText: 'No, mantener',
                customClass: {
                    container: 'swal2-container z-[9999]'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    if (idEvento) {
                        Livewire.dispatch(metodoCancelacion, { idEvento });
                    } else if (idSesion) {
                        Livewire.dispatch(metodoCancelacion, { idSesion });
                    }
                } else {
                    if (idEvento !== null) {
                        @this.call(metodo, idEvento);
                    } else if (idSesion !== null) {
                        @this.call(metodo, idSesion);  
                    }
                }
            });
        });

        Livewire.on('confirmar-inscripcion', ({
            idEvento, idSesion, title, text, metodo
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
                        Livewire.dispatch(metodo, { idEvento });
                    } else if (idSesion !== null) {
                        Livewire.dispatch(metodo, { idSesion });
                    }
                }
            });
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
</script>