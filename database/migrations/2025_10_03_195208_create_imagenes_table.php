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
        Schema::create('imagenes', function (Blueprint $table) {
            $table->id();
            $table->string('related_table');
            $table->unsignedBigInteger('related_id');
            $table->text('url');
            $table->text('path');
            $table->string('alt_text')->nullable();
            $table->bigInteger('size')->nullable();
            $table->string('mime_type')->nullable();
            $table->boolean('is_main')->default(false);
            $table->timestamps();
            $table->index(['related_id'], 'idx_imagenes_relacion');
            $table->index(['related_id', 'is_main'], 'idx_imagenes_principal');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('imagenes');
    }
};
