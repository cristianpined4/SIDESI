<?php

namespace App\Livewire\Site;

use Livewire\Component;

class ContactoController extends Component
{
    public $contacts = [
        [
            'id' => 1,
            'category' => 'Administrativo',
            'category_color' => 'bg-green-100 text-green-700',
            'name' => 'Unidad de Recursos Humanos',
            'description' => 'Encargada de la gestión del personal docente y administrativo, así como de los procesos de contratación y desarrollo laboral.',
            'email' => 'recursoshumanos@ues.edu.sv',
            'phone' => '2221-7456',
            'image' => 'https://images.unsplash.com/photo-1521737604893-d14cc237f11d?auto=format&fit=crop&w=800&q=80',
        ],
        [
            'id' => 2,
            'category' => 'Administrativo',
            'category_color' => 'bg-green-100 text-green-700',
            'name' => 'Secretaría de Posgrados',
            'description' => 'Gestión de programas de maestrías, especialidades y doctorados.',
            'email' => 'secretaria.posgrado@ues.edu.sv',
            'phone' => '2222-9834',
            'image' => 'https://images.unsplash.com/photo-1557804506-669a67965ba0?auto=format&fit=crop&w=800&q=80',
        ],
        [
            'id' => 3,
            'category' => 'Comunicaciones',
            'category_color' => 'bg-indigo-100 text-indigo-700',
            'name' => 'Secretaría de Comunicaciones',
            'description' => 'Encargada de la difusión institucional y relaciones públicas.',
            'email' => 'secretaria.comunicaciones@ues.edu.sv',
            'phone' => '2265-4102',
            'image' => 'https://images.unsplash.com/photo-1593642532973-d31b6557fa68?auto=format&fit=crop&w=800&q=80',
        ],
    ];

    public function render()
    {
        return view('livewire.site.contacto', [
            'contacts' => $this->contacts
        ])
            ->extends('layouts.site')
            ->section('content');
    }
}
