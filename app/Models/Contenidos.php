<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contenidos extends Model
{
    protected $table = 'contenidos';
    protected $fillable = ['title', 'slug', 'description', 'content_type', 'autor_id', 'status'];

    public function autor()
    {
        return $this->belongsTo("App\Models\User", 'autor_id', 'id');
    }

    public function contenidoCuerpo()
    {
        return $this->hasMany("App\Models\ContenidoCuerpo", 'contenido_id', 'id');
    }

}
