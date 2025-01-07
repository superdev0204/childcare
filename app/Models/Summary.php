<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Summary extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'summary';
    
    protected $fillable = [
        'center_count',
        'homebase_count',
        'top_cities',
        'feature_daycares',
    ];
}
