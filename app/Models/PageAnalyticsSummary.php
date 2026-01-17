<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PageAnalyticsSummary extends Model
{
    use HasFactory;
    
    protected $table = 'page_analytics_summary';
    
    protected $fillable = [
        'page_url', 
        'views', 
        'downloads'
    ];

    public $timestamps = false;
    // Указываем, что первичный ключ — это page_url
    protected $primaryKey = 'page_url';

    // Отключаем авто-инкрементирование, так как это не необходимо
    public $incrementing = false;

    // Указываем тип данных для ключа
    protected $keyType = 'string'; // если `page_url` строковый тип
}
