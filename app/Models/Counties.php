<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Counties extends Model
{
    use HasFactory;
    
    public $timestamps = false;
    protected $table = 'counties';

    protected $fillable = [
        'county',
        'state',
        'county_file',
        'center_count',
        'homebase_count',
        'statefile',
        'referalResources',
        'provider_ids',
        'district_office',
        'do_address',
        'do_address2',
        'do_phone',
        'do_website',
    ];
}
