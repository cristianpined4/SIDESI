@section('title', 'Mis eventos')

<main>
    <div class="loading" wire:loading.attr="show" show="false" wire:target.except="search">
        <div class="loader"></div>
        <p class="loading-text">Cargando...</p>
    </div>

    <!-- Modal para Detalles del Evento -->
    <div id="event-modal" class="news-modal modal" wire:ignore.self style="display:none;">
        <div class="modal-content">
            <div class="modal-header">
                <img id="event-image" src="{{$records_event?->main_image ?? url('/images/sin-imagen.png')}}"
                    alt="Evento" class="modal-image">
                <button class="modal-close" wire:click="cerrarModal('event-modal', false)">×</button>
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
                @if(!empty($records_event?->description))
                <div class="modal-description prose max-w-none">
                    {!! $records_event->description !!}
                </div>
                @endif

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
                {{-- verificar si aun no ha pasado el evento --}}
                @if (now()->lessThanOrEqualTo(\Carbon\Carbon::parse($records_event?->end_time)))


                {{-- ¿El usuario está inscrito en el evento? --}}
                @if ($is_registered_evento)

                {{-- Si la inscripción está pendiente --}}
                @if ($pendiente)
                <button type="button"
                    class="btn bg-yellow-500 text-white px-4 py-2 rounded-md cursor-not-allowed opacity-75"
                    wire:click="cancelarInscripcion({{ $records_event?->id }})">
                    Inscripción pendiente (Cancelar)
                </button>
                @elseif($rechazado)
                <div class="flex justify-center items-center py-6">
                    <p class="text-xl font-semibold text-red-600 bg-red-100 px-6 py-3 rounded-lg shadow-sm">
                        Tu solicitud fue rechazada
                    </p>
                </div>
                @else
                <button type="button" class="btn bg-gray-400 text-white px-4 py-2 rounded-md hover:bg-gray-500"
                    wire:click="cancelarInscripcion({{ $records_event?->id }})">
                    Ya inscrito (Cancelar)
                </button>
                @endif

                @else
                @if ($records_event?->is_paid)
                {{-- Botón para inscribirse en evento pagado --}}
                <button wire:click="pagarEventoEInscribirConWompi({{ $records_event?->id }})"
                    class="flex items-center gap-2 bg-[#335DFF] hover:bg-[#264DDA] text-white font-semibold px-5 py-2.5 rounded-lg shadow-md transition-all duration-200 focus:outline-none focus:ring-4 focus:ring-blue-300">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-white" fill="currentColor"
                        viewBox="0 0 24 24">
                        <path
                            d="M12 1C6.48 1 2 3.58 2 6.5v5c0 4.22 4.03 8.27 9.55 11.25.29.15.63.15.92 0C17.97 19.77 22 15.72 22 11.5v-5C22 3.58 17.52 1 12 1zm0 19.47C8.03 18.01 4 14.36 4 11.5v-5C4 4.47 7.58 3 12 3s8 1.47 8 3.5v5c0 2.86-4.03 6.51-8 8.97z" />
                        <path
                            d="M10.9 14.32 8.28 11.7a1 1 0 0 1 1.42-1.4l1.8 1.79 3.8-3.79a1 1 0 1 1 1.4 1.42l-4.5 4.5a1 1 0 0 1-1.4 0z" />
                    </svg>
                    <span>Pagar con <strong>Wompi</strong> ${{ number_format($records_event?->price, 2, '.', ',')
                        }}</span>
                </button>
                @else
                {{-- Botón para inscribirse --}}
                <button type="button" class="btn bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-md"
                    wire:click="inscribir({{ $records_event?->id }})">
                    Inscribirse
                </button>
                @endif
                @endif
                @else
                <div class="flex justify-center items-center py-6">
                    <p class="text-xl font-semibold text-red-600 bg-red-100 px-6 py-3 rounded-lg shadow-sm">
                        Este evento ya ha finalizado
                    </p>
                </div>
                @endif
                @endauth

                <h2 class="modal-title mt-10">Sesiones</h2>

                <div class="container mx-auto px-4 py-12">
                    @if ($records_sesiones && $records_sesiones->count() > 0)
                    <div class="grid md:grid-cols-2 lg:grid-cols-2 gap-8">
                        @foreach($records_sesiones as $sesion)
                        {{-- Cards de sesiones --}}
                        <div class="bg-white rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-shadow duration-300 cursor-pointer group"
                            wire:click="verDetallesSesion({{$sesion->id}})">
                            <!-- Imagen -->
                            <img src="{{$sesion?->main_image ?? url('/images/sin-imagen.png')}}"
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

                                {{-- <div class="flex items-center justify-between text-sm text-gray-500 mt-2">
                                    <span class="flex items-center gap-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M21 10c0 7-9 13-9 13S3 17 3 10a9 9 0 1118 0z" />
                                            <circle cx="12" cy="10" r="3" />
                                        </svg>
                                        {{ $sesion->location ?: 'Por definir' }}
                                    </span>
                                </div> --}}
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
    <div id="sesion-modal" class="news-modal modal" wire:ignore.self style="display:none;">
        <div class="modal-content">
            <div class="modal-header">
                <img id="event-image" src="{{$records_sesion?->main_image ?? url('/images/sin-imagen.png')}}"
                    alt="sesion" class="modal-image">
                <button class="modal-close" wire:click="cerrarModal('sesion-modal', false)">×</button>
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
                @if(!empty($records_sesion?->description))
                <div class="modal-description prose max-w-none">
                    {!! $records_sesion->description !!}
                </div>
                @endif

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
                            @if(!empty($records_sesion?->ponente))
                            <tr>
                                <td class="py-2 align-top font-medium">Ponente:</td>
                                <td class="py-2">{{ $records_sesion->ponente->name }}</td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                @auth
                @if(now()->lessThanOrEqualTo(\Carbon\Carbon::parse($records_sesion?->end_time)))
                @if ($is_ponente)
                <div class="flex justify-center items-center py-6">
                    <p class="text-xl font-semibold text-blue-600 bg-blue-100 px-6 py-3 rounded-lg shadow-sm">
                        Eres el ponente de esta sesión
                    </p>
                </div>
                @elseif($rechazado)
                @else
                @if ($is_registered_evento && $pendiente === false)
                @if ($is_registered_sesion)
                <button type="button" class="btn bg-gray-400 text-white px-4 py-2 rounded-md hover:bg-gray-500"
                    wire:click="cancelarInscripcionSesion({{ $records_sesion?->id }})">
                    Ya inscrito (Cancelar)
                </button>
                @else
                <button type="button" class="btn bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-md"
                    wire:click="inscribirSesion({{ $records_sesion?->id }})">
                    Inscribirse
                </button>
                @endif
                @else
                <div class="flex justify-center items-center py-6">
                    <p class="text-xl font-semibold text-red-600 bg-red-100 px-6 py-3 rounded-lg shadow-sm">
                        Tienes que estar inscrito en el evento para inscribirte en esta sesión
                    </p>
                </div>
                @endif
                @endif
                @else
                <div class="flex justify-center items-center py-6">
                    <p class="text-xl font-semibold text-red-600 bg-red-100 px-6 py-3 rounded-lg shadow-sm">
                        Esta sesión ya ha finalizado
                    </p>
                </div>
                @endif
                @endauth
            </div>
        </div>
    </div>
    <!-- Fin Modal para Detalles de la sesion -->

    @if (session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            Alert('¡Éxito!', '{{ session('success') }}', 'success');
        });
    </script>
    @endif

    @if (session('error'))
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            Alert('Error', '{{ session('error') }}', 'error');
        });
    </script>
    @endif

    @if (session('evento_id_inscripto'))
    <div wire:init="sesiones({{ session('evento_id_inscripto') }})"></div>
    @endif

    <!-- fin modales -->

    <div class="w-full pb-8">
        <!-- Búsqueda y Filtros -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-8 relative z-10">
            <div class="flex flex-col lg:flex-row gap-4 items-center">
                <!-- Búsqueda -->
                <div class="flex-1 w-full">
                    <div class="relative">
                        <input type="text" wire:model.live.debounce.500ms="search"
                            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Buscar eventos...">
                        <div class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="inline-block w-4 h-4" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Pestañas -->
                <div class="flex w-full lg:w-auto">
                    <div class="inline-flex rounded-md shadow-sm" role="group">
                        <button type="button" wire:click="$set('tab', 'proximos')"
                            class="px-4 py-2 text-sm font-medium rounded-l-lg border border-gray-200 hover:bg-gray-50 focus:z-10 focus:ring-2 focus:ring-blue-500 focus:bg-blue-50 {{ $tab === 'proximos' ? 'bg-blue-50 text-blue-700 border-blue-300' : 'bg-white text-gray-700' }}">
                            Próximos Eventos
                        </button>
                        <button type="button" wire:click="$set('tab', 'pasados')"
                            class="px-4 py-2 text-sm font-medium rounded-r-lg border border-gray-200 hover:bg-gray-50 focus:z-10 focus:ring-2 focus:ring-blue-500 focus:bg-blue-50 {{ $tab === 'pasados' ? 'bg-blue-50 text-blue-700 border-blue-300' : 'bg-white text-gray-700' }}">
                            Eventos Pasados
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contenido principal -->
        <div class="container mx-auto px-4 py-12 relative">
            <div class="loading-search" wire:loading>
                <div class="loader"></div>
            </div>

            <!-- Título de Sección -->
            <div class="mb-8 text-center">
                <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-2">Mis Eventos</h1>
                <p class="text-gray-600 max-w-2xl mx-auto">
                    {{ $tab === 'proximos' ? 'Aquí puedes ver los eventos a los que estás inscrito y que están por
                    venir.' : 'Revisa el historial de eventos en los que has participado.' }}
                </p>
            </div>

            @if($eventos->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($eventos as $inscripcion)
                @php $evento = $inscripcion->evento; @endphp
                <div
                    class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300">
                    <!-- Imagen del evento -->
                    <div class="relative h-48 overflow-hidden">
                        @if($evento->imagenes->isNotEmpty())
                        <img src="{{ $evento->imagenes->first()->url }}"
                            alt="{{ $evento->imagenes->first()->alt_text }}"
                            class="w-full h-full object-cover transition-transform duration-500 hover:scale-105">
                        @else
                        <img src="{{ url('/images/sin-imagen.png') }}" alt="Imagen no disponible"
                            class="w-full h-full object-cover">
                        @endif
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>

                        <!-- Badge de estado de inscripción -->
                        <div class="absolute top-3 right-3">
                            @if($inscripcion->status === 'aprobado' || $inscripcion->status === 'registrado')
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7"></path>
                                </svg>
                                Inscrito
                            </span>
                            @elseif($inscripcion->status === 'pendiente')
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Pendiente
                            </span>
                            @else
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                Rechazado
                            </span>
                            @endif
                        </div>
                    </div>

                    <div class="p-6">
                        <div class="flex justify-between items-start">
                            <div>
                                @if($evento->start_time)
                                <div class="text-xs text-gray-500 uppercase font-semibold tracking-wider mb-1">
                                    {{ \Carbon\Carbon::parse($evento->start_time)->format('d M, Y') }}
                                </div>
                                @endif
                                <h3 class="text-xl font-bold text-gray-900 mb-2 line-clamp-2"
                                    title="{{ $evento->title }}">
                                    {{ $evento->title }}
                                </h3>
                            </div>
                        </div>

                        @if(!empty($evento->description))
                        <div class="text-gray-600 text-sm mb-4 line-clamp-2 prose prose-sm">
                            {!! Str::limit(strip_tags($evento->description), 100) !!}
                        </div>
                        @endif

                        <div class="flex items-center text-sm text-gray-500 mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span>{{ $evento->location ?? 'Ubicación no especificada' }}</span>
                        </div>

                        <div class="flex flex-wrap gap-2 mt-4">
                            <button wire:click="verDetalles({{ $evento->id }})"
                                class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200">
                                Ver Detalles
                            </button>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Paginación -->
            <div class="mt-8">
                {{ $eventos->links() }}
            </div>
            @else
            <div class="text-center py-12">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-400" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-1">
                    {{ $tab === 'proximos' ? 'No tienes eventos próximos' : 'No hay eventos pasados' }}
                </h3>
                <p class="text-gray-500 max-w-md mx-auto">
                    {{ $tab === 'proximos'
                    ? 'No estás inscrito en ningún evento próximo. Explora nuestros eventos disponibles para
                    inscribirte.'
                    : 'Aún no has participado en ningún evento o no ha finalizado ninguno de los eventos en los que
                    estás inscrito.' }}
                </p>
                @if($tab === 'proximos')
                <div class="mt-6">
                    <a href="{{ route('site.eventos') }}"
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="-ml-1 mr-2 h-5 w-5" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Ver todos los eventos
                    </a>
                </div>
                @endif
            </div>
            @endif
        </div>
    </div>
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

        document.addEventListener('click', function(event) {
            // Obtener todos los modales visibles
            const modals = document.querySelectorAll('.modal');
            
            // Encontrar el modal más superficial (último abierto)
            let topModal = null;
            modals.forEach(modal => {
                if (modal.style.display === 'flex' || modal.hasAttribute('show')) {
                    topModal = modal;
                }
            });
            
            // Si hay un modal abierto
            if (topModal) {
                const modalContent = topModal.querySelector('.modal-content, .modal-dialog');
                // Verificar si el click fue fuera del modal-content
                // Y que no sea en un botón de cerrar
                if (modalContent && 
                    !modalContent.contains(event.target) && 
                    !event.target.closest('.modal-close, .btn-close, .btn-secondary') &&
                    !event.target.closest('.swal2-container')
                ) {
                    closeModal(topModal);
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
            Livewire.dispatch('delete', {
                id
            });
        }
    }
</script>