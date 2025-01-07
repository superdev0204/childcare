<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pages extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'pages';
    
    protected $fillable = [
        'pagename',
        'header',
        'body',
        'domain',
        'description',
        'created_date',
        'ludate',
        'visits',
        'approved',
        'main_category',
        'categories',
        'image_url',
    ];
}
