<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SessionesEvento;
use Carbon\Carbon;

class SessionesEventoSeeder extends Seeder
{
    public function run(): void
    {
        $sessiones = [
            [
                'evento_id' => 1,
                'title' => 'Sesión de Apertura: Innovación y Tecnología',
                'description' => 'Introducción al evento, presentación de ponentes y objetivos de la jornada.',
                'start_time' => Carbon::create(2025, 11, 10, 9, 0, 0),
                'end_time' => Carbon::create(2025, 11, 10, 10, 30, 0),
                'location' => 'Auditorio Central UES',
                'mode' => 'ponencia',
                'max_participants' => 100,
                'require_approval' => false,
                'ponente_id' => 1,
            ],
            [
                'evento_id' => 1,
                'title' => 'Taller Práctico de Ciberseguridad',
                'description' => 'Sesión interactiva sobre técnicas de defensa en entornos empresariales.',
                'start_time' => Carbon::create(2025, 11, 10, 11, 0, 0),
                'end_time' => Carbon::create(2025, 11, 10, 12, 30, 0),
                'location' => 'Laboratorio de Redes 2',
                'mode' => 'taller',
                'max_participants' => 25,
                'require_approval' => true,
                'ponente_id' => 1,
            ],
            [
                'evento_id' => 1,
                'title' => 'Conferencia sobre IA Aplicada',
                'description' => 'Exploración del uso de la inteligencia artificial en procesos industriales.',
                'start_time' => Carbon::create(2025, 12, 5, 8, 30, 0),
                'end_time' => Carbon::create(2025, 12, 5, 10, 0, 0),
                'location' => 'Centro de Convenciones UES - Sala 3',
                'mode' => 'ponencia',
                'max_participants' => 200,
                'require_approval' => false,
                'ponente_id' => 1,
            ],
            [
                'evento_id' => 1,
                'title' => 'Mesa Redonda de Emprendimiento',
                'description' => 'Conversatorio con invitados sobre cómo lanzar startups tecnológicas en El Salvador.',
                'start_time' => Carbon::create(2025, 10, 13, 2, 30, 0),
                'end_time' => Carbon::create(2025, 10, 13, 4, 0, 0),
                'location' => 'MI CASA (Evento en línea)',
                'mode' => 'taller',
                'max_participants' => 50,
                'require_approval' => false,
                'ponente_id' => 1,
            ],
        ];

        foreach ($sessiones as $sesion) {
            SessionesEvento::create($sesion);
        }
    }
}
