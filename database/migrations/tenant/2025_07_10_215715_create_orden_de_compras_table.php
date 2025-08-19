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
        Schema::create('orden_de_compras', function (Blueprint $table) {
            $table->integer('id', true);
            $table->dateTime('fecha_hora');
            $table->integer('responsable')->nullable()->index('usuario1');
            $table->integer('cliente')->nullable()->index('cliente6');
            $table->integer('activa')->default(1);
            $table->dateTime('updated_at')->nullable();
            $table->dateTime('created_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orden_de_compras');
    }
};
