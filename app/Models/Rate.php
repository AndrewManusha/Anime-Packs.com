<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class rate extends Model
{
    use HasFactory;
    
    protected $table='rates';
    protected $fillable=['google_id', 'page_url', 'rate', 'difference', 'is_new', 'updated_at'];
    public $timestamps = false;
}
