<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Mtleads extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'mtleads';
    
    protected $fillable = [
        'provider_id',
        'name',
        'email',
        'phone',
        'childage',
        'requested_date',
        'created_date',
        'ip_address',
        'server_uri',
        'user_agent',
        'leadtype',
        'address',
        'zipcode',
        'city',
        'message',
        'intime',
        'outtime',
        'conversion',
        'reconciled_date',
    ];
}
