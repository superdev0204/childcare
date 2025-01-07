<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Zipcodes extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'zipcodes';

    protected $fillable = [
        'zipcode',
        'lat',
        'lng',
        'city',
        'state',
        'county',
        'type',
        'center_count',
        'homebase_count',
        'cityfile',
        'countyfile',
        'statefile',
        'ludate',
        'referalResources',
        'provider_ids',
    ];
}
