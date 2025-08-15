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
        Schema::create('items', function (Blueprint $table) {
            $table->integer('id', true);
            $table->string('nombre', 300);
            $table->integer('cantidad')->nullable();
            $table->decimal('valor', 10, 0)->nullable();
            $table->integer('tipo')->nullable();
            $table->dateTime('updated_at')->nullable();
            $table->dateTime('create_at')->nullable();
            $table->integer('area')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
