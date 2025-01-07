<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Emails extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'email';
    
    protected $fillable = [
        'from_email',
        'from_name',
        'from_userid',
        'to_email',
        'to_name',
        'to_userid',
        'type',
        'subject',
        'message',
        'created',
        'previous_id',
        'readdate',
        'responsedate',
        'ip_sent',
    ];
}
