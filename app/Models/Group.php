<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Group extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'group';
    
    protected $fillable = [
        'name',
        'description',
        'logo',
        'contact_name',
        'contact_email',
        'website',
    ];
}
