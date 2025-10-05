<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogsSistema extends Model
{
    protected $table = 'logs_sistemas';
    protected $fillable = ['action', 'user_id', 'ip_address', 'description', 'target_table', 'target_id', 'status'];
}
