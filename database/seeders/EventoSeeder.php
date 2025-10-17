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
    }
}
