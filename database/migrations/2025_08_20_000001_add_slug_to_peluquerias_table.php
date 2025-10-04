<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('peluquerias', function (Blueprint $table) {
            if (! Schema::hasColumn('peluquerias', 'slug')) {
                $table->string('slug')->nullable()->unique()->after('nombre');
            }
        });

        $peluquerias = DB::table('peluquerias')->select('id', 'nombre', 'slug')->get();

        foreach ($peluquerias as $peluqueria) {
            if (! empty($peluqueria->slug)) {
                continue;
            }

            $baseSlug = Str::slug($peluqueria->nombre ?: 'peluqueria-' . $peluqueria->id);
            $slug = $baseSlug;
            $counter = 1;

            while (DB::table('peluquerias')->where('slug', $slug)->exists()) {
                $slug = $baseSlug . '-' . $counter++;
            }

            DB::table('peluquerias')->where('id', $peluqueria->id)->update([
                'slug' => $slug,
            ]);
        }
    }

    public function down(): void
    {
        Schema::table('peluquerias', function (Blueprint $table) {
            if (Schema::hasColumn('peluquerias', 'slug')) {
                $table->dropUnique('peluquerias_slug_unique');
                $table->dropColumn('slug');
            }
        });
    }
};
