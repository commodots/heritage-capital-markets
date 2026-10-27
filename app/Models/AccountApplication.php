<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccountApplication extends Model
{
    protected $fillable = [
        'reference', 'account_type', 'applicant_name', 'email', 'phone',
        'status', 'current_step', 'form_data', 'documents', 'submitted_at',
        'review_notes',
    ];

    protected $casts = [
        'form_data' => 'array',
        'documents' => 'array',
        'submitted_at' => 'datetime',
    ];
}
