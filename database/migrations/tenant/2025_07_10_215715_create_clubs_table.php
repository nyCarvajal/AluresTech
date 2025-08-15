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
        Schema::create('clubs', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('nombre', 300);
            $table->integer('pos')->nullable();
            $table->integer('cuentaCobro')->nullable();
            $table->integer('electronica')->nullable();
            $table->longText('terminos')->nullable();
            $table->string('color', 100)->nullable();
            $table->string('msj_bienvenida', 500)->nullable();
            $table->string('msj_reserva_confirmada', 500)->nullable();
			$table->string('msj_finalizado', 500)->nullable();
            $table->string('nit', 100)->nullable();
            $table->string('direccion', 300)->nullable();
            $table->integer('municipio')->nullable();
            $table->dateTime('updated_at')->nullable();
            $table->dateTime('created_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clubs');
    }
};
