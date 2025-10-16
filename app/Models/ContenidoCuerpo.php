<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContenidoCuerpo extends Model
{
    protected $table = 'contenido_cuerpo';
    protected $fillable = ['contenido_id', 'body'];

    // Si body es binario, puedes intentar acceder como string
    protected $casts = [
        'body' => 'string', // Esto puede no funcionar si es binario puro
    ];
}