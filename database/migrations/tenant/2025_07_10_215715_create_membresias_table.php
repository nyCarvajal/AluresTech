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
        Schema::create('membresias', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('nombre', 200)->nullable();
            $table->string('descripcion', 400)->nullable();
            $table->integer('clases')->nullable();
            $table->integer('reservas')->nullable();
            $table->dateTime('updated_at')->nullable();
            $table->dateTime('created_at')->nullable();
            $table->decimal('valor', 10, 0)->nullable();
            $table->integer('item')->nullable()->index('item');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('membresias');
    }
};
