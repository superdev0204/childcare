<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jobs extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'jobs';
    
    protected $fillable = [
        'title',
        'description',
        'state',
        'city',
        'zip',
        'education',
        'rate_range',
        'requirements',
        'phone',
        'email',
        'created',
        'approved',
        'email_verified',
        'company',
        'source',
        'ext_id',
        'employmentType',
        'jobServiceURL',
        'jobDetailsURL',
        'companyDetailsURL',
        'companyImageURL',
        'applyURL',
        'experienceRequired',
        'startDate',
        'endDate',
        'hasDetail',
        'user_id',
        'howtoapply',
        'updated',
    ];
}
