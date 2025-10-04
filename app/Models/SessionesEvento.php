<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SessionesEvento extends Model
{
    protected $table = 'sessiones_evento';
    protected $fillable = ['evento_id', 'title', 'description', 'start_time', 'end_time', 'location', 'mode', 'max_participants', 'require_approval', 'ponente_id'];
}
