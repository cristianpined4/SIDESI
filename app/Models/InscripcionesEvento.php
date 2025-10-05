<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InscripcionesEvento extends Model
{
    protected $table = 'inscripciones_eventos';
    protected $fillable = ['evento_id', 'user_id', 'status', 'approved_by', 'approved_at', 'comprobante_codigo'];
}
