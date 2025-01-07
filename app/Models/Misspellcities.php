<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Misspellcities extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'misspellcities';
    
    protected $fillable = [
        'city',
        'state',
        'county',
        'prov_count',
        'correct_city',
        'correct_county',
        'cityfile',
        'updated',
    ];
}
