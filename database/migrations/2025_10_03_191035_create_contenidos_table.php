<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('contenidos', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('slug');
            $table->text('description');
            $table->enum('content_type', ['Noticia', 'Convocatoria', 'Información', 'Evento', 'Otro']);
            $table->unsignedBigInteger('autor_id')->nullable();
            $table->foreign('autor_id')->references('id')->on('users')->onDelete('set null');
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            $table->timestamps();
            $table->index(['content_type', 'status'], 'idx_contenidos_tipo_pub');
        });

        Schema::create('contenido_cuerpo', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('contenido_id');
            $table->foreign('contenido_id')->references('id')->on('contenidos')->onDelete('cascade');
            $table->binary('body');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contenido_cuerpo');
        Schema::dropIfExists('contenidos');
    }
};
