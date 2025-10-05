<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Eventos extends Model
{
    protected $table = 'eventos';
    protected $fillable = ['title', 'description', 'start_time', 'end_time', 'location', 'inscriptions_enabled', 'max_participants', 'contact_email', 'contact_phone', 'is_active', 'mode', 'is_paid', 'price', 'organizer_id'];
}
