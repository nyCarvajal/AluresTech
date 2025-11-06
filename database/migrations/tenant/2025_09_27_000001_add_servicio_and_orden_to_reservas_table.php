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
        Schema::table('reservas', function (Blueprint $table) {
            if (! Schema::hasColumn('reservas', 'servicio_id')) {
                $table->integer('servicio_id')->nullable()->after('cliente_id');
            }

            if (! Schema::hasColumn('reservas', 'orden_id')) {
                $table->integer('orden_id')->nullable()->after('servicio_id');
            }

            if (! Schema::hasColumn('reservas', 'venta_id')) {
                $table->integer('venta_id')->nullable()->after('orden_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservas', function (Blueprint $table) {
            if (Schema::hasColumn('reservas', 'venta_id')) {
                $table->dropColumn('venta_id');
            }

            if (Schema::hasColumn('reservas', 'orden_id')) {
                $table->dropColumn('orden_id');
            }

            if (Schema::hasColumn('reservas', 'servicio_id')) {
                $table->dropColumn('servicio_id');
            }
        });
    }
};
