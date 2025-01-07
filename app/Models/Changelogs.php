<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Changelogs extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'changelogs';

    protected $fillable = [
        'table',
        'table_id',
        'fieldname',
        'oldvalue',
        'newvalue',
        'ip_address',
        'ludate',
        'approved',        
    ];
}
