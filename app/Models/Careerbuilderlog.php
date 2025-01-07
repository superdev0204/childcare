<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Careerbuilderlog extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'careerbuilderlog';

    protected $fillable = [
        'key',
        'type',
        'source',
        'request',
        'date',
    ];
}
