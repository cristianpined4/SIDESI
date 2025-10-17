<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SessionesEvento extends Model
{
    protected $table = 'sessiones_eventos';
    protected $fillable = ['evento_id', 'title', 'description', 'start_time', 'end_time', 'location', 'mode', 'max_participants', 'require_approval', 'ponente_id'];

    protected $appends = [
        'main_image',
    ];

    public function ponente()
    {
        return $this->belongsTo('App\Models\User', 'ponente_id');
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