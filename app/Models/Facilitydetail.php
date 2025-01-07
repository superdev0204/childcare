<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Facilitydetail extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'facilitydetail';
    
    protected $fillable = [
        'facility_id',
        'group_id',
        'group_extid',
        'phone2',
        'fax',
        'location_url',
        'facebook_url',
        'career_url',
        'application_url',
        'parent_handbook_url',
        'initial_application_date',
        'current_license_begin_date',
        'current_license_expiration_date',
        'childcare_food_program',
        'momtrusted_phone',
        'mailing_address',
        'mailing_address2',
        'mailing_city',
        'mailing_state',
        'mailing_zip',
        'gmap_pano_id',
        'license_holder',
        'license_holder_id',
        'license_holder_onsite',
    ];
}
