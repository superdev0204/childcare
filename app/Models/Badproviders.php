<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Badproviders extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'badproviders';

    protected $fillable = [
        'license_type',
        'revoke_type',
        'name',
        'location',
        'state',
        'zip',
        'county',
    ];
}
