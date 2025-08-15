<?php declare(strict_types=1);

use App\Models\Tenant;
use Stancl\Tenancy\Database\Models\Domain;
use Stancl\Tenancy\Bootstrappers\CacheTenancyBootstrapper;
use Stancl\Tenancy\Bootstrappers\DatabaseTenancyBootstrapper;
use Stancl\Tenancy\Bootstrappers\FilesystemTenancyBootstrapper;
use Stancl\Tenancy\Bootstrappers\QueueTenancyBootstrapper;
use Stancl\Tenancy\Bootstrappers\SubdomainTenantIdentificationBootstrapper;
use Stancl\Tenancy\Bootstrappers\UniversalRoutesBootstrapper;
use Stancl\Tenancy\Bootstrappers\UserImpersonationBootstrapper;
use Stancl\Tenancy\Bootstrappers\ViteBundlerBootstrapper;

return [


    /*
    |--------------------------------------------------------------------------
    | Tenant & Domain Models
    |--------------------------------------------------------------------------
    |
    | Aquí le dices al paquete qué modelos usar para tenants y dominios.
    |
    */

    'tenant_model' => Tenant::class,

    'domain_model' => Domain::class,

    /*
    |--------------------------------------------------------------------------
    | Central Domains
    |--------------------------------------------------------------------------
    |
    | Dominios donde corre tu aplicación “central” (sin contexto tenant).
    |
    */

    'central_domains' => [
        env('APP_URL'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Tenancy Bootstrappers
    |--------------------------------------------------------------------------
    |
    | Cada uno de estos “bootstrappers” se ejecuta cuando Stancl inicializa
    | un tenant, y por ejemplo DatabaseTenancyBootstrapper es el que inyecta
    | el nombre de tu BD de tenant en la conexión `tenant`.
    |
    */

    'bootstrappers' => [
	DatabaseTenancyBootstrapper::class,
        SubdomainTenantIdentificationBootstrapper::class,
        CacheTenancyBootstrapper::class,
        FilesystemTenancyBootstrapper::class,
        QueueTenancyBootstrapper::class,
        UniversalRoutesBootstrapper::class,
        UserImpersonationBootstrapper::class,
        ViteBundlerBootstrapper::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Database Connections
    |--------------------------------------------------------------------------
    |
    | Define aquí tu conexión “landlord” (común) y la conexión “tenant”
    | que será reconfigurada en runtime por DatabaseTenancyBootstrapper.
    |
    */

    'database' => [
        'central_connection'       => env('TENANCY_LANDLORD_CONNECTION', 'mysql'),
        'tenant_connection'        => env('TENANCY_TENANT_CONNECTION',  'tenant'),
        'template_tenant_connection' => null,

        // (Opcional) si quieres prefijos o sufijos automáticos para nuevas bases:
        'prefix' => '',
        'suffix' => '',

        // Clases encargadas de CREATE/DROP si usas la creación automática.
        'managers' => [
            'sqlite' => Stancl\Tenancy\TenantDatabaseManagers\SQLiteDatabaseManager::class,
            'mysql'  => Stancl\Tenancy\TenantDatabaseManagers\MySQLDatabaseManager::class,
            'pgsql'  => Stancl\Tenancy\TenantDatabaseManagers\PostgreSQLDatabaseManager::class,
        ],
    ],


    /*
    |--------------------------------------------------------------------------
    | Tenant Migrations
    |--------------------------------------------------------------------------
    |
    | Estas opciones se pasan a `php artisan tenants:migrate` para que sepa
    | por dónde buscar tus migraciones de tenant y en qué conexión correrlas.
    |
    */

    'migration_parameters' => [
        '--database' => env('TENANCY_TENANT_CONNECTION', 'tenant'),
        '--path'     => database_path('migrations/tenant'),
        '--realpath' => true,
        '--force'    => true,
    ],
	
	'storage' => [
    'drivers' => [
        'database' => [
            'data_column'    => 'database',
            'custom_columns' => [],    // aquí: qué otros atributos NO van a JSON
            'connection'     => null,
            'table_names'    => [
                'tenants'        => 'tenants',
                'tenant_domains' => 'tenant_domains',
            ],
        ],
    ],
],

];
