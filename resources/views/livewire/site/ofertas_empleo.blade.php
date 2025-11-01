@section('title', 'Ofertas de Empleo')
@section('meta_description', 'Explora las últimas ofertas de empleo y oportunidades laborales disponibles.')

<div>
    <!-- Hero Section -->
    <section class="relative overflow-hidden bg-gradient-to-br from-blue-50 via-white to-blue-100">
        <div class="container mx-auto px-4 py-24 lg:py-32">
            <div class="text-center space-y-8">
                <div class="space-y-4">
                    <h1 class="text-4xl lg:text-6xl font-bold text-balance leading-tight">Bolsa de
                        <span class="text-blue-600">Trabajo</span>
                    </h1>
                    <p class="text-xl text-gray-600 text-pretty max-w-2xl mx-auto leading-relaxed">
                        Encuentra las últimas oportunidades laborales disponibles.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Offers Grid Section -->
    <section class="bg-gray-50 relative">
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
                            <input type="text" wire:model.live="currentSearchTerm" placeholder="Buscar ofertas..."
                                class="w-full pl-10 pr-4 py-3 rounded-lg border border-gray-300 bg-white text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="container mx-auto px-4 py-12">
            <div class="flex items-center justify-between mb-12 w-4/5 max-w-7xl mx-auto">
                <div>
                    <h2 class="text-3xl lg:text-4xl font-bold mb-4">
                        <span class="text-blue-600">Ofertas</span> Disponibles
                    </h2>
                    <p class="text-xl text-gray-600">
                        Explora oportunidades y postúlate antes de la fecha límite.
                    </p>
                </div>
                <div class="text-sm text-gray-500 hidden sm:block">
                    <p class="text-sm text-gray-700 leading-5 dark:text-gray-400">
                        <span>Mostrando</span>
                        <span class="font-medium">{{ $offers->firstItem() ?? 0 }}</span>
                        <span>de</span>
                        <span class="font-medium">{{ $offers->lastItem() ?? 0 }}</span>
                        <span>de</span>
                        <span class="font-medium">{{ $offers->total() ?? 0 }}</span>
                        <span>resultados</span>
                    </p>
                </div>
            </div>

            @if(count($offers) > 0)
            <div class="grid lg:grid-cols-3 gap-8 w-4/5 max-w-7xl mx-auto">
                @foreach($offers as $offer)
                <div class="bg-white rounded-xl overflow-hidden shadow-sm news-card-hover cursor-pointer group"
                    wire:key="offer-{{ $offer->id }}" onclick="openOfferModal(@js([
                        'id' => $offer->id,
                        'title' => $offer->title,
                        'company_name' => $offer->company_name,
                        'location' => $offer->location,
                        'salary' => $offer->salary ? number_format($offer->salary, 2) : null,
                        'vacancies' => $offer->vacancies,
                        'description' => $offer->description,
                        'image' => $offer->main_image ?? 'https://via.placeholder.com/800x500?text=Sin+Imagen',
                        'deadline' => optional($offer->application_deadline)->format('d/m/Y h:i A')
                    ]))">

                    <img src="{{ $offer->main_image ?? 'https://via.placeholder.com/800x500?text=Sin+Imagen' }}"
                        alt="{{ $offer->title }}"
                        class="w-full h-48 object-cover transition-transform duration-400 group-hover:scale-105">

                    <div class="p-6 flex flex-col gap-4">
                        <div class="flex items-center justify-between">
                            <span class="inline-flex items-center justify-center rounded-md px-3 py-1 text-xs font-medium badge-info">
                                {{ $offer->company_name }}
                            </span>
                            <div class="flex items-center text-sm text-gray-500">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-calendar h-4 w-4 mr-1">
                                    <path d="M8 2v4"></path>
                                    <path d="M16 2v4"></path>
                                    <rect width="18" height="18" x="3" y="4" rx="2"></rect>
                                    <path d="M3 10h18"></path>
                                </svg>
                                {{ optional($offer->application_deadline)->format('d/m/Y h:i A') ?? 'Sin fecha límite' }}
                            </div>
                        </div>

                        <h3 class="font-semibold text-xl text-balance group-hover:text-blue-600 transition-colors">
                            {{ $offer->title }}
                        </h3>

                        <p class="text-gray-600 text-base leading-relaxed">
                            {{ $offer->location }}
                        </p>

                        <div class="text-gray-700 text-sm">
                            @if($offer->salary)
                            <span class="font-medium">Salario:</span> ${{ number_format($offer->salary, 2) }}
                            @endif
                            <span class="ml-3 font-medium">Vacantes:</span> {{ $offer->vacancies }}
                        </div>

                        <div class="flex items-center text-sm text-blue-600 font-medium group-hover:gap-2 transition-all mt-2">
                            Ver detalles
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-right h-4 w-4 ml-1">
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
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-search h-8 w-8 text-gray-400">
                        <circle cx="11" cy="11" r="8"></circle>
                        <path d="m21 21-4.3-4.3"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold mb-2">No se encontraron ofertas</h3>
                <p class="text-gray-500">Intenta con otros términos de búsqueda.</p>
            </div>
            @endif

            @if (count($offers) > 0)
            <div class="mt-12 mx-auto w-4/5 max-w-7xl">
                {{ $offers->links() }}
            </div>
            @endif
        </div>
    </section>

    <!-- Modal Detalles Oferta -->
    <div id="offer-modal" class="news-modal" wire:ignore.self>
        <div class="modal-content">
            <div class="modal-header flex justify-center items-center">
                <img id="offer-modal-image" src="" alt="Oferta" class="max-h-96 w-auto object-contain mx-auto block">
                <button class="modal-close" id="offer-modal-close">×</button>
            </div>
            <div class="modal-body">
                <div class="modal-meta">
                    <span id="offer-modal-company" class="modal-badge">Empresa</span>
                    <span id="offer-modal-deadline" class="modal-date"></span>
                </div>
                <h2 id="offer-modal-title" class="modal-title"></h2>
                <p id="offer-modal-description" class="modal-description"></p>

                <div class="modal-details">
                    <div class="detail-item"><span class="detail-label">Ubicación:</span> <span id="offer-modal-location" class="detail-value"></span></div>
                    <div class="detail-item"><span class="detail-label">Vacantes:</span> <span id="offer-modal-vacancies" class="detail-value"></span></div>
                    <div class="detail-item" id="offer-modal-salary-row" style="display:none;"><span class="detail-label">Salario:</span> <span id="offer-modal-salary" class="detail-value"></span></div>
                </div>

                <div class="modal-actions">
                    <button class="btn btn-secondary" id="offer-modal-share"><span>📤</span> Compartir</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function openOfferModal(offer) {
        document.getElementById('offer-modal-image').src = offer.image;
        document.getElementById('offer-modal-title').textContent = offer.title;
        document.getElementById('offer-modal-company').textContent = offer.company_name;
        document.getElementById('offer-modal-description').textContent = offer.description;
        document.getElementById('offer-modal-location').textContent = offer.location;
        document.getElementById('offer-modal-vacancies').textContent = offer.vacancies;
        document.getElementById('offer-modal-deadline').textContent = `📅 ${offer.deadline ?? 'Sin fecha límite'}`;

        const salaryRow = document.getElementById('offer-modal-salary-row');
        if (offer.salary) {
            salaryRow.style.display = '';
            document.getElementById('offer-modal-salary').textContent = `$${offer.salary}`;
        } else {
            salaryRow.style.display = 'none';
        }

        document.getElementById('offer-modal').classList.add('show');
        document.body.style.overflow = 'hidden';
    }

    function closeOfferModal() {
        document.getElementById('offer-modal').classList.remove('show');
        document.body.style.overflow = 'auto';
    }

    function shareOffer() {
        const title = document.getElementById('offer-modal-title').textContent;
        const url = window.location.href;
        if (navigator.share) {
            navigator.share({ title, url }).catch(() => fallbackShare(title, url));
        } else {
            fallbackShare(title, url);
        }
    }

    function fallbackShare(title, url) {
        navigator.clipboard.writeText(`${title} - ${url}`).then(() => {
            alert('Enlace copiado al portapapeles');
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        const modalClose = document.getElementById('offer-modal-close');
        const modalShare = document.getElementById('offer-modal-share');
        const modal = document.getElementById('offer-modal');

        if (modalClose) modalClose.addEventListener('click', closeOfferModal);
        if (modalShare) modalShare.addEventListener('click', shareOffer);
        modal.addEventListener('click', function(event) {
            if (event.target === modal) closeOfferModal();
        });
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') closeOfferModal();
        });
    });
</script>
