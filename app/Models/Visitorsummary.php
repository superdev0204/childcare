<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Visitorsummary extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'visitorsummary';

    protected $fillable = [
        'page_url',
        'date',
        'visitor_count'
    ];
}
