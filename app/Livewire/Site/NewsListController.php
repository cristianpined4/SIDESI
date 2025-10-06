<?php

namespace App\Livewire\Site;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Pagination\LengthAwarePaginator;

class NewsListController extends Component
{
    use WithPagination;

    public $currentCategory = 'all';
    public $currentSearchTerm = '';
    public $paginate = 20;

    protected $news = [
        [
            'id' => 1,
            'category' => 'evento',
            'category_label' => 'Evento',
            'badge_class' => 'bg-secondary text-secondary-foreground',
            'title' => 'Hackathon de Innovación Tecnológica 2024',
            'description' => 'Participa en el hackathon más grande del año y desarrolla soluciones innovadoras para problemas reales.',
            'details' => 'Este evento reúne a los mejores talentos en tecnología para resolver desafíos actuales de la industria. Incluye mentorías, talleres técnicos y premios en efectivo.',
            'image' => 'https://images.unsplash.com/photo-1540575467063-178a50c2df87?w=800&h=500&fit=crop',
            'date' => '15 de Marzo, 2024',
            'location' => 'Auditorio Principal - Campus Central',
            'time' => '9:00 AM - 6:00 PM',
            'participants' => 'Estudiantes y profesionales de todas las carreras'
        ],
        [
            'id' => 2,
            'category' => 'empleo',
            'category_label' => 'Empleo',
            'badge_class' => 'bg-secondary text-secondary-foreground',
            'title' => 'Bolsa de Empleo: Oportunidades para Egresados',
            'description' => 'Nuevas ofertas laborales disponibles para recién graduados y profesionales con experiencia en el sector tecnológico.',
            'details' => 'Conectamos a nuestros egresados con las mejores empresas del sector tecnológico. Incluye procesos de reclutamiento acelerado y sesiones de networking exclusivas.',
            'image' => 'https://images.unsplash.com/photo-1552664730-d307ca884978?w=800&h=500&fit=crop',
            'date' => '12 de Marzo, 2024',
            'location' => 'Plataforma Virtual SIDESI',
            'time' => 'Todo el día',
            'participants' => 'Egresados y estudiantes de últimos años'
        ],
        [
            'id' => 3,
            'category' => 'evento',
            'category_label' => 'Conferencia',
            'badge_class' => 'bg-secondary text-secondary-foreground',
            'title' => 'Conferencia Internacional de Inteligencia Artificial',
            'description' => 'Únete a expertos mundiales en IA para discutir las últimas tendencias y avances en aprendizaje automático.',
            'details' => 'Evento internacional que cuenta con la participación de investigadores líderes en IA. Incluye presentaciones de casos de estudio, demostraciones en vivo y oportunidades de networking.',
            'image' => 'https://images.unsplash.com/photo-1591115765373-5207764f72e7?w=800&h=500&fit=crop',
            'date' => '10 de Marzo, 2024',
            'location' => 'Centro de Convenciones Universitario',
            'time' => '8:00 AM - 5:00 PM',
            'participants' => 'Estudiantes, investigadores y profesionales del área'
        ],
        [
            'id' => 4,
            'category' => 'taller',
            'category_label' => 'Taller',
            'badge_class' => 'bg-secondary text-secondary-foreground',
            'title' => 'Workshop de Desarrollo Web Full Stack',
            'description' => 'Aprende a crear aplicaciones web modernas desde cero utilizando las tecnologías más demandadas del mercado.',
            'details' => 'Taller práctico intensivo donde aprenderás las tecnologías más modernas para desarrollo web. Incluye proyectos reales, código en vivo y certificación al finalizar.',
            'image' => 'https://images.unsplash.com/photo-1504384308090-c894fdcc538d?w=800&h=500&fit=crop',
            'date' => '8 de Marzo, 2024',
            'location' => 'Laboratorio de Computación - Edificio B',
            'time' => '10:00 AM - 2:00 PM',
            'participants' => 'Estudiantes con conocimientos básicos de programación'
        ],
        [
            'id' => 5,
            'category' => 'empleo',
            'category_label' => 'Becas',
            'badge_class' => 'bg-secondary text-secondary-foreground',
            'title' => 'Programa de Becas para Estudios de Posgrado',
            'description' => 'Convocatoria abierta para solicitar becas completas en maestrías y doctorados en universidades internacionales.',
            'details' => 'Programa exclusivo que ofrece becas completas para estudios de posgrado en universidades de prestigio mundial. Incluye asesoramiento personalizado para el proceso de aplicación.',
            'image' => 'https://images.unsplash.com/photo-1523240795612-9a054b0db644?w=800&h=500&fit=crop',
            'date' => '5 de Marzo, 2024',
            'location' => 'Oficina de Relaciones Internacionales',
            'time' => '8:00 AM - 4:00 PM',
            'participants' => 'Estudiantes con promedio superior a 8.5'
        ],
        [
            'id' => 6,
            'category' => 'egresados',
            'category_label' => 'Egresados',
            'badge_class' => 'bg-blue-600 text-white',
            'title' => 'Encuentro de Egresados de Ingeniería',
            'description' => 'Reunión anual para fortalecer la red profesional y compartir experiencias entre egresados de ingeniería.',
            'details' => 'Evento exclusivo para egresados donde se realizarán charlas, mesas de discusión y actividades de networking. Ideal para ampliar contactos profesionales y conocer nuevas oportunidades laborales.',
            'image' => 'https://universae.com/wp-content/uploads/2023/06/que-es-el-networking-1200x900.webp',
            'date' => '12 de Octubre, 2025',
            'location' => 'Auditorio Central de Ingeniería',
            'time' => '9:00 AM - 3:00 PM',
            'participants' => 'Egresados de todas las generaciones'
        ],
        [
            'id' => 7,
            'category' => 'egresados',
            'category_label' => 'Egresados',
            'badge_class' => 'bg-green-600 text-white',
            'title' => 'Premios a la Trayectoria Profesional',
            'description' => 'Reconocimiento a egresados destacados por su aporte en el ámbito profesional y académico.',
            'details' => 'Ceremonia de premiación para celebrar los logros de egresados que han sobresalido en sus carreras, investigación o emprendimiento. Incluye testimonios, fotos y cobertura mediática.',
            'image' => 'https://media.istockphoto.com/id/2062707205/es/foto/estrella-dorada-sobre-fondo-azul-como-recompensa-premio-al-mejor-rendimiento-copa-de-campeones.jpg?s=612x612&w=0&k=20&c=Pplk9-RG88yIdCQDN0aIQ-LBW10TnmDVgRyjnISbSoI=',
            'date' => '22 de Noviembre, 2025',
            'location' => 'Salón de Eventos Universitario',
            'time' => '6:00 PM - 9:00 PM',
            'participants' => 'Egresados nominados y comunidad académica'
        ],
    ];

