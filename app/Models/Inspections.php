<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inspections extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'inspections';
    
    protected $fillable = [
        'facility_id',
        'report_url',
        'report_date',
        'report_status',
        'report_type',
        'pages',
        'complaint_date',
        'rule_description',
        'current_status',
        'status_date',
        'provider_response',
        'state',
        'inserted',
        'updated',
    ];
}
