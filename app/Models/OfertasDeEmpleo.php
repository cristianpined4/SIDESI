<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OfertasDeEmpleo extends Model
{
    protected $table = 'ofertas_de_empleos';
    protected $fillable = ['title', 'description', 'location', 'company_name', 'contact_email', 'contact_phone', 'is_active', 'salary', 'vacancies', 'posted_by', 'application_deadline'];

    protected $casts = [
        'is_active' => 'boolean',
        'salary' => 'decimal:2',
        'application_deadline' => 'datetime',
    ];

    protected $appends = ['main_image'];

    public function imagenes()
    {
        return $this->hasMany(Imagenes::class, 'related_id')
            ->where('related_table', $this->getTable());
    }

    public function getMainImageAttribute()
    {
        return $this->imagenes()->where('is_main', true)->first()?->url ?? null;
    }
}
