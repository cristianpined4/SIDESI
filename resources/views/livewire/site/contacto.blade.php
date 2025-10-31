@section('title', 'Contacto')
@section('meta_description', 'Encuentra la información de contacto de las principales áreas académicas, administrativas y de apoyo.')

<main style="width: 100%;">
    <div class="loading" wire:loading.attr="show" show="false">
        <div class="loader"></div>
        <p class="loading-text">Cargando...</p>
    </div>

    {{-- Estilos del overlay --}}
    <style>
      .loading{
        position:fixed; inset:0; display:none; z-index:9999;
        background:rgba(0,0,0,.35); backdrop-filter:blur(2px);
        align-items:center; justify-content:center; flex-direction:column;
        pointer-events:all;
      }
      .loader{
        width:48px; height:48px; border-radius:50%;
        border:4px solid #e2e8f0; border-top-color:#2563eb;
        animation:spin .8s linear infinite;
      }
      .loading-text{ margin-top:12px; color:#fff; font-weight:600; }
      @keyframes spin{ to{ transform:rotate(360deg) } }
    </style>

    <div>
        <!-- Hero -->
        <section class="relative overflow-hidden bg-gradient-to-br from-blue-50 via-white to-blue-100">
            <div class="container mx-auto px-4 py-24 lg:py-32 text-center">
                <h1 class="text-4xl lg:text-6xl font-bold leading-tight">
                    <span class="text-blue-600">Contactos</span> Institucionales
                </h1>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto mt-4 leading-relaxed">
                    Encuentra la información de contacto de las principales áreas académicas, administrativas y de apoyo.
                </p>
            </div>
        </section>

        <!-- Directorio -->
        <section class="py-20 bg-gray-50">
            <div class="container mx-auto px-8 max-w-7xl">
                <h2 class="text-3xl font-bold text-center mb-14">
                    <span class="text-blue-600">Directorio</span> de Dependencias Universitarias
                </h2>

                <div class="grid gap-14 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach($contacts as $contact)
                    <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100 group">
                        @if(!empty($contact['image']))
                          <img src="{{ $contact['image'] }}" alt="{{ $contact['name'] }}"
                               class="w-full h-48 object-cover group-hover:scale-105 transition-transform duration-300">
                        @else
                          <div class="w-full h-48 bg-gray-100"></div>
                        @endif

                        <div class="p-6">
                            <div class="flex items-center justify-between text-sm text-gray-500 mb-2">
                                <span class="{{ $contact['category_color'] }} font-medium px-3 py-1 rounded-full text-xs">
                                    {{ $contact['category'] }}
                                </span>
                                <div class="flex items-center text-gray-600">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-1 text-blue-600" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 3.07 8.81 19.79 19.79 0 0 1 0 0.18 2 2 0 0 1 2.18 0h3a2 2 0 0 1 2 1.72c.13 1.21.44 2.38.93 3.49a2 2 0 0 1-.45 2.11L6.09 9.91a16 16 0 0 0 8 8l2.59-1.54a2 2 0 0 1 2.11-.45c1.11.49 2.28.8 3.49.93a2 2 0 0 1 1.72 2z" />
                                    </svg>
                                    {{ $contact['phone'] }}
                                </div>
                            </div>

                            <h3 class="text-xl font-semibold text-gray-800 mb-2 group-hover:text-blue-600 transition-colors">
                                {{ $contact['name'] }}
                            </h3>

                            <p class="text-gray-600 mb-3 leading-relaxed">
                                {{ $contact['description'] }}
                            </p>

                            <p class="text-blue-600 font-medium mb-4">
                                {{ $contact['email'] }}
                            </p>

                            <button type="button"
                                class="inline-flex items-center text-blue-600 font-semibold hover:underline"
                                onclick="openContactModal(this)"
                                data-name="{{ $contact['name'] }}"
                                data-email="{{ $contact['email'] }}"
                                data-phone="{{ $contact['phone'] }}"
                                data-description="{{ $contact['description'] }}"
                                data-more="{{ $contact['more'] }}">
                                Leer más
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 ml-1" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                </svg>
                            </button>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- Modal reutilizable --}}
        <div id="contact-modal"
            class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 backdrop-blur-sm">
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-xl border border-gray-100 overflow-hidden">
                <div class="px-6 py-5 border-b">
                    <h3 id="cm-title" class="text-2xl font-bold text-gray-800">Título</h3>
                    <p id="cm-desc" class="text-gray-600 mt-1">Descripción</p>
                </div>

                <div class="px-6 py-5 space-y-3 text-gray-700">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-600" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M22 16.92v3a2 2 0 0 1-2.18 2 19.8 19.8 0 0 1-8.63-3.07A19.5 19.5 0 0 1 3.07 8.81 19.8 19.8 0 0 1 0 0.18 2 2 0 0 1 2.18 0h3a2 2 0 0 1 2 1.72c.13 1.21.44 2.38.93 3.49a2 2 0 0 1-.45 2.11L6.09 9.91a16 16 0 0 0 8 8l2.59-1.54a2 2 0 0 1 2.11-.45c1.11.49 2.28.8 3.49.93a2 2 0 0 1 1.72 2z" />
                        </svg>
                        <span id="cm-phone" class="font-medium">Teléfono</span>
                    </div>
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-600" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 12H8m8 4H8m8-8H8M5 20h14a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2H5c-1.1 0-2 .9-2 2v12a2 2 0 0 0 2 2z" />
                        </svg>
                        <a id="cm-email" href="#" class="text-blue-600 hover:underline">correo@ues.edu.sv</a>
                    </div>
                    <pre id="cm-more" class="whitespace-pre-wrap text-gray-700 bg-gray-50 rounded-lg p-3"></pre>
                </div>

                <div class="px-6 py-4 border-t bg-gray-50 flex justify-end gap-2">
                    <button type="button" onclick="closeContactModal()"
                        class="px-4 py-2 rounded-lg border hover:bg-gray-100">Cerrar</button>
                    <a id="cm-mailto" href="#" class="px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700">
                        Escribir correo
                    </a>
                </div>
            </div>
        </div>
    </div> {{-- cierra el componente --}}
