<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;


class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Juan',
            'lastname' => 'Pérez',
            'username' => 'juanp',
            'email' => 'juan.perez@example.com',
            'password' => bcrypt('password123'),
            'is_active' => true,
            'role_id' => 1,
            'document_type' => 'DUI',
            'document_number' => '01234567-8',
            'phone' => '7777-1111',
            'institution' => 'UES',
            'metadata' => '{}',
        ]);

        User::create([
            'name' => 'María',
            'lastname' => 'González',
            'username' => 'mariag',
            'email' => 'maria.gonzalez@example.com',
            'password' => bcrypt('password123'),
            'is_active' => true,
            'role_id' => 2,
            'document_type' => 'DUI',
            'document_number' => '87654321-0',
            'phone' => '7777-2222',
            'institution' => 'UES',
            'metadata' => '{}',
        ]);

        User::create([
            'name' => 'Carlos',
            'lastname' => 'Ramírez',
            'username' => 'carlosr',
            'email' => 'carlos.ramirez@example.com',
            'password' => bcrypt('password123'),
            'is_active' => true,
            'role_id' => 3,
            'document_type' => 'DUI',
            'document_number' => '12345678-9',
            'phone' => '7777-3333',
            'institution' => 'UES',
            'metadata' => '{}',
        ]);
    }
}
