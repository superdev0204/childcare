<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Mtchildcare extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'mt_childcare';
    
    protected $fillable = [
        'name',
        'address',
        'city',
        'state',
        'zip',
        'county',
        'phone',
        'website',
        'Email',
    ];
}
