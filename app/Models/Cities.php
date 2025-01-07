<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cities extends Model
{
    use HasFactory;
    
    public $timestamps = false;
    protected $table = 'cities';

    protected $fillable = [
        'city',
        'county',
        'state',
        'latitude',
        'longitude',
        'center_count',
        'homebase_count',
        'filename',
        'statefile',
        'center_visits',
        'homebase_visits',
        'countyfile',
        'jobs_count',
        'afterschool_count',
        'provider_ids',
    ];
}
