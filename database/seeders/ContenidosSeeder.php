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
        $contenidos = [
            // Eventos
            [
                'title' => 'Hackathon de Innovación Tecnológica 2025',
                'description' => 'Participa en el hackathon más grande del año y desarrolla soluciones innovadoras para problemas reales.',
                'content_type' => 'Evento',
                'body' => 'Este evento reúne a los mejores talentos en tecnología para resolver desafíos actuales de la industria. Incluye mentorías, talleres técnicos y premios en efectivo.',
            ],
            [
                'title' => 'Conferencia Internacional de Inteligencia Artificial',
                'description' => 'Únete a expertos mundiales en IA para discutir las últimas tendencias y avances en aprendizaje automático.',
                'content_type' => 'Evento',
                'body' => 'Evento internacional que cuenta con la participación de investigadores líderes en IA. Incluye presentaciones de casos de estudio, demostraciones en vivo y oportunidades de networking.',
            ],
            [
                'title' => 'Workshop de Desarrollo Web Full Stack',
                'description' => 'Aprende a crear aplicaciones web modernas desde cero utilizando las tecnologías más demandadas del mercado.',
                'content_type' => 'Evento', // o 'Información' si prefieres
                'body' => 'Taller práctico intensivo donde aprenderás las tecnologías más modernas para desarrollo web. Incluye proyectos reales, código en vivo y certificación al finalizar.',
            ],

            // Convocatorias (empleo, becas)
            [
                'title' => 'Bolsa de Empleo: Oportunidades para Egresados',
                'description' => 'Nuevas ofertas laborales disponibles para recién graduados y profesionales con experiencia en el sector tecnológico.',
                'content_type' => 'Convocatoria',
                'body' => 'Conectamos a nuestros egresados con las mejores empresas del sector tecnológico. Incluye procesos de reclutamiento acelerado y sesiones de networking exclusivas.',
            ],
            [
                'title' => 'Programa de Becas para Estudios de Posgrado',
                'description' => 'Convocatoria abierta para solicitar becas completas en maestrías y doctorados en universidades internacionales.',
                'content_type' => 'Convocatoria',
                'body' => 'Programa exclusivo que ofrece becas completas para estudios de posgrado en universidades de prestigio mundial. Incluye asesoramiento personalizado para el proceso de aplicación.',
            ],

            // Información (egresados, anuncios generales)
            [
                'title' => 'Encuentro Anual de Egresados de Ingeniería NEW',
                'description' => 'Reunión anual para fortalecer la red profesional y compartir experiencias entre egresados de ingeniería.',
                'content_type' => 'Información',
                'body' => 'Evento exclusivo para egresados donde se realizarán charlas, mesas de discusión y actividades de networking. Ideal para ampliar contactos profesionales.',
            ],
        ];

        foreach ($contenidos as $data) {
            $contenidoId = DB::table('contenidos')->insertGetId([
                'title' => $data['title'],
                'slug' => Str::slug($data['title']),
                'description' => $data['description'],
                'content_type' => $data['content_type'], // ✅ Ahora todos son válidos
                'autor_id' => null,
                'status' => 'published',
                'created_at' => Carbon::now()->subDays(rand(1, 30)),
                'updated_at' => now(),
            ]);

            DB::table('contenido_cuerpo')->insert([
                'contenido_id' => $contenidoId,
                'body' => $data['body'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('✅ 6 contenidos insertados con content_type válido.');
    }
}