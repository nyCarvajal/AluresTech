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
        Schema::create('clientes', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('nombres', 200);
            $table->string('apellidos', 200)->nullable();
            $table->string('correo', 200)->nullable();
            $table->string('whatsapp', 200)->nullable();
            $table->string('tipo_identificacion', 100)->nullable()->default('CC');
            $table->string('numero_identificacion', 100)->nullable();
            $table->date('fecha_nacimiento')->nullable();
            $table->string('direccion', 200)->nullable();
            $table->integer('municipio')->nullable();
            $table->integer('departamento')->nullable();
            $table->integer('pais')->nullable();
            $table->string('sexo', 11)->nullable();
            $table->integer('tipo')->nullable();
            $table->dateTime('updated_at')->nullable();
            $table->dateTime('created_at')->nullable();
            $table->integer('nivel_id')->nullable()->index('nivel');
            $table->integer('power')->nullable()->default(0);
            $table->string('foto', 500)->nullable();
            $table->integer('activo')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};
