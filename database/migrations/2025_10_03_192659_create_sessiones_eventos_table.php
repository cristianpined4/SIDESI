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
        Schema::create('sessiones_eventos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('evento_id');
            $table->foreign('evento_id')->references('id')->on('eventos')->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->dateTime('start_time');
            $table->dateTime('end_time')->nullable();
            $table->unsignedBigInteger('ponente_id')->nullable();
            $table->foreign('ponente_id')->references('id')->on('users')->onDelete('set null');
            $table->enum('mode', ['taller', 'ponencia', 'panel', 'otro'])->default('otro');
            $table->integer('max_participants')->nullable();
            $table->boolean('require_approval')->default(false);
            $table->index(['start_time', 'end_time', 'mode'], 'idx_sesiones_evento_fecha_tipo');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sessiones_eventos');
    }
};
