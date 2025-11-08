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

        User::create([
            'name' => 'María',
            'lastname' => 'López',
            'username' => 'marial',
            'email' => 'maria.lopez@example.com',
            'password' => bcrypt('password123'),
            'is_active' => true,
            'role_id' => 2,
            'document_type' => 'DUI',
            'document_number' => '04561234-5',
            'phone' => '7000-1111',
            'institution' => 'UES',
            'metadata' => '{}',
        ]);

    User::create([
        'name' => 'José',
        'lastname' => 'Martínez',
        'username' => 'jmartinez',
        'email' => 'jose.martinez@example.com',
        'password' => bcrypt('password123'),
        'is_active' => true,
        'role_id' => 3,
        'document_type' => 'DUI',
        'document_number' => '11223344-6',
        'phone' => '7222-9988',
        'institution' => 'UES',
        'metadata' => '{}',
    ]);

    User::create([
        'name' => 'Ana',
        'lastname' => 'González',
        'username' => 'anagonz',
        'email' => 'ana.gonzalez@example.com',
        'password' => bcrypt('password123'),
        'is_active' => true,
        'role_id' => 1,
        'document_type' => 'DUI',
        'document_number' => '99887766-5',
        'phone' => '7555-2211',
        'institution' => 'UES',
        'metadata' => '{}',
    ]);

    User::create([
        'name' => 'Luis',
        'lastname' => 'Hernández',
        'username' => 'lhernandez',
        'email' => 'luis.hernandez@example.com',
        'password' => bcrypt('password123'),
        'is_active' => true,
        'role_id' => 2,
        'document_type' => 'DUI',
        'document_number' => '33445566-7',
        'phone' => '7444-3322',
        'institution' => 'UES',
        'metadata' => '{}',
    ]);

    User::create([
        'name' => 'Sofía',
        'lastname' => 'Castro',
        'username' => 'sofiac',
        'email' => 'sofia.castro@example.com',
        'password' => bcrypt('password123'),
        'is_active' => true,
        'role_id' => 3,
        'document_type' => 'DUI',
        'document_number' => '55667788-9',
        'phone' => '7333-4422',
        'institution' => 'UES',
        'metadata' => '{}',
    ]);

    User::create([
        'name' => 'Miguel',
        'lastname' => 'Torres',
        'username' => 'mtorres',
        'email' => 'miguel.torres@example.com',
        'password' => bcrypt('password123'),
        'is_active' => true,
        'role_id' => 2,
        'document_type' => 'DUI',
        'document_number' => '22334455-1',
        'phone' => '7666-1100',
        'institution' => 'UES',
        'metadata' => '{}',
    ]);

    User::create([
        'name' => 'Elena',
        'lastname' => 'Rivas',
        'username' => 'elenar',
        'email' => 'elena.rivas@example.com',
        'password' => bcrypt('password123'),
        'is_active' => true,
        'role_id' => 1,
        'document_type' => 'DUI',
        'document_number' => '66778899-3',
        'phone' => '7999-5522',
        'institution' => 'UES',
        'metadata' => '{}',
    ]);

    User::create([
        'name' => 'Javier',
        'lastname' => 'Pérez',
        'username' => 'javierp',
        'email' => 'javier.perez@example.com',
        'password' => bcrypt('password123'),
        'is_active' => true,
        'role_id' => 3,
        'document_type' => 'DUI',
        'document_number' => '77889900-4',
        'phone' => '7123-9876',
        'institution' => 'UES',
        'metadata' => '{}',
    ]);

    User::create([
        'name' => 'Lucía',
        'lastname' => 'Flores',
        'username' => 'luciaf',
        'email' => 'lucia.flores@example.com',
        'password' => bcrypt('password123'),
        'is_active' => true,
        'role_id' => 2,
        'document_type' => 'DUI',
        'document_number' => '88990011-2',
        'phone' => '7011-2345',
        'institution' => 'UES',
        'metadata' => '{}',
    ]);

    User::create([
        'name' => 'Ricardo',
        'lastname' => 'Santos',
        'username' => 'rsantos',
        'email' => 'ricardo.santos@example.com',
        'password' => bcrypt('password123'),
        'is_active' => true,
        'role_id' => 1,
        'document_type' => 'DUI',
        'document_number' => '99001122-3',
        'phone' => '7888-4567',
        'institution' => 'UES',
        'metadata' => '{}',
    ]);

    }
}
