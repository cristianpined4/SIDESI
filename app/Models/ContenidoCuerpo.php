<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContenidoCuerpo extends Model
{
    protected $table = 'contenido_cuerpo';
    protected $fillable = ['contenido_id', 'body'];
}
