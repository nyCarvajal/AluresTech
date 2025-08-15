<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('reservas', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('cancha_id')->nullable()->index('cancha2');
            $table->dateTime('fecha')->nullable();
            $table->integer('duracion')->nullable();
            $table->integer('alumno_id')->nullable()->index('alumno');
            $table->string('estado', 200)->nullable();
            $table->integer('entrenador_id')->nullable()->index('entrenador1');
            $table->dateTime('updated_at')->nullable();
            $table->dateTime('created_at')->nullable();
            $table->string('tipo', 100)->nullable();
            $table->integer('responsable_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservas');
    }
};
