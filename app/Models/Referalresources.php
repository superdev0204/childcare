<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Referalresources extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'referalresources';
    
    protected $fillable = [
        'name',
        'address',
        'city',
        'state',
        'zip',
        'phone',
        'tollfree',
        'fax',
        'email',
        'website',
        'contact_name',
        'details',
        'services_offered',
        'county_area_served',
        'logo_url',
        'operation_hour',
        'lat',
        'lng',
        'ludate',
        'created',
        'approved',
    ];
}
