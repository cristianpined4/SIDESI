<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Contenidos extends Model
{
    protected $table = 'contenidos';

    protected $fillable = [
        'title', 'slug', 'description', 'content_type', 'autor_id', 'status'
    ];

    protected $appends = ['main_image', 'category', 'category_label', 'date', 'details'];

    // Relaciones
    public function autor()
    {
        return $this->belongsTo(User::class, 'autor_id', 'id');
    }

    public function contenidoCuerpo()
    {
        return $this->hasOne(ContenidoCuerpo::class, 'contenido_id', 'id');
    }

    public function imagenes()
    {
        return $this->hasMany(Imagenes::class, 'related_id')
            ->where('related_table', $this->getTable());
    }

    // Imagen principal
    public function getMainImageAttribute()
    {
        return $this->imagenes()->where('is_main', true)->first()?->path ?? null;
    }

    // Categoría para frontend
    public function getCategoryAttribute()
    {
        return match($this->content_type) {
            'Evento' => 'evento',
            'Convocatoria' => 'empleo',
            'Noticia' => 'noticia',
            'Información' => 'info',
            default => 'otro',
        };
    }

    // Etiqueta legible
    public function getCategoryLabelAttribute()
    {
        return match($this->content_type) {
            'Evento' => 'Evento',
            'Convocatoria' => 'Convocatoria',
            'Noticia' => 'Noticia',
            'Información' => 'Información',
            default => 'Otro',
        };
    }

    // Fecha legible
    public function getDateAttribute()
    {
        return $this->created_at->format('j \d\e F, Y');
    }

    // Detalles desde contenido_cuerpo (binario → string)
    public function getDetailsAttribute()
    {
        if ($this->contenidoCuerpo && !empty($this->contenidoCuerpo->body)) {
            return DB::connection()->getPdo()->quote($this->contenidoCuerpo->body) 
                ? rtrim(ltrim(pack('H*', bin2hex($this->contenidoCuerpo->body)), "'"), "'")
                : (is_string($this->contenidoCuerpo->body) ? $this->contenidoCuerpo->body : '');
        }
        return $this->description; // fallback
    }
}