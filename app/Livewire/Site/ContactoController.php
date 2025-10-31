<?php

namespace App\Livewire\Site;

use Livewire\Component;

class ContactoController extends Component
{
    public array $contacts = [];

    public function mount(): void
    {
        // Puedes cambiar las imágenes a las tuyas en /public/images/contacts/*
        $this->contacts = [
            // UES Central
            [
                'name' => 'Universidad de El Salvador – PBX y Relaciones',
                'category' => 'Administración',
                'category_color' => 'bg-blue-100 text-blue-700',
                'phone' => '+503 2511-2000',
                'email' => 'secretaria.relaciones@ues.edu.sv',
                'image' => asset('images/contacts/ues-campus.jpg'),
                'description' => 'Atención general y relaciones institucionales de la UES.',
                'more' => "Dirección: Ciudad Universitaria, San Salvador.\nHorario: Lun–Vie 08:00–16:00.\n¿En qué ayudan?: Canalizan consultas generales, derivan a dependencias y apoyan trámites institucionales."
            ],
            [
                'name' => 'Ingreso Universitario – UES',
                'category' => 'Académico',
                'category_color' => 'bg-emerald-100 text-emerald-700',
                'phone' => '+503 2511-2012',
                'email' => 'ingreso.universitario@ues.edu.sv',
                'image' => asset('images/contacts/ingreso.jpg'),
                'description' => 'Consultas sobre proceso de admisión, requisitos y resultados.',
                'more' => "Horario: Lun–Vie 08:00–12:00 y 13:00–16:00.\n¿En qué ayudan?: Inscripción a pruebas, consulta de resultados, pasos posteriores al ingreso."
            ],
            [
                'name' => 'Sistema Bibliotecario – Biblioteca Central',
                'category' => 'Servicios',
                'category_color' => 'bg-violet-100 text-violet-700',
                'phone' => '+503 2511-2027',
                'email' => 'biblioteca.central@ues.edu.sv',
                'image' => asset('images/contacts/biblioteca.jpg'),
                'description' => 'Préstamos, salas, recursos digitales y constancias.',
                'more' => "Horario ref.: Lun–Vie 08:00–18:00.\n¿En qué ayudan?: Préstamo de materiales, salas de estudio, e-resources (EBSCO, eLibro, etc.), constancias."
            ],
            [
                'name' => 'Secretaría de Asuntos Académicos (SAA) – Certificaciones',
                'category' => 'Académico',
                'category_color' => 'bg-amber-100 text-amber-700',
                'phone' => '2511-2000 ext. 3017',
                'email' => 'saa.certificaciones@ues.edu.sv',
                'image' => asset('images/contacts/saa.jpg'),
                'description' => 'Certificaciones, graduaciones y reposición de título.',
                'more' => "Correos directos:\n• Graduaciones: graduaciones.saa@ues.edu.sv\n• Certificaciones: saa.certificaciones@ues.edu.sv\n• Reposición de título: reposiciones.saa@ues.edu.sv\n¿En qué ayudan?: Emisión de certificaciones, trámites de graduación y reposición de título."
            ],

            // FMO (San Miguel) – 5 contactos
            [
                'name' => 'FMO – Facultad Multidisciplinaria Oriental (Sede San Miguel)',
                'category' => 'FMO',
                'category_color' => 'bg-red-100 text-red-700',
                'phone' => '+503 2667-3702',
                'email' => 'posgrados.investigaciones@ues.edu.sv',
                'image' => asset('images/contacts/fmo.jpg'),
                'description' => 'Sede UES en Oriente: grado/posgrado e investigación.',
                'more' => "Dirección: Km 144 Carretera al Cuco, Cantón El Jute, San Miguel.\nTel. alterno: 2668-9203.\n¿En qué ayudan?: Información de carreras en FMO, posgrados, investigación y atención académica."
            ],
            [
                'name' => 'FMO – Escuela de Posgrado y Educación Continua',
                'category' => 'FMO',
                'category_color' => 'bg-red-100 text-red-700',
                'phone' => '+503 2668-9203',
                'email' => 'escuelaposgrado.fmo@ues.edu.sv',
                'image' => asset('images/contacts/fmo-posgrado.jpg'),
                'description' => 'Maestrías y educación continua en la Región Oriental.',
                'more' => "WhatsApp ref.: 7599-2216.\n¿En qué ayudan?: Oferta de posgrados, inscripción, requisitos, aranceles y calendario académico."
            ],
            [
                'name' => 'FMO – Unidad de Comunicaciones',
                'category' => 'FMO',
                'category_color' => 'bg-red-100 text-red-700',
                'phone' => '+503 2667-8408',
                'email' => 'comunicafmo@ues.edu.sv',
                'image' => asset('images/contacts/fmo-comunicaciones.jpg'),
                'description' => 'Canales informativos y apoyo en difusión institucional.',
                'more' => "¿En qué ayudan?: Publicaciones oficiales, contactos de prensa, difusión de eventos y avisos a la comunidad FMO."
            ],
            [
                'name' => 'FMO – Unidad Bibliotecaria',
                'category' => 'FMO',
                'category_color' => 'bg-red-100 text-red-700',
                'phone' => '+503 2667-3721',
                'email' => '—',
                'image' => asset('images/contacts/fmo-biblioteca.jpg'),
                'description' => 'Servicios bibliotecarios en la sede de San Miguel.',
                'more' => "¿En qué ayudan?: Consulta y préstamo de materiales, salas de estudio y orientación bibliográfica."
            ],
            [
                'name' => 'FMO – Administración Académica',
                'category' => 'FMO',
                'category_color' => 'bg-red-100 text-red-700',
                'phone' => '+503 2667-3702',
                'email' => '—',
                'image' => asset('images/contacts/fmo-admin-academica.jpg'),
                'description' => 'Gestiones académicas de estudiantes y docentes.',
                'more' => "¿En qué ayudan?: Procesos académicos, horarios, coordinaciones de cátedra y apoyo a escuelas."
            ],
        ];
    }

    public function render()
    {
        return view('livewire.site.contacto', [
            'contacts' => $this->contacts,
        ])->extends('layouts.site')
          ->section('content');
    }
}
