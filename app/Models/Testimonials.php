<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Testimonials extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'testimonials';
    
    protected $fillable = [
        'comments',
        'approved',
        'email_verified',
        'name',
        'location',
        'date',
        'email',
        'pros',
        'cons',
        'suggestion',
    ];
}