    public function getFilteredNews()
    {
        // Convertimos las noticias en colección
        $filtered = collect($this->news)
            ->when($this->currentCategory !== 'all', function ($collection) {
                return $collection->where('category', $this->currentCategory);
            })
            ->when($this->currentSearchTerm, function ($collection) {
                return $collection->filter(function ($news) {
                    return str_contains(strtolower($news['title']), strtolower($this->currentSearchTerm)) ||
                        str_contains(strtolower($news['description']), strtolower($this->currentSearchTerm));
                });
            });

        // Obtenemos la página actual y el número de elementos por página
        $page = request()->get('page', 1);
        $perPage = $this->paginate;

        // Hacemos el "slice" para obtener solo los elementos de la página actual
        $currentPageItems = $filtered->slice(($page - 1) * $perPage, $perPage)->values();

        // Creamos el paginador manual
        return new LengthAwarePaginator(
            $currentPageItems,
            $filtered->count(), // Total de elementos
            $perPage,
            $page,
            [
                'path' => request()->url(),
                'query' => request()->query(),
            ]
        );
    }

    public function paginationView()
    {
        return 'vendor.livewire.tailwind';
    }

    public function render()
    {
        return view('livewire.site.news-list-controller', [
            'filteredNews' => $this->getFilteredNews()
        ])
            ->extends('layouts.site')
            ->section('content');
    }
}