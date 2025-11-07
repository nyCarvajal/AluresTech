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
        Schema::table('peluquerias', function (Blueprint $table) {
            if (! Schema::hasColumn('peluquerias', 'email')) {
                $table->string('email', 191)->nullable()->after('direccion');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('peluquerias', function (Blueprint $table) {
            if (Schema::hasColumn('peluquerias', 'email')) {
                $table->dropColumn('email');
            }
        });
    }
};
