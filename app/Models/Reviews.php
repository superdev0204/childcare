<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reviews extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'reviews';
    
    protected $fillable = [
        'facility_id',
        'email',
        'review_date',
        'review_by',
        'experience',
        'rating',
        'comments',
        'ip_address',
        'owner_comment',
        'owner_comment_date',
        'owner_comment_approved',
        'helpful',
        'nohelp',
        'facility_filename',
        'facility_name',
        'approved',
        'email_verified',
    ];

    public function provider(): BelongsTo{
        return $this->belongsTo(Facility::class, 'facility_id', 'id');
    }
}
