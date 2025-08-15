<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class BaseModel extends Model
{
    /**
     * Devuelve 'tenant' si hay usuario autenticado, 
     * o la conexión normal (p.ej. 'mysql') si no.
     */
    public function getConnectionName()
    {
        return Auth::check() ? 'tenant' : parent::getConnectionName();
    }
}
