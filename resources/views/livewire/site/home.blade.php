@section('title', "SIDESI - Sistema de Desarrollo de Estudiantes de Ingeniería")

<main>
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
    <!-- fin modales -->

    <!-- Contenido - inicio -->
    <section class="relative overflow-hidden bg-gradient-to-br from-background via-card to-secondary/20">
        <div class="container mx-auto px-4 py-24 lg:py-32">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <div class="space-y-8">
                    <div class="space-y-4">
                        <h1 class="text-4xl lg:text-6xl font-bold text-balance leading-tight">Sección de Ingeniería de
                            <span class="text-primary">Sistemas Informáticos</span>
                        </h1>
                        <p class="text-xl text-muted-foreground text-pretty leading-relaxed">Plataforma integral para la
                            gestión
                            académica, eventos, desarrollo profesional y comunicación institucional de la comunidad
                            estudiantil de
                            ingeniería.
                        </p>
                    </div>
                    <div class="flex flex-col sm:flex-row gap-4">
                        <!-- Botón: Explorar Eventos -->
                        <a href="{{ route('site.eventos') }}"
                            class="inline-flex items-center justify-center gap-2 whitespace-nowrap font-medium transition-all disabled:pointer-events-none disabled:opacity-50 [&_svg]:pointer-events-none [&_svg:not([class*='size-'])]:size-4 shrink-0 [&_svg]:shrink-0 outline-none focus-visible:border-ring focus-visible:ring-ring/50 focus-visible:ring-[3px] aria-invalid:ring-destructive/20 dark:aria-invalid:ring-destructive/40 aria-invalid:border-destructive bg-primary text-primary-foreground shadow-xs hover:bg-primary/90 h-10 rounded-md px-6 has-[>svg]:px-4 text-base">
                            Explorar Eventos
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="lucide lucide-arrow-right ml-2 h-5 w-5">
                                <path d="M5 12h14"></path>
                                <path d="m12 5 7 7-7 7"></path>
                            </svg>
                        </a>

                        <!-- Botón: Ver Documentos -->
                        <a href="{{ route('site.documentos') }}"
                            class="inline-flex items-center justify-center gap-2 whitespace-nowrap font-medium transition-all disabled:pointer-events-none disabled:opacity-50 [&_svg]:pointer-events-none [&_svg:not([class*='size-'])]:size-4 shrink-0 [&_svg]:shrink-0 outline-none focus-visible:border-ring focus-visible:ring-ring/50 focus-visible:ring-[3px] aria-invalid:ring-destructive/20 dark:aria-invalid:ring-destructive/40 aria-invalid:border-destructive border shadow-xs hover:bg-accent hover:text-accent-foreground dark:bg-input/30 dark:border-input dark:hover:bg-input/50 h-10 rounded-md px-6 has-[>svg]:px-4 text-base bg-transparent">
                            Ver Documentos
                        </a>
                    </div>

                    <div class="grid grid-cols-3 gap-6 pt-8">
                        <div class="text-center">
                            <div
                                class="h-12 w-12 mx-auto mb-3 rounded-lg bg-primary/10 flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="lucide lucide-calendar h-6 w-6 text-primary">
                                    <path d="M8 2v4"></path>
                                    <path d="M16 2v4"></path>
                                    <rect width="18" height="18" x="3" y="4" rx="2"></rect>
                                    <path d="M3 10h18"></path>
                                </svg>
                            </div>
                            <div class="text-2xl font-bold text-foreground">150+</div>
                            <div class="text-sm text-muted-foreground">Eventos Anuales</div>
                        </div>
                        <div class="text-center">
                            <div
                                class="h-12 w-12 mx-auto mb-3 rounded-lg bg-primary/10 flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="lucide lucide-users h-6 w-6 text-primary">
                                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="9" cy="7" r="4"></circle>
                                    <path d="M22 21v-2a4 4 0 0 0-3-3.87"></path>
                                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                </svg>
                            </div>
                            <div class="text-2xl font-bold text-foreground">5,000+</div>
                            <div class="text-sm text-muted-foreground">Estudiantes</div>
                        </div>
                        <div class="text-center">
                            <div
                                class="h-12 w-12 mx-auto mb-3 rounded-lg bg-primary/10 flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="lucide lucide-file-text h-6 w-6 text-primary">
                                    <path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"></path>
                                    <path d="M14 2v4a2 2 0 0 0 2 2h4"></path>
                                    <path d="M10 9H8"></path>
                                    <path d="M16 13H8"></path>
                                    <path d="M16 17H8"></path>
                                </svg>
                            </div>
                            <div class="text-2xl font-bold text-foreground">200+</div>
                            <div class="text-sm text-muted-foreground">Recursos</div>
                        </div>
                    </div>
                </div>
                <div class="relative">
                    <div class="aspect-square rounded-2xl bg-gradient-to-br from-primary/20 to-accent/20 p-8">
                        <div class="h-full w-full rounded-xl bg-card shadow-2xl flex items-center justify-center">
                            <div class="text-center space-y-4">
                                <div class="h-16 w-16 mx-auto rounded-full bg-primary flex items-center justify-center">
                                    <span class="text-2xl font-bold text-primary-foreground">S</span>
                                </div>
                                <div class="space-y-2">
                                    <h3 class="text-xl font-semibold">Portal Estudiantil</h3>
                                    <p class="text-muted-foreground text-sm">Accede a todos los servicios y recursos
                                        académicos</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="py-24 bg-card">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16">
                <h2 class="text-3xl lg:text-4xl font-bold mb-4 text-balance">Herramientas para tu
                    <span class="text-primary">Desarrollo Académico</span>
                </h2>
                <p class="text-xl text-muted-foreground text-pretty max-w-2xl mx-auto">Descubre todas las
                    funcionalidades
                    diseñadas para potenciar tu experiencia educativa y desarrollo profesional.</p>
            </div>
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div data-slot="card"
                    class="bg-card text-card-foreground flex flex-col gap-6 rounded-xl py-6 border-0 shadow-sm hover:shadow-md transition-shadow">
                    <div data-slot="card-header"
                        class="@container/card-header grid auto-rows-min grid-rows-[auto_auto] items-start gap-1.5 px-6 has-data-[slot=card-action]:grid-cols-[1fr_auto] [.border-b]:pb-6">
                        <div class="h-12 w-12 rounded-lg bg-primary/10 flex items-center justify-center mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="lucide lucide-calendar h-6 w-6 text-primary">
                                <path d="M8 2v4"></path>
                                <path d="M16 2v4"></path>
                                <rect width="18" height="18" x="3" y="4" rx="2"></rect>
                                <path d="M3 10h18"></path>
                            </svg>
                        </div>
                        <div data-slot="card-title" class="font-semibold text-xl">Gestión de Eventos</div>
                    </div>
                    <div data-slot="card-content" class="px-6">
                        <div data-slot="card-description" class="text-muted-foreground text-base leading-relaxed">
                            Organiza y
                            participa en talleres, conferencias y actividades académicas con inscripción automática.
                        </div>
                    </div>
                </div>
                <div data-slot="card"
                    class="bg-card text-card-foreground flex flex-col gap-6 rounded-xl py-6 border-0 shadow-sm hover:shadow-md transition-shadow">
                    <div data-slot="card-header"
                        class="@container/card-header grid auto-rows-min grid-rows-[auto_auto] items-start gap-1.5 px-6 has-data-[slot=card-action]:grid-cols-[1fr_auto] [.border-b]:pb-6">
                        <div class="h-12 w-12 rounded-lg bg-primary/10 flex items-center justify-center mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="lucide lucide-file-text h-6 w-6 text-primary">
                                <path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"></path>
                                <path d="M14 2v4a2 2 0 0 0 2 2h4"></path>
                                <path d="M10 9H8"></path>
                                <path d="M16 13H8"></path>
                                <path d="M16 17H8"></path>
                            </svg>
                        </div>
                        <div data-slot="card-title" class="font-semibold text-xl">Centro de Documentos</div>
                    </div>
                    <div data-slot="card-content" class="px-6">
                        <div data-slot="card-description" class="text-muted-foreground text-base leading-relaxed">Accede
                            a
                            planes de estudio, reglamentos, guías y material académico actualizado.</div>
                    </div>
                </div>
                <div data-slot="card"
                    class="bg-card text-card-foreground flex flex-col gap-6 rounded-xl py-6 border-0 shadow-sm hover:shadow-md transition-shadow">
                    <div data-slot="card-header"
                        class="@container/card-header grid auto-rows-min grid-rows-[auto_auto] items-start gap-1.5 px-6 has-data-[slot=card-action]:grid-cols-[1fr_auto] [.border-b]:pb-6">
                        <div class="h-12 w-12 rounded-lg bg-primary/10 flex items-center justify-center mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="lucide lucide-users h-6 w-6 text-primary">
                                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                                <circle cx="9" cy="7" r="4"></circle>
                                <path d="M22 21v-2a4 4 0 0 0-3-3.87"></path>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                            </svg>
                        </div>
                        <div data-slot="card-title" class="font-semibold text-xl">Comunidad Estudiantil</div>
                    </div>
                    <div data-slot="card-content" class="px-6">
                        <div data-slot="card-description" class="text-muted-foreground text-base leading-relaxed">
                            Conecta con
                            estudiantes, docentes y egresados en una plataforma integrada.</div>
                    </div>
                </div>
                <div data-slot="card"
                    class="bg-card text-card-foreground flex flex-col gap-6 rounded-xl py-6 border-0 shadow-sm hover:shadow-md transition-shadow">
                    <div data-slot="card-header"
                        class="@container/card-header grid auto-rows-min grid-rows-[auto_auto] items-start gap-1.5 px-6 has-data-[slot=card-action]:grid-cols-[1fr_auto] [.border-b]:pb-6">
                        <div class="h-12 w-12 rounded-lg bg-primary/10 flex items-center justify-center mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="lucide lucide-credit-card h-6 w-6 text-primary">
                                <rect width="20" height="14" x="2" y="5" rx="2"></rect>
                                <line x1="2" x2="22" y1="10" y2="10"></line>
                            </svg>
                        </div>
                        <div data-slot="card-title" class="font-semibold text-xl">Pagos en Línea</div>
                    </div>
                    <div data-slot="card-content" class="px-6">
                        <div data-slot="card-description" class="text-muted-foreground text-base leading-relaxed">
                            Realiza pagos
                            seguros para eventos y servicios con comprobantes electrónicos.</div>
                    </div>
                </div>
                <div data-slot="card"
                    class="bg-card text-card-foreground flex flex-col gap-6 rounded-xl py-6 border-0 shadow-sm hover:shadow-md transition-shadow">
                    <div data-slot="card-header"
                        class="@container/card-header grid auto-rows-min grid-rows-[auto_auto] items-start gap-1.5 px-6 has-data-[slot=card-action]:grid-cols-[1fr_auto] [.border-b]:pb-6">
                        <div class="h-12 w-12 rounded-lg bg-primary/10 flex items-center justify-center mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="lucide lucide-award h-6 w-6 text-primary">
                                <path
                                    d="m15.477 12.89 1.515 8.526a.5.5 0 0 1-.81.47l-3.58-2.687a1 1 0 0 0-1.197 0l-3.586 2.686a.5.5 0 0 1-.81-.469l1.514-8.526">
                                </path>
                                <circle cx="12" cy="8" r="6"></circle>
                            </svg>
                        </div>
                        <div data-slot="card-title" class="font-semibold text-xl">Certificaciones</div>
                    </div>
                    <div data-slot="card-content" class="px-6">
                        <div data-slot="card-description" class="text-muted-foreground text-base leading-relaxed">Obtén
                            certificados digitales verificables con códigos QR para tus logros académicos.</div>
                    </div>
                </div>
                <div data-slot="card"
                    class="bg-card text-card-foreground flex flex-col gap-6 rounded-xl py-6 border-0 shadow-sm hover:shadow-md transition-shadow">
                    <div data-slot="card-header"
                        class="@container/card-header grid auto-rows-min grid-rows-[auto_auto] items-start gap-1.5 px-6 has-data-[slot=card-action]:grid-cols-[1fr_auto] [.border-b]:pb-6">
                        <div class="h-12 w-12 rounded-lg bg-primary/10 flex items-center justify-center mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="lucide lucide-search h-6 w-6 text-primary">
                                <circle cx="11" cy="11" r="8"></circle>
                                <path d="m21 21-4.3-4.3"></path>
                            </svg>
                        </div>
                        <div data-slot="card-title" class="font-semibold text-xl">Búsqueda Avanzada</div>
                    </div>
                    <div data-slot="card-content" class="px-6">
                        <div data-slot="card-description" class="text-muted-foreground text-base leading-relaxed">
                            Encuentra
                            rápidamente noticias, eventos, documentos y oportunidades profesionales.</div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="py-24 bg-background">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-between mb-12">
                <div>
                    <h2 class="text-3xl lg:text-4xl font-bold mb-4">Últimas
                        <span class="text-primary">Noticias</span>
                    </h2>
                    <p class="text-xl text-muted-foreground">Mantente informado sobre eventos, convocatorias y
                        oportunidades.
                    </p>
                </div>
                <a href="/noticias"
                    class="items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium transition-all disabled:pointer-events-none disabled:opacity-50 [&_svg]:pointer-events-none [&_svg:not([class*='size-'])]:size-4 shrink-0 [&_svg]:shrink-0 outline-none focus-visible:border-ring focus-visible:ring-ring/50 focus-visible:ring-[3px] aria-invalid:ring-destructive/20 dark:aria-invalid:ring-destructive/40 aria-invalid:border-destructive border shadow-xs hover:bg-accent hover:text-accent-foreground dark:bg-input/30 dark:border-input dark:hover:bg-input/50 h-9 px-4 py-2 has-[>svg]:px-3 hidden sm:flex bg-transparent">
                    Ver Todas
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="lucide lucide-arrow-right ml-2 h-4 w-4">
                        <path d="M5 12h14"></path>
                        <path d="m12 5 7 7-7 7"></path>
                    </svg>
                </a>

            </div>
            <div class="grid lg:grid-cols-3 gap-8">
                <div data-slot="card"
                    class="bg-card text-card-foreground flex flex-col gap-6 rounded-xl py-6 border-0 shadow-sm hover:shadow-md transition-all cursor-pointer lg:col-span-2 lg:row-span-1">
                    <div data-slot="card-header"
                        class="@container/card-header grid auto-rows-min grid-rows-[auto_auto] items-start gap-1.5 px-6 has-data-[slot=card-action]:grid-cols-[1fr_auto] [.border-b]:pb-6">
                        <div class="flex items-center justify-between mb-2">
                            <span data-slot="badge"
                                class="inline-flex items-center justify-center rounded-md border px-2 py-0.5 text-xs font-medium w-fit whitespace-nowrap shrink-0 [&amp;&gt;svg]:size-3 gap-1 [&amp;&gt;svg]:pointer-events-none focus-visible:border-ring focus-visible:ring-ring/50 focus-visible:ring-[3px] aria-invalid:ring-destructive/20 dark:aria-invalid:ring-destructive/40 aria-invalid:border-destructive transition-[color,box-shadow] overflow-hidden border-transparent bg-secondary text-secondary-foreground [a&amp;]:hover:bg-secondary/90">Convocatoria</span>
                            <div class="flex items-center text-sm text-muted-foreground">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="lucide lucide-calendar h-4 w-4 mr-1">
                                    <path d="M8 2v4"></path>
                                    <path d="M16 2v4"></path>
                                    <rect width="18" height="18" x="3" y="4" rx="2"></rect>
                                    <path d="M3 10h18"></path>
                                </svg>15 de Marzo, 2024
                            </div>
                        </div>
                        <div data-slot="card-title" class="font-semibold text-2xl text-balance">Convocatoria Abierta:
                            Congreso
                            Nacional de Ingeniería 2024</div>
                    </div>
                    <div data-slot="card-content" class="px-6">
                        <div data-slot="card-description" class="text-muted-foreground text-base leading-relaxed">Se
                            abre la
                            convocatoria para participar en el evento más importante del año con ponencias magistrales y
                            talleres
                            especializados.</div>
                    </div>
                </div>
                <div data-slot="card"
                    class="bg-card text-card-foreground flex flex-col gap-6 rounded-xl py-6 border-0 shadow-sm hover:shadow-md transition-all cursor-pointer">
                    <div data-slot="card-header"
                        class="@container/card-header grid auto-rows-min grid-rows-[auto_auto] items-start gap-1.5 px-6 has-data-[slot=card-action]:grid-cols-[1fr_auto] [.border-b]:pb-6">
                        <div class="flex items-center justify-between mb-2">
                            <span data-slot="badge"
                                class="inline-flex items-center justify-center rounded-md border px-2 py-0.5 text-xs font-medium w-fit whitespace-nowrap shrink-0 [&amp;&gt;svg]:size-3 gap-1 [&amp;&gt;svg]:pointer-events-none focus-visible:border-ring focus-visible:ring-ring/50 focus-visible:ring-[3px] aria-invalid:ring-destructive/20 dark:aria-invalid:ring-destructive/40 aria-invalid:border-destructive transition-[color,box-shadow] overflow-hidden border-transparent bg-secondary text-secondary-foreground [a&amp;]:hover:bg-secondary/90">Educación</span>
                            <div class="flex items-center text-sm text-muted-foreground">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="lucide lucide-calendar h-4 w-4 mr-1">
                                    <path d="M8 2v4"></path>
                                    <path d="M16 2v4"></path>
                                    <rect width="18" height="18" x="3" y="4" rx="2"></rect>
                                    <path d="M3 10h18"></path>
                                </svg>12 de Marzo, 2024
                            </div>
                        </div>
                        <div data-slot="card-title" class="font-semibold text-xl text-balance">Nuevos Cursos de
                            Certificación
                            Profesional</div>
                    </div>
                    <div data-slot="card-content" class="px-6">
                        <div data-slot="card-description" class="text-muted-foreground text-base leading-relaxed">Amplía
                            tus
                            competencias con nuestros nuevos programas de certificación en tecnologías emergentes.</div>
                    </div>
                </div>
                <div data-slot="card"
                    class="bg-card text-card-foreground flex flex-col gap-6 rounded-xl py-6 border-0 shadow-sm hover:shadow-md transition-all cursor-pointer">
                    <div data-slot="card-header"
                        class="@container/card-header grid auto-rows-min grid-rows-[auto_auto] items-start gap-1.5 px-6 has-data-[slot=card-action]:grid-cols-[1fr_auto] [.border-b]:pb-6">
                        <div class="flex items-center justify-between mb-2">
                            <span data-slot="badge"
                                class="inline-flex items-center justify-center rounded-md border px-2 py-0.5 text-xs font-medium w-fit whitespace-nowrap shrink-0 [&amp;&gt;svg]:size-3 gap-1 [&amp;&gt;svg]:pointer-events-none focus-visible:border-ring focus-visible:ring-ring/50 focus-visible:ring-[3px] aria-invalid:ring-destructive/20 dark:aria-invalid:ring-destructive/40 aria-invalid:border-destructive transition-[color,box-shadow] overflow-hidden border-transparent bg-secondary text-secondary-foreground [a&amp;]:hover:bg-secondary/90">Empleo</span>
                            <div class="flex items-center text-sm text-muted-foreground">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="lucide lucide-calendar h-4 w-4 mr-1">
                                    <path d="M8 2v4"></path>
                                    <path d="M16 2v4"></path>
                                    <rect width="18" height="18" x="3" y="4" rx="2"></rect>
                                    <path d="M3 10h18"></path>
                                </svg>10 de Marzo, 2024
                            </div>
                        </div>
                        <div data-slot="card-title" class="font-semibold text-xl text-balance">Bolsa de Empleo:
                            Oportunidades
                            para Egresados</div>
                    </div>
                    <div data-slot="card-content" class="px-6">
                        <div data-slot="card-description" class="text-muted-foreground text-base leading-relaxed">Nuevas
                            ofertas
                            laborales disponibles para recién graduados y profesionales con experiencia.</div>
                    </div>
                </div>
            </div>
            <div class="text-center mt-8 sm:hidden"><button data-slot="button"
                    class="inline-flex items-center justify-center gap-2 whitespace-nowrap rounded-md text-sm font-medium transition-all disabled:pointer-events-none disabled:opacity-50 [&amp;_svg]:pointer-events-none [&amp;_svg:not([class*=&#x27;size-&#x27;])]:size-4 shrink-0 [&amp;_svg]:shrink-0 outline-none focus-visible:border-ring focus-visible:ring-ring/50 focus-visible:ring-[3px] aria-invalid:ring-destructive/20 dark:aria-invalid:ring-destructive/40 aria-invalid:border-destructive border bg-background shadow-xs hover:bg-accent hover:text-accent-foreground dark:bg-input/30 dark:border-input dark:hover:bg-input/50 h-9 px-4 py-2 has-[&gt;svg]:px-3">Ver
                    Todas las Noticias
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="lucide lucide-arrow-right ml-2 h-4 w-4">
                        <path d="M5 12h14"></path>
                        <path d="m12 5 7 7-7 7"></path>
                    </svg>
                </button>
            </div>
        </div>
    </section>
    <!-- Contenido - fin -->
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
            }
        });
    });

    const confirmarEliminar = async id => {
        if (await window.Confirm(
                'Eliminar',
                '¿Estas seguro de eliminar este Home?',
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