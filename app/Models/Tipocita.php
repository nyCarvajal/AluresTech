<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tipocita extends Model
{
    protected $connection = 'tenant';
    protected $table = 'tipocita';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
    ];
}

