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
        Schema::create('historial_cuenta', function (Blueprint $table) {
            $table->integer('id', true);
            $table->dateTime('fecha_hora');
            $table->integer('cuenta')->index('cuenta4');
            $table->string('descripcion', 200);
            $table->integer('responsable')->index('usuario6');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historial_cuenta');
    }
};
