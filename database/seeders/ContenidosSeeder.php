<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ContenidosSeeder extends Seeder
{
    public function run(): void
    {
        $autorId = 1; // si quieres dejarlo null, cambia a: null

        $contenidos = [
            // Evento
            [
                'title'        => 'Hackathon de Innovación Tecnológica 2025',
                'description'  => 'Participa en el hackathon más grande del año y desarrolla soluciones innovadoras para problemas reales.',
                'content_type' => 'Evento',
                'body'         => 'Este evento reúne a los mejores talentos en tecnología para resolver desafíos actuales. Habrá mentorías, talleres y premios.',
            ],
            // Evento
            [
                'title'        => 'Conferencia Internacional de Inteligencia Artificial',
                'description'  => 'Únete a expertos mundiales en IA para discutir las últimas tendencias y avances en aprendizaje automático.',
                'content_type' => 'Evento',
                'body'         => 'Investigadores líderes presentarán casos de estudio, demostraciones y oportunidades de networking.',
            ],
            // Evento (o Información)
            [
                'title'        => 'Workshop de Desarrollo Web Full Stack',
                'description'  => 'Aprende a crear aplicaciones web modernas desde cero con tecnologías demandadas.',
                'content_type' => 'Evento',
                'body'         => 'Taller intensivo con proyectos reales, código en vivo y certificación al finalizar.',
            ],
            // Convocatoria
            [
                'title'        => 'Bolsa de Empleo: Oportunidades para Egresados',
                'description'  => 'Nuevas ofertas laborales disponibles para recién graduados y profesionales del sector tecnológico.',
                'content_type' => 'Convocatoria',
                'body'         => 'Conectamos egresados con empresas del sector. Habrá reclutamiento acelerado y sesiones de networking.',
            ],
            // Convocatoria
            [
                'title'        => 'Programa de Becas para Posgrado 2025',
                'description'  => 'Convocatoria abierta para solicitar becas completas en maestrías y doctorados.',
                'content_type' => 'Convocatoria',
                'body'         => 'Becas completas en universidades de prestigio. Incluye asesoría para aplicar.',
            ],
            // Información
            [
                'title'        => 'Encuentro Anual de Egresados de Ingeniería',
                'description'  => 'Reunión anual para fortalecer la red profesional y compartir experiencias.',
                'content_type' => 'Información',
                'body'         => 'Charlas, mesas de discusión y networking. Ideal para ampliar contactos profesionales.',
            ],
            // Noticia
            [
                'title'        => 'Inauguración del nuevo laboratorio de redes',
                'description'  => 'Se inauguró un laboratorio con routers Cisco y switches gestionables.',
                'content_type' => 'Noticia',
                'body'         => 'El laboratorio permitirá prácticas avanzadas de redes y simulaciones de topologías reales.',
            ],
            // Otro
            [
                'title'        => 'Actualización del portal académico',
                'description'  => 'El portal recibió mejoras de rendimiento y accesibilidad.',
                'content_type' => 'Otro',
                'body'         => 'Se optimizaron tiempos de carga y se incorporaron nuevas herramientas para docentes y estudiantes.',
            ],
        ];

        foreach ($contenidos as $item) {
            $contenidoId = DB::table('contenidos')->insertGetId([
                'title'        => $item['title'],
                'slug'         => Str::slug($item['title']),
                'description'  => $item['description'],
                'content_type' => $item['content_type'], // Debe estar en ['Noticia','Convocatoria','Información','Evento','Otro']
                'autor_id'     => $autorId,             // o null
                'status'       => 'published',          // 'draft' | 'published' | 'archived'
                'created_at'   => Carbon::now()->subDays(rand(0, 30)),
                'updated_at'   => Carbon::now(),
            ]);

            DB::table('contenido_cuerpo')->insert([
                'contenido_id' => $contenidoId,
                'body'         => $item['body'], // tu migración usa binary; se almacenará como bytes del string
                'created_at'   => Carbon::now(),
                'updated_at'   => Carbon::now(),
            ]);
        }

        $this->command?->info('✅ ContenidosSeeder: contenidos y cuerpos insertados.');
    }
}