</main>
    <script>
      // Helpers para el loader global
      function showLoader() {
        const loader = document.querySelector('.loading');
        if (!loader) return;
        loader.setAttribute('show', 'true');
        loader.style.display = 'flex';
      }
      function hideLoader() {
        const loader = document.querySelector('.loading');
        if (!loader) return;
        loader.removeAttribute('show');
        loader.style.display = 'none';
      }

      function openContactModal(btn) {
        showLoader(); // muestra "Cargando..."

        // Si luego haces fetch/Livewire, mueve hideLoader() al .then/.finally
        setTimeout(() => {
          const modal = document.getElementById('contact-modal');

          // Poblar campos del modal
          document.getElementById('cm-title').textContent = btn.dataset.name || '';
          document.getElementById('cm-desc').textContent = btn.dataset.description || '';
          document.getElementById('cm-phone').textContent = btn.dataset.phone || '—';

          const email = btn.dataset.email || '';
          const cmEmail = document.getElementById('cm-email');
          cmEmail.textContent = email || '—';
          cmEmail.href = email ? `mailto:${email}` : '#';

          document.getElementById('cm-more').textContent = btn.dataset.more || '';

          const mailto = document.getElementById('cm-mailto');
          mailto.href = email ? `mailto:${email}` : '#';

          // Mostrar modal y ocultar loader
          modal.classList.remove('hidden');
          modal.classList.add('flex');
          hideLoader();
        }, 250); // leve delay para feedback visual
      }

      function closeContactModal() {
        const modal = document.getElementById('contact-modal');
        modal.classList.remove('flex');
        modal.classList.add('hidden');
      } 
      // Cerrar con ESC
      document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closeContactModal();
      });
    </script>

