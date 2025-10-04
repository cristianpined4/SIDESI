<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OfertasDeEmpleo extends Model
{
    protected $table = 'ofertas_de_empleos';
    protected $fillable = ['title', 'description', 'location', 'company_name', 'contact_email', 'contact_phone', 'is_active', 'salary', 'vacancies', 'posted_by', 'application_deadline'];
}
