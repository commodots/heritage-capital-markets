<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    protected $table = 'media';

    protected $fillable = [
        'name',
        'file_path',
        'disk',
        'mime_type',
        'alt_text',
        'caption',
        'file_size',
    ];
}