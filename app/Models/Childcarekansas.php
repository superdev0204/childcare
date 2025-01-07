<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Childcarekansas extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'childcare_kansas';

    protected $fillable = [
        'name',
        'address',
        'city',
        'state',
        'zip',
        'county',
        'phone',
        'type',
        'website',
        'email',
        'introduction',
    ];
}
