<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BoletinSuscriptores extends Model
{
    protected $table = 'boletin_suscriptores';
    protected $fillable = ['user_id', 'email', 'is_valid', 'token_confirmation'];
}
