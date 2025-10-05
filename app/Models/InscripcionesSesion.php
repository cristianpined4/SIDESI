<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InscripcionesSesion extends Model
{
    protected $table = 'inscripciones_sesions';
    protected $fillable = ['session_id', 'user_id', 'status', 'approved_by', 'approved_at'];
}
