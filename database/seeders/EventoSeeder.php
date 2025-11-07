<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Eventos;

class EventoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Eventos::create([
            'title' => 'Conferencia de Tecnología 2025',
            'description' => 'Evento sobre innovación y desarrollo tecnológico.',
            'start_time' => '2025-11-10 09:00:00',
            'end_time' => '2025-11-10 17:00:00',
            'location' => 'Auditorio Central UES',
            'inscriptions_enabled' => true,
            'max_participants' => 200,
            'contact_email' => 'info@evento.com',
            'contact_phone' => '7777-7777',
            'is_active' => true,
            'mode' => 'presencial',
            'is_paid' => false,
            'price' => 0,
            'organizer_id' => 1,
        ]);

        Eventos::create([
            'title' => 'Feria de Innovación Académica 2025',
            'description' => 'Exposición de proyectos estudiantiles y charlas sobre investigación aplicada.',
            'start_time' => '2025-12-05 08:30:00',
            'end_time' => '2025-12-05 16:30:00',
            'location' => 'Centro de Convenciones UES',
            'inscriptions_enabled' => true,
            'max_participants' => 500,
            'contact_email' => 'feria@ues.edu.sv',
            'contact_phone' => '7845-3321',
            'is_active' => true,
            'mode' => 'presencial',
            'is_paid' => true,
            'price' => 5.00,
            'organizer_id' => 1,
        ]);

        Eventos::create([
            'title' => 'Seminario Virtual sobre Energías Renovables',
            'description' => 'Seminario en línea enfocado en las últimas tendencias en energías limpias.',
            'start_time' => '2025-11-20 10:00:00',
            'end_time' => '2025-11-20 15:00:00',
            'location' => 'En línea (Zoom)',
            'inscriptions_enabled' => true,
            'max_participants' => 300,
            'contact_email' => 'seminario@evento.com',
            'contact_phone' => '7777-8888',
            'is_active' => true,
            'mode' => 'virtual',
            'is_paid' => false,
            'price' => 0,
            'organizer_id' => 1,
        ]);

        Eventos::create([
            'title' => 'Taller de Emprendimiento para Estudiantes',
            'description' => 'Taller práctico para fomentar habilidades emprendedoras entre los estudiantes universitarios.',
            'start_time' => '2025-12-15 09:00:00',
            'end_time' => '2025-12-15 13:00:00',
            'location' => 'Sala de Conferencias B, UES',
            'inscriptions_enabled' => true,
            'max_participants' => 100,
            'contact_email' => 'taller@evento.com',
            'contact_phone' => '7777-9999',
            'is_active' => true,
            'mode' => 'presencial',
            'is_paid' => true,
            'price' => 10.00,
            'organizer_id' => 1,
        ]);

        Eventos::create([
            'title' => 'Seminario de Ciberseguridad y Protección de Datos',
            'description' => 'Introducción a las buenas prácticas de seguridad informática para estudiantes y docentes.',
            'start_time' => '2025-11-20 14:00:00',
            'end_time' => '2025-11-20 17:00:00',
            'location' => 'Auditorio Central, UES',
            'inscriptions_enabled' => true,
            'max_participants' => 120,
            'contact_email' => 'seguridad@evento.com',
            'contact_phone' => '7111-2233',
            'is_active' => true,
            'mode' => 'presencial',
            'is_paid' => false,
            'price' => 0.00,
            'organizer_id' => 3,
        ]);

        Eventos::create([
            'title' => 'Conferencia: El Futuro de la Inteligencia Artificial',
            'description' => 'Charla sobre las tendencias actuales y futuras en IA y aprendizaje automático.',
            'start_time' => '2025-10-05 10:00:00',
            'end_time' => '2025-10-05 12:00:00',
            'location' => 'Sala Virtual Zoom',
            'inscriptions_enabled' => true,
            'max_participants' => 300,
            'contact_email' => 'ia@evento.com',
            'contact_phone' => '7222-3344',
            'is_active' => true,
            'mode' => 'virtual',
            'is_paid' => true,
            'price' => 5.00,
            'organizer_id' => 5,
        ]);

        Eventos::create([
            'title' => 'Feria de Innovación Estudiantil',
            'description' => 'Exposición de proyectos tecnológicos realizados por estudiantes de ingeniería.',
            'start_time' => '2025-09-12 08:00:00',
            'end_time' => '2025-09-12 15:00:00',
            'location' => 'Plaza Central, UES',
            'inscriptions_enabled' => false,
            'max_participants' => 500,
            'contact_email' => 'innovacion@evento.com',
            'contact_phone' => '7555-8899',
            'is_active' => true,
            'mode' => 'presencial',
            'is_paid' => false,
            'price' => 0.00,
            'organizer_id' => 1,
        ]);

        Eventos::create([
            'title' => 'Curso Básico de Python',
            'description' => 'Curso para aprender lógica de programación desde cero utilizando Python.',
            'start_time' => '2025-08-01 15:00:00',
            'end_time' => '2025-08-01 18:00:00',
            'location' => 'Laboratorio 3, Facultad de Ingeniería',
            'inscriptions_enabled' => true,
            'max_participants' => 35,
            'contact_email' => 'python@evento.com',
            'contact_phone' => '7666-3344',
            'is_active' => true,
            'mode' => 'presencial',
            'is_paid' => true,
            'price' => 8.00,
            'organizer_id' => 7,
        ]);

        Eventos::create([
            'title' => 'Webinar: Desarrollo de APIs con Laravel',
            'description' => 'Taller enfocado en la construcción de APIs REST con Laravel 11.',
            'start_time' => '2025-11-02 18:00:00',
            'end_time' => '2025-11-02 20:00:00',
            'location' => 'Google Meet',
            'inscriptions_enabled' => true,
            'max_participants' => 200,
            'contact_email' => 'laravel@evento.com',
            'contact_phone' => '7444-0001',
            'is_active' => true,
            'mode' => 'virtual',
            'is_paid' => false,
            'price' => 0.00,
            'organizer_id' => 9,
        ]);

        Eventos::create([
            'title' => 'Concurso de Resolución de Problemas Matemáticos',
            'description' => 'Competencia para fomentar el pensamiento lógico y analítico.',
            'start_time' => '2025-07-09 09:00:00',
            'end_time' => '2025-07-09 13:00:00',
            'location' => 'Aula Magna, UES',
            'inscriptions_enabled' => true,
            'max_participants' => 80,
            'contact_email' => 'matematicas@evento.com',
            'contact_phone' => '7999-5522',
            'is_active' => true,
            'mode' => 'presencial',
            'is_paid' => false,
            'price' => 0.00,
            'organizer_id' => 4,
        ]);

        Eventos::create([
            'title' => 'Taller de Photoshop y Edición Digital',
            'description' => 'Curso práctico de edición gráfica dirigido a principiantes.',
            'start_time' => '2025-11-22 14:30:00',
            'end_time' => '2025-11-22 17:30:00',
            'location' => 'Laboratorio Multimedia, UES',
            'inscriptions_enabled' => true,
            'max_participants' => 25,
            'contact_email' => 'diseno@evento.com',
            'contact_phone' => '7123-9988',
            'is_active' => true,
            'mode' => 'presencial',
            'is_paid' => true,
            'price' => 12.00,
            'organizer_id' => 2,
        ]);

        Eventos::create([
            'title' => 'Festival Cultural Universitario',
            'description' => 'Presentaciones artísticas y expresiones culturales estudiantiles.',
            'start_time' => '2025-10-10 13:00:00',
            'end_time' => '2025-10-10 20:00:00',
            'location' => 'Plaza Cultural, UES',
            'inscriptions_enabled' => false,
            'max_participants' => 1000,
            'contact_email' => 'cultura@evento.com',
            'contact_phone' => '7888-1111',
            'is_active' => true,
            'mode' => 'presencial',
            'is_paid' => false,
            'price' => 0.00,
            'organizer_id' => 6,
        ]);

        Eventos::create([
            'title' => 'Taller de Marketing Digital en Redes Sociales',
            'description' => 'Cómo mejorar alcance, interacción y posicionamiento en redes.',
            'start_time' => '2025-12-05 10:00:00',
            'end_time' => '2025-12-05 12:00:00',
            'location' => 'Google Meet',
            'inscriptions_enabled' => true,
            'max_participants' => 150,
            'contact_email' => 'marketing@evento.com',
            'contact_phone' => '7011-3344',
            'is_active' => true,
            'mode' => 'virtual',
            'is_paid' => true,
            'price' => 6.00,
            'organizer_id' => 8,
        ]);

        Eventos::create([
            'title' => 'Foro de Discusión: Ética Profesional en Ingeniería',
            'description' => 'Espacio de discusión sobre la responsabilidad ética en la práctica profesional.',
            'start_time' => '2025-09-18 16:00:00',
            'end_time' => '2025-09-18 18:00:00',
            'location' => 'Aula 204, Facultad de Ingeniería',
            'inscriptions_enabled' => true,
            'max_participants' => 60,
            'contact_email' => 'etica@evento.com',
            'contact_phone' => '7222-8765',
            'is_active' => true,
            'mode' => 'presencial',
            'is_paid' => false,
            'price' => 0.00,
            'organizer_id' => 10,
        ]);

    }
}
