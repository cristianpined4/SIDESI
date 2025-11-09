<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Eventos extends Model
{
    protected $table = 'eventos';
    protected $fillable = ['title', 'description', 'start_time', 'end_time', 'location', 'inscriptions_enabled', 'max_participants', 'contact_email', 'contact_phone', 'is_active', 'mode', 'is_paid', 'price', 'organizer_id'];

    protected $appends = [
        'main_image',
    ];

    public function imagenes()
    {
        return $this->hasMany('App\Models\Imagenes', 'related_id')
            ->where('related_table', $this->getTable());
    }

    public function getMainImageAttribute()
    {
        return $this->imagenes()->where('is_main', true)->first()->url ?? null;
    }
    
    /**
     * Obtiene las sesiones asociadas a este evento.
     */
    public function sesiones()
    {
        return $this->hasMany('App\Models\SessionesEvento', 'evento_id');
    }
    
    /**
     * Obtiene las inscripciones asociadas a este evento.
     */
    public function inscripciones()
    {
        return $this->hasMany('App\Models\InscripcionesEvento', 'evento_id');
    }
    
    /**
     * Obtiene los usuarios inscritos a este evento.
     */
    public function usuariosInscritos()
    {
        return $this->belongsToMany('App\Models\User', 'inscripciones_eventos', 'evento_id', 'user_id')
            ->withPivot('status', 'approved_at')
            ->withTimestamps();
    }
}