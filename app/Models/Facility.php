<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Facility extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'facility';
    
    protected $fillable = [
        'state_id',
        'name',
        'address',
        'address2',
        'city',
        'state',
        'zip',
        'phone',
        'operation_id',
        'county',
        'email',
        'created_date',
        'capacity',
        'status',
        'type',
        'contact_firstname',
        'contact_lastname',
        'age_range',
        'transportation',
        'is_center',
        'lat',
        'lng',
        'filename',
        'visits',
        'ludate',
        'state_rating',
        'website',
        'subsidized',
        'accreditation',
        'cityfile',
        'headstart',
        'approved',
        'daysopen',
        'hoursopen',
        'typeofcare',
        'language',
        'introduction',
        'additionalInfo',
        'pricing',
        'ranking',
        'user_id',
        'is_preschool',
        'is_religious',
        'is_afterschool',
        'is_montessori',
        'pending_logid',
        'gmap_heading',
        'logo',
        'image_statuscode',
        'district_office',
        'do_phone',
        'schools_served',
        'licensor',
        'location_url',
        'facebook_url',
        'career_url',
        'application_url',
        'parent_handbook_url',
        'is_infant',
        'is_toddler',
        'is_prek',
        'is_camp',
        'group_id',
        'is_featured',
        'momtrusted_id',
        'avg_rating',
        'highlight',
    ];

    public function detail(): BelongsTo{
        return $this->belongsTo(Facilitydetail::class, 'id', 'facility_id');
    }

    public function operationHours(): BelongsTo{
        return $this->belongsTo(Facilityhours::class, 'id', 'facility_id');
    }

    public function user(): BelongsTo{
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
