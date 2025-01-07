<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Error extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'error';

    protected $fillable = [
        'host',
        'url',
        'errortype',
        'ip',
        'user_agent',
        'date',
        'referrer',
        'exception',
    ];
}
