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

    public function isDownloadableDocument(): bool
    {
        return ($this->disk ?: 'public') === 'public'
            && in_array(
                strtolower(pathinfo($this->file_path, PATHINFO_EXTENSION)),
                ['pdf', 'doc', 'docx'],
                true,
            );
    }
}
