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
        Schema::create('ventas', function (Blueprint $table) {
            $table->integer('id', true);
            $table->integer('cuenta')->index('cuenta');
            $table->integer('producto')->index('membresia2');
            $table->integer('cantidad')->default(1);
            $table->decimal('descuento', 10, 0)->default(0);
            $table->decimal('valor_unitario', 10, 0);
            $table->decimal('valor_total', 10, 0);
            $table->dateTime('updated_at')->nullable();
            $table->dateTime('created_at')->nullable();
			$table->integer('usuario_id')->index('usuarios');
			$table->decimal('valor_total_venta', 12, 2);
			$table->decimal('porcentaje_comision', 5, 2); // editable
			$table->decimal('comision', 12, 2);   
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ventas');
    }
};
