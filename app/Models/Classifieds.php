<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Classifieds extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'classifieds';
    
    protected $fillable = [
        'summary',
        'detail',
        'name',
        'email',
        'phone',
        'city',
        'state',
        'zip',
        'approved',
        'pricing',
        'additionalInfo',
        'created',
        'email_verified',
        'user_id',
    ];
}
