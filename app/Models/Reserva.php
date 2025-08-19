<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany; // <—— ESTE es el import correcto
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;


class Reserva extends Model
{
	   protected $connection = 'tenant';
	   public function resolveRouteBinding($value, $field = null)
    {
        // 1) Ajusta la conexión tenant según el usuario autenticado
        if ($user = Auth::user()) {
            $dbName = $user->peluqueria->db;                               // el nombre dynamic de la BD
            Config::set('database.connections.tenant.database', $dbName);
            DB::purge('tenant');
            DB::reconnect('tenant');
        }

        // 2) Resuelve el modelo usando esa conexión
        $field = $field ?: $this->getRouteKeyName();
        return $this->on('tenant')
                    ->where($field, $value)
                    ->firstOrFail();
    }
    protected $fillable = [
    'fecha',
    'end',
    'type',
    'cliente_id',
	'entrenador_id',
    'cancha_id',
    'estado',
	'duracion',
	'tipo',
	'repeat_enabled',
	'repeat_until',
	
    // …
];


 public function canchas()
    {
        // Asume que la tabla pivote se llama "reserva_cancha"
        // y que las claves foráneas son reserva_id y cancha_id.
        return $this->belongsToMany(Cancha::class, 'reserva_cancha', 'reserva_id', 'cancha_id');
    }

    /**
     * Relación uno-a-uno (o muchos-a-uno) con Entrenador (solo para tipo "Clase").
     */
    public function entrenador()
    {
        // Aquí asumo que tienes un modelo Entrenador que representa a los entrenadores.
        // Si tu modelo se llama Usuario con rol "entrenador", recuerda usar ese modelo aquí.
        return $this->belongsTo(User::class);
    }

 /**
     * Obtener el cliente responsable de la reserva.
     */
   public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    /**
     * Relación con Cancha.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function cancha()
    {
        return $this->belongsTo(Cancha::class, 'cancha_id');
    }
	
	 /**
     * Relación MANY-TO-MANY con Cliente a través de la tabla cliente_clase,
     * pero si sólo quieres enlazar Reserva ↔ Clientes, ajusta el nombre de la tabla pivot.
     */
    public function clientes(): BelongsToMany
    {
        // (aquí “cliente_reserva” es un ejemplo; úsalo solo si esa es tu tabla pivote)
        return $this->belongsToMany(
            Cliente::class,
            'clase_cliente', // o el nombre real de tu tabla pivote
            'reserva_id',     // FK de reservas en la pivote
            'cliente_id'       // FK de clientes en la pivote
        )->withTimestamps();
    }


}
