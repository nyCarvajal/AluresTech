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
            if (! Schema::hasColumn('peluquerias', 'trainer_label_singular')) {
                $table->string('trainer_label_singular')->nullable();
            }

            if (! Schema::hasColumn('peluquerias', 'trainer_label_plural')) {
                $table->string('trainer_label_plural')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('peluquerias', function (Blueprint $table) {
            if (Schema::hasColumn('peluquerias', 'trainer_label_plural')) {
                $table->dropColumn('trainer_label_plural');
            }

            if (Schema::hasColumn('peluquerias', 'trainer_label_singular')) {
                $table->dropColumn('trainer_label_singular');
            }
        });
    }
};
