<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Peluqueria extends Model
{
	 protected $connection = 'mysql';
	 
    protected $fillable = [
        'nombre', 'pos', 'cuentaCobro', 'electronica',
        'terminos', 'color', 'msj_recordatorio', 'msj_bienvenida', 'msj_finalizado', 'msj_reserva_confirmada',
        'nit', 'direccion', 'municipio', 'db'
    ];
}
