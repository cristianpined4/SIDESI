<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Moderaciones extends Model
{
    protected $table = 'moderaciones';
    protected $fillable = ['user_id', 'related_table', 'related_id', 'reason', 'status'];
}
