<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;

class CreateClubDatabase extends Command
{
    protected $signature = 'club:create 
                            {name : Nombre del club (ej: "Academia Padel")} 
                            {--migrate : Ejecutar migraciones tras crearla}';

    protected $description = 'Crea la base de datos de un club y, opcionalmente, corre sus migraciones.';

    public function handle()
    {
        $name = $this->argument('name');
        // Generamos un nombre de BD seguro:
        $dbName = 'club_' . Str::slug($name, '_');

        // 1) Creamos la base de datos:
        $this->info("Creando base de datos `$dbName` …");
        DB::statement("CREATE DATABASE IF NOT EXISTS `$dbName` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        $this->info("Base `$dbName` creada o ya existía.");

        // 2) Reconfiguramos la conexión tenant
        $this->info("Configurando conexión tenant para usar `$dbName` …");
        config(['database.connections.tenant.database' => $dbName]);
        DB::purge('tenant');
        DB::reconnect('tenant');

        // 3) (Opcional) ejecutamos migraciones
        if ($this->option('migrate')) {
            $this->info("Ejecutando migraciones en `$dbName` …");
            Artisan::call('migrate', [
                '--database' => 'tenant',
                '--path'     => 'database/migrations/tenant',
                '--force'    => true,
            ], $this->getOutput());
            $this->info("Migraciones finalizadas.");
        }

        $this->info("✅ Club `$name` provisoned: database=$dbName");
    }
}
