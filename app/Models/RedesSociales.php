<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RedesSociales extends Model
{
    protected $table = 'redes_sociales';
    protected $fillable = ['related_id', 'related_table', 'platform', 'url', 'shared_by'];
}
