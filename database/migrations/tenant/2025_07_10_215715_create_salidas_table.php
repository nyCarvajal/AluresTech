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
        Schema::create('salidas', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('concepto', 300)->nullable();
            $table->dateTime('fecha')->nullable();
            $table->integer('cuenta_bancaria')->nullable();
            $table->decimal('valor', 10, 0)->nullable();
            $table->integer('cuenta_contable')->nullable();
            $table->string('observaciones', 500)->nullable();
            $table->integer('responsable')->nullable();
            $table->dateTime('updated_at')->nullable();
            $table->dateTime('created_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salidas');
    }
};
