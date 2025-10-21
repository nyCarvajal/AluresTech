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
        Schema::create('role_labels', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('peluqueria_id');
            $table->unsignedInteger('role');
            $table->string('singular');
            $table->string('plural');
            $table->timestamps();

            $table->foreign('peluqueria_id')
                ->references('id')
                ->on('peluquerias')
                ->onDelete('cascade');

            $table->unique(['peluqueria_id', 'role']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('role_labels');
    }
};
