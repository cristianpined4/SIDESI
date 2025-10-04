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
        Schema::create('redes_sociales', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('related_id');
            $table->string('related_table');
            $table->string('platform');
            $table->string('url');
            $table->unsignedBigInteger('shared_by')->nullable();
            $table->foreign('shared_by')->references('id')->on('users')->onDelete('set null');
            $table->timestamps();
            $table->index(['related_id', 'related_table'], 'idx_redes_relacion');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('redes_sociales');
    }
};
