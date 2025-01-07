<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Resume extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'resume';
    
    protected $fillable = [
        'name',
        'position',
        'objective',
        'rate_range',
        'city',
        'state',
        'zip',
        'phone',
        'email',
        'experience',
        'skillsCertification',
        'created',
        'approved',
        'educationLevel',
        'school',
        'major',
        'additionalInfo',
        'email_verified',
    ];
}
