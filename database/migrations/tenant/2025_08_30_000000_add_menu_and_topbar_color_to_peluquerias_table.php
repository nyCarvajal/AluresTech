<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('peluquerias', function (Blueprint $table) {
            if (! Schema::hasColumn('peluquerias', 'menu_color')) {
                $table->string('menu_color', 20)->nullable()->after('color');
            }

            if (! Schema::hasColumn('peluquerias', 'topbar_color')) {
                $table->string('topbar_color', 20)->nullable()->after('menu_color');
            }
        });
    }

    public function down(): void
    {
        Schema::table('peluquerias', function (Blueprint $table) {
            if (Schema::hasColumn('peluquerias', 'topbar_color')) {
                $table->dropColumn('topbar_color');
            }

            if (Schema::hasColumn('peluquerias', 'menu_color')) {
                $table->dropColumn('menu_color');
            }
        });
    }
};
