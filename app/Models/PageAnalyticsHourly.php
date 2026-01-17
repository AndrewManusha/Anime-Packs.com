<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PageAnalyticsHourly extends Model
{
    use HasFactory;
    
    protected $table = 'page_analytics_hourly';
    
    protected $fillable = [
        'page_url', 
        'views', 
        'downloads'
    ];

    public $timestamps = false;
}
