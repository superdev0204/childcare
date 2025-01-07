<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class News extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'news';
    
    protected $fillable = [
        'provider_id',
        'title',
        'shortdesc',
        'url',
        'created_date',
    ];
}
