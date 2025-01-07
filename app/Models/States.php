<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class States extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'states';

    protected $fillable = [
        'state_code',
        'state_name',
        'statefile',
        'center_count',
        'homebase_count',
        'nanny_count',
        'latitude',
        'longitude',
        'country',
        'coords',
        'nextlevel',
        'jobs_count',
        'agency',
        'introduction',
        'headstart_count',
        'coords_small',
        'coords_medium',
    ];
}
