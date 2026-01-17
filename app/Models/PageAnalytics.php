<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PageAnalytics extends Model
{
    use HasFactory;
    
    protected $table='page_analytics';
    protected $fillable=['user_identifier', 'page_url', 'action', 'action_date', 'time'];
    public $timestamps = false;
}
