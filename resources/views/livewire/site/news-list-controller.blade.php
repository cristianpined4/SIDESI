@section('title', 'Noticias y Convocatorias')
@section('meta_description', 'Mantente informado sobre los últimos eventos, oportunidades académicas y noticias
relevantes para la comunidad estudiantil de ingeniería.')

<div>
    <!-- Hero Section -->
    <section class="relative overflow-hidden bg-gradient-to-br from-blue-50 via-white to-blue-100">
        <div class="container mx-auto px-4 py-24 lg:py-32">
            <div class="text-center space-y-8">
                <div class="space-y-4">
                    <h1 class="text-4xl lg:text-6xl font-bold text-balance leading-tight">Noticias y
                        <span class="text-blue-600">Convocatorias</span>
                    </h1>
                    <p class="text-xl text-gray-600 text-pretty max-w-2xl mx-auto leading-relaxed">
                        Mantente informado sobre los últimos eventos, oportunidades académicas y noticias relevantes
                        para la comunidad estudiantil de ingeniería.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- News Grid Section -->
    <section class="bg-gray-50 relative">
        <!-- Filtros Fijos - Debajo de la barra de navegación -->
        <div class="sticky top-16 z-40 bg-white border-b border-gray-200 py-4 shadow-sm">
            <div class="container mx-auto px-4">
                <div class="flex flex-col sm:flex-row gap-4 items-center justify-center w-4/5 max-w-7xl mx-auto">
                    <div class="relative flex-1">
                        <div class="relative">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round"
                                class="lucide lucide-search absolute left-3 top-1/2 transform -translate-y-1/2 h-4 w-4 text-gray-400">
                                <circle cx="11" cy="11" r="8"></circle>
                                <path d="m21 21-4.3-4.3"></path>
                            </svg>
                            <input type="text" wire:model.live="currentSearchTerm" placeholder="Buscar noticias..."
                                class="w-full pl-10 pr-4 py-3 rounded-lg border border-gray-300 bg-white text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>

                    <div class="relative">
                        <select wire:model.live="currentCategory"
                            class="appearance-none bg-white border border-gray-300 rounded-lg px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 pr-8">
                            <option value="all">Todas las categorías</option>
                            <option value="evento">Eventos</option>
                            <option value="empleo">Empleo</option>
                            <option value="taller">Talleres</option>
                            <option value="egresados">Egresados</option>
                        </select>
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                            class="lucide lucide-chevron-down absolute right-2 top-1/2 transform -translate-y-1/2 h-4 w-4 pointer-events-none">
                            <path d="m6 9 6 6 6-6"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <div class="container mx-auto px-4 py-12">
            <div class="flex items-center justify-between mb-12 w-4/5 max-w-7xl mx-auto">
                <div>
                    <h2 class="text-3xl lg:text-4xl font-bold mb-4">
                        <span class="text-blue-600">Noticias</span> Destacadas
                    </h2>
                    <p class="text-xl text-gray-600">
                        Explora las últimas convocatorias, eventos y oportunidades académicas.
                    </p>
                </div>
                <div class="text-sm text-gray-500 hidden sm:block">
                    <p class="text-sm text-gray-700 leading-5 dark:text-gray-400">
                        <span>Mostrando</span>
                        <span class="font-medium">{{ $filteredNews->firstItem() ?? 0 }}</span>
                        <span>de</span>
                        <span class="font-medium">{{ $filteredNews->lastItem() ?? 0 }}</span>
                        <span>de</span>
                        <span class="font-medium">{{ $filteredNews->total() ?? 0 }}</span>
                        <span>resultados</span>
                    </p>
                </div>
            </div>

            @if(count($filteredNews) > 0)
            <!-- Grid de 3 columnas centrado y más ancho -->
            <div class="grid lg:grid-cols-3 gap-8 w-4/5 max-w-7xl mx-auto">
                @foreach($filteredNews as $news)
                <div class="bg-white rounded-xl overflow-hidden shadow-sm news-card-hover cursor-pointer group"
                    wire:key="news-{{ $news['id'] }}" onclick="openNewsModal(@js($news))">
                    <!-- Imagen agregada como en el original -->
                    <img src="{{ $news['image'] }}" alt="{{ $news['title'] }}"
                        class="w-full h-48 object-cover transition-transform duration-400 group-hover:scale-105">

                    <div class="p-6 flex flex-col gap-4">
                        <div class="flex items-center justify-between">
                            <span
                                class="inline-flex items-center justify-center rounded-md px-3 py-1 text-xs font-medium badge-{{ $news['category'] }}">
                                {{ $news['category_label'] }}
                            </span>
                            <div class="flex items-center text-sm text-gray-500">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="lucide lucide-calendar h-4 w-4 mr-1">
                                    <path d="M8 2v4"></path>
                                    <path d="M16 2v4"></path>
                                    <rect width="18" height="18" x="3" y="4" rx="2"></rect>
                                    <path d="M3 10h18"></path>
                                </svg>
                                {{ $news['date'] }}
                            </div>
                        </div>

                        <h3 class="font-semibold text-xl text-balance group-hover:text-blue-600 transition-colors">
                            {{ $news['title'] }}
                        </h3>

                        <p class="text-gray-600 text-base leading-relaxed">
                            {{ $news['description'] }}
                        </p>

                        <div
                            class="flex items-center text-sm text-blue-600 font-medium group-hover:gap-2 transition-all mt-2">
                            Leer más
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="lucide lucide-arrow-right h-4 w-4 ml-1">
                                <path d="M5 12h14"></path>
                                <path d="m12 5 7 7-7 7"></path>
                            </svg>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="text-center py-16 w-4/5 max-w-7xl mx-auto">
                <div class="h-16 w-16 mx-auto mb-4 rounded-full bg-gray-100 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="lucide lucide-search h-8 w-8 text-gray-400">
                        <circle cx="11" cy="11" r="8"></circle>
                        <path d="m21 21-4.3-4.3"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold mb-2">No se encontraron noticias</h3>
                <p class="text-gray-500">Intenta con otros términos de búsqueda o categorías.</p>
            </div>
            @endif

            @if (count($filteredNews) > 0)
            <div class="mt-12 mx-auto w-4/5 max-w-7xl">
                <!-- Paginación centrada -->
                {{ $filteredNews->links() }}
            </div>
            @endif
        </div>
    </section>

    <!-- Modal para detalles de noticias - CLASE MODIFICADA -->
    <div id="news-modal" class="news-modal" wire:ignore.self>
        <div class="modal-content">
            <div class="modal-header">
                <img id="modal-image" src="" alt="Evento" class="modal-image">
                <button class="modal-close" id="modal-close">×</button>
            </div>
            <div class="modal-body">
                <div class="modal-meta">
                    <span id="modal-badge" class="modal-badge">Evento</span>
                    <span id="modal-date" class="modal-date">📅 10 de Marzo, 2024</span>
                </div>
                <h2 id="modal-title" class="modal-title">Hackathon de Innovación Tecnológica 2024</h2>
                <p id="modal-description" class="modal-description">Participa en el hackathon más grande del año y
                    desarrolla soluciones innovadoras para problemas reales.</p>

                <div class="modal-details">
                    <div class="detail-item">
                        <span class="detail-icon"></span>
                        <span class="detail-label">Ubicación:</span>
                        <span id="modal-location" class="detail-value">Auditorio Principal - Campus Central</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-icon"></span>
                        <span class="detail-label">Horario:</span>
                        <span id="modal-time" class="detail-value">9:00 AM - 6:00 PM</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-icon"></span>
                        <span class="detail-label">Dirigido a:</span>
                        <span id="modal-participants" class="detail-value">Estudiantes y profesionales de todas las
                            carreras</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-icon"></span>
                        <span class="detail-label">Detalles:</span>
                        <span id="modal-full-details" class="detail-value">Este evento reúne a los mejores talentos en
                            tecnología para resolver desafíos actuales de la industria.</span>
                    </div>
                </div>

                <div class="modal-actions">
                    <button class="btn btn-secondary" id="modal-share">
                        <span>📤</span> Compartir
                    </button>
                    <button class="btn btn-primary" id="modal-register">
                        <span>📝</span> Registrarse
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function openNewsModal(news) {
    // Actualizar contenido del modal
    document.getElementById('modal-image').src = news.image;
    document.getElementById('modal-title').textContent = news.title;
    document.getElementById('modal-description').textContent = news.description;
    document.getElementById('modal-full-details').textContent = news.details;
    document.getElementById('modal-date').textContent = `📅 ${news.date}`;
    document.getElementById('modal-location').textContent = news.location;
    document.getElementById('modal-time').textContent = news.time;
    document.getElementById('modal-participants').textContent = news.participants;

    // Actualizar badge según categoría
    const modalBadge = document.getElementById('modal-badge');
    modalBadge.textContent = news.category_label;
    modalBadge.className = 'modal-badge ' + news.category;

    // Mostrar modal
    document.getElementById('news-modal').classList.add('show');
    document.body.style.overflow = 'hidden';
}

function closeNewsModal() {
    document.getElementById('news-modal').classList.remove('show');
    document.body.style.overflow = 'auto';
}

function registerForEvent() {
    const title = document.getElementById('modal-title').textContent;
    alert(`¡Gracias por tu interés en "${title}"! Serás redirigido al formulario de registro.`);
    closeNewsModal();
}

function shareEvent() {
    const title = document.getElementById('modal-title').textContent;
    const url = window.location.href;
    
    if (navigator.share) {
        navigator.share({
            title: title,
            url: url
        }).then(() => {
            console.log('Evento compartido exitosamente');
        }).catch(err => {
            console.log('Error al compartir:', err);
            fallbackShare(title, url);
        });
    } else {
        fallbackShare(title, url);
    }
}

function fallbackShare(title, url) {
    // Fallback para navegadores que no soportan Web Share API
    navigator.clipboard.writeText(`${title} - ${url}`).then(() => {
        alert('Enlace copiado al portapapeles');
    }).catch(err => {
        // Fallback más básico
        const tempInput = document.createElement('input');
        tempInput.value = `${title} - ${url}`;
        document.body.appendChild(tempInput);
        tempInput.select();
        document.execCommand('copy');
        document.body.removeChild(tempInput);
        alert('Enlace copiado al portapapeles');
    });
}

// Event listeners
document.addEventListener('DOMContentLoaded', function() {
    const modalClose = document.getElementById('modal-close');
    const modalShare = document.getElementById('modal-share');
    const modalRegister = document.getElementById('modal-register');
    const modal = document.getElementById('news-modal');

    if (modalClose) {
        modalClose.addEventListener('click', closeNewsModal);
    }

    if (modalShare) {
        modalShare.addEventListener('click', shareEvent);
    }

    if (modalRegister) {
        modalRegister.addEventListener('click', registerForEvent);
    }

    // Cerrar modal al hacer clic fuera
    modal.addEventListener('click', function(event) {
        if (event.target === modal) {
            closeNewsModal();
        }
    });

    // Cerrar modal con tecla Escape
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeNewsModal();
        }
    });
});
</script>