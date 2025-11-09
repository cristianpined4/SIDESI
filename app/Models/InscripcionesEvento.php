<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InscripcionesEvento extends Model
{
    protected $table = 'inscripciones_eventos';
    protected $fillable = ['evento_id', 'user_id', 'status', 'approved_by', 'approved_at', 'comprobante_codigo'];
    
    protected $dates = [
        'approved_at',
        'created_at',
        'updated_at'
    ];
    
    /**
     * Obtiene el evento relacionado con la inscripción.
     */
    public function evento(): BelongsTo
    {
        return $this->belongsTo(Eventos::class, 'evento_id');
    }
    
    /**
     * Obtiene el usuario relacionado con la inscripción.
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
    /**
     * Obtiene el administrador que aprobó la inscripción.
     */
    public function aprobador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
