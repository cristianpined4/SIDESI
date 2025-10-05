<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Roles;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description');
            $table->text('permissions')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_external')->default(false);
            $table->timestamps();
        });

        $roles = [
            [
                'name' => 'Administrador',
                'description' => 'Administrador',
                'is_active' => true,
                'is_external' => false,
            ],
            [
                'name' => 'Directivo',
                'description' => 'Directivo',
                'is_active' => true,
                'is_external' => false,
            ],
            [
                'name' => 'Docente',
                'description' => 'Docente',
                'is_active' => true,
                'is_external' => false,
            ],
            [
                'name' => 'Estudiante',
                'description' => 'Estudiante',
                'is_active' => true,
                'is_external' => false,
            ],
            [
                'name' => 'Invitado',
                'description' => 'Invitado',
                'is_active' => true,
                'is_external' => true,
            ],
        ];

        if (!Roles::where('name', 'Administrador')->exists()) {
            Roles::insert($roles);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
