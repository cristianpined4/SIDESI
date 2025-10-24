<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('configuraciones', function (Blueprint $table) {
            $table->id();

            // Relación con el usuario dueño de la configuración (perfil)
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->onDelete('cascade');

            // Clave única por usuario (ej: "perfil.nombre_mostrar", "ui.tema", "seguridad.2fa_habilitado")
            $table->string('key', 120);

            // Valor genérico (texto) y/o valor estructurado (jsonb en PostgreSQL)
            $table->text('value')->nullable();
            $table->json('data')->nullable(); // en PostgreSQL será jsonb

            // Metadatos
            $table->string('name', 255)->nullable();        // nombre legible de la config
            $table->string('group', 100)->nullable();       // ej: perfil, ui, seguridad, notificaciones
            $table->string('type', 50)->nullable();         // string|number|boolean|json|file|html
            $table->boolean('is_active')->default(true);
            $table->string('main_image', 512)->nullable();  // URL/Path de imagen (avatar, banner, logo)

            // “Perfil de usuario” — campos prácticos listos para usar
            $table->string('display_name', 120)->nullable();
            $table->string('avatar_path', 512)->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('alt_email', 255)->nullable();
            $table->string('timezone', 60)->nullable()->default('America/El_Salvador');
            $table->string('language', 10)->nullable()->default('es');

            // Preferencias y privacidad (útil y extensible)
            $table->boolean('notify_email')->default(true);
            $table->boolean('notify_push')->default(false);
            $table->boolean('show_email')->default(false);
            $table->boolean('show_phone')->default(false);

            // Seguridad (algo extra que se me ocurre)
            $table->boolean('two_factor_enabled')->default(false);
            $table->json('security_questions')->nullable();

            // Redes sociales / enlaces (jsonb)
            $table->json('social_links')->nullable(); // { "facebook": "...", "github": "...", ... }

            // Auditoría básica (si quieres luego llenarlos desde policies/middleware)
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();

            // Un usuario no debería tener dos veces la misma key
            $table->unique(['user_id', 'key']);
            $table->index(['user_id', 'group']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('configuraciones');
    }
};