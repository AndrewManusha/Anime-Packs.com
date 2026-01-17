<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PageAnalyticsDayly extends Model
{
    use HasFactory;
    
    protected $table = 'page_analytics_dayly';
    
    protected $fillable = [
        'page_url', 
        'views', 
        'downloads'
    ];

    public $timestamps = false;
}
