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
        Schema::table('membresia_cliente', function (Blueprint $table) {
            $table->foreign(['membresia_id'], 'membresiaAd')->references(['id'])->on('membresias')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('membresia_cliente', function (Blueprint $table) {
            $table->dropForeign('membresiaAd');
        });
    }
};
