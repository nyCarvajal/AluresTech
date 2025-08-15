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
        Schema::create('proveedores', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('tipo_documento_id');
            $table->string('numero_documento', 100)->nullable();
            $table->string('nombre', 300)->nullable();
            $table->integer('regimen')->nullable();
            $table->integer('responsable_iva')->nullable();
            $table->string('direccion', 400)->nullable();
            $table->integer('municipio_id')->nullable();
            $table->dateTime('created_at')->nullable();
            $table->dateTime('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proveedores');
    }
};
