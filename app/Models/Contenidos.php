<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contenidos extends Model
{
    protected $table = 'contenidos';
    protected $fillable = ['title', 'slug', 'description', 'content_type', 'autor_id', 'status'];

    protected $appends = [
        'main_image',
    ];

    public function autor()
    {
        return $this->belongsTo("App\Models\User", 'autor_id', 'id');
    }

    public function contenidoCuerpo()
    {
        return $this->hasMany("App\Models\ContenidoCuerpo", 'contenido_id', 'id');
    }

    public function imagenes()
    {
        return $this->hasMany('App\Models\Imagenes', 'related_id')
            ->where('related_table', $this->getTable());
    }

    public function getMainImageAttribute()
    {
        return $this->imagenes()->where('is_main', true)->first()->url ?? null;
    }
}