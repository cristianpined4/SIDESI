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
        Schema::create('pagos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedBigInteger('evento_id')->nullable();
            $table->foreign('evento_id')->references('id')->on('eventos')->onDelete('set null');
            $table->decimal('amount', 10, 2);
            $table->string('currency')->default('USD');
            $table->string('payment_method');
            $table->enum('status', ['pendiente', 'completado', 'fallido', 'reembolsado', 'pendiente_confirmacion', 'rechazado'])->default('pendiente');
            $table->string('transaction_id')->nullable();
            $table->dateTime('paid_at')->nullable();
            $table->timestamps();
            $table->index(['status', 'user_id'], 'idx_pagos_estado_usuario');
            $table->index(['evento_id', 'status'], 'idx_pagos_evento_estado');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pagos');
    }
};