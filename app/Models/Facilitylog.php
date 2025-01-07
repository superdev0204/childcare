<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Facilitylog extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'facilitylog';
    
    protected $fillable = [
        'provider_id',
        'name',
        'email',
        'address',
        'address2',
        'city',
        'state',
        'zip',
        'phone',
        'operation_id',
        'capacity',
        'age_range',
        'transportation',
        'ludate',
        'website',
        'subsidized',
        'accreditation',
        'approved',
        'daysopen',
        'hoursopen',
        'typeofcare',
        'language',
        'introduction',
        'additionalInfo',
        'pricing',
        'user_id',
        'schools_served',
    ];

    public function provider(): BelongsTo{
        return $this->belongsTo(Facility::class, 'provider_id');
    }

    public function user(): BelongsTo{
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function getEditableFields()
    {
        return [
            'name' => 'Name',
            'email' => 'Email',
            'address' => 'Address',
            'address2' => 'Address 2',
            'city' => 'City',
            'state' => 'State',
            'zip' => 'Zip',
            'phone' => 'Phone',
            'operation_id' => 'Operation ID',
            'capacity' => 'Capacity',
            'age_range' => 'Age Range',
            'transportation' => 'Transportation',
            'website' => 'Website',
            'subsidized' => 'Subsidized',
            'accreditation' => 'Accreditation',
            'daysopen' => 'Days Open',
            'hoursopen' => 'Hours Open',
            'typeofcare' => 'Type Of Care',
            'language' => 'Language',
            'introduction' => 'Introduction',
            'additionalInfo' => 'Additional Info',
            'pricing' => 'Pricing',
            'schools_served' => 'Schools Served',
        ];
    }
}
