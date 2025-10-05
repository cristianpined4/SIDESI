<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Imagenes extends Model
{
    protected $table = 'imagenes';
    protected $fillable = ['related_table', 'related_id', 'url', 'path', 'alt_text', 'size', 'mime_type', 'is_main'];
}
