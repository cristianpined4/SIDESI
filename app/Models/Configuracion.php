<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Configuracion extends Model
{
    use HasFactory;

    protected $table = 'configuraciones';

    protected $fillable = [
        'user_id',
        'key',
        'value',
        'data',
        'name',
        'group',
        'type',
        'is_active',
        'main_image',
        'display_name',
        'avatar_path',
        'phone',
        'alt_email',
        'timezone',
        'language',
        'notify_email',
        'notify_push',
        'show_email',
        'show_phone',
        'two_factor_enabled',
        'security_questions',
        'social_links',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_active'           => 'boolean',
        'notify_email'        => 'boolean',
        'notify_push'         => 'boolean',
        'show_email'          => 'boolean',
        'show_phone'          => 'boolean',
        'two_factor_enabled'  => 'boolean',
        'data'                => 'array',
        'security_questions'  => 'array',
        'social_links'        => 'array',
    ];

    // Relaciones útiles
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
