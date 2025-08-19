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
        Schema::create('membresia_cliente', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('membresia_id')->index('membresiaad');
            $table->integer('cliente_id')->index('cliente1');
            $table->integer('estado')->nullable()->default(1);
            $table->integer('tipo')->nullable();
            $table->integer('clases')->nullable();
            $table->integer('reservas')->nullable();
            $table->integer('clasesVistas')->default(0);
            $table->integer('numReservas')->default(0);
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('membresia_cliente');
    }
};
