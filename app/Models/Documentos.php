<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Documentos extends Model
{
    protected $table = 'documentos';
    protected $fillable = ['user_id', 'name', 'description', 'path', 'type', 'is_valid', 'visibility'];
}
