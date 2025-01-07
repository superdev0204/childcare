<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Momtrusted extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'momtrusted';
    
    protected $fillable = [
        'ccus_id',
        'name',
        'address',
        'city',
        'state',
        'zip',
        'county',
        'lat',
        'lng',
        'momtrusted_id',
    ];
}
