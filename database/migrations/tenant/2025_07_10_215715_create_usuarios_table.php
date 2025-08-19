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
        Schema::create('usuarios', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('nombre', 100);
            $table->integer('peluqueria_id');
            $table->integer('role');
            $table->string('email', 200)->nullable();
            $table->string('apellidos', 200)->nullable();
            $table->integer('nivel')->nullable();
            $table->string('tipo_identificacion', 100)->nullable()->default('CC');
            $table->string('numero_identificacion', 100)->nullable();
            $table->string('direccion', 200)->nullable();
            $table->string('whatsapp', 100)->nullable();
            $table->integer('ciudad')->nullable();
            $table->string('password', 200)->nullable();
            $table->dateTime('updated_at')->nullable();
            $table->dateTime('created_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('usuarios');
    }
};
