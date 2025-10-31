<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogsSistema extends Model
{
    protected $table = 'logs_sistemas';

    protected $fillable = [
        'action',
        'user_id',
        'ip_address',
        'description',
        'target_table',
        'target_id',
        'status',
    ];

    protected $appends = ['display_name'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getDisplayNameAttribute(): string
    {
        if ($this->relationLoaded('user') && $this->user) {
            return ($this->user->name . ' ' . $this->user->lastname) ?? 'Sistema';
        }

        // Si no está cargada, intenta obtenerlo (sin fallar)
        $user = User::find($this->user_id);
        return ($user ? ($user->name . ' ' . $user->lastname) : 'Sistema');
    }
}