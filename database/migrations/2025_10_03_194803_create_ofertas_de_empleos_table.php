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
        Schema::create('ofertas_de_empleos', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->longText('description');
            $table->string('location');
            $table->string('company_name');
            $table->string('contact_email');
            $table->string('contact_phone');
            $table->boolean('is_active')->default(false);
            $table->decimal('salary', 10, 2)->nullable();
            $table->integer('vacancies')->default(1);
            $table->unsignedBigInteger('posted_by');
            $table->foreign('posted_by')->references('id')->on('users')->onDelete('cascade');
            $table->dateTime('application_deadline')->nullable();
            $table->index(['is_active'], 'idx_ofertas_publicadas');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ofertas_de_empleos');
    }
};
