<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Iptracker extends Model
{
    use HasFactory;

    public $timestamps = false;
    protected $table = 'iptracker';
    
    protected $fillable = [
        'ip',
        'hour',
        'current_count',
        'total_count',
        'ludate',
        'user_agent',
        'zip_count',        
    ];
}
