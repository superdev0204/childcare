<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Images extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'images';
    
    protected $fillable = [
        'provider_id',
        'type',
        'created',
        'imagename',
        'altname',
        'approved',
        'image_url',
        'path',
    ];

    public function provider(): BelongsTo{
        return $this->belongsTo(Facility::class, 'provider_id', 'id');
    }
}
