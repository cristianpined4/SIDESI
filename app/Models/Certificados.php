<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Certificados extends Model
{
    protected $table = 'certificados';
    protected $fillable = ['user_id', 'evento_id', 'emitido_en', 'url', 'codigo_qr', 'is_valid'];
}
