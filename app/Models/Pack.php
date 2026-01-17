<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pack extends Model
{
    use HasFactory;
    
    protected $table = 'packs';
    protected $fillable = ['page_url', 'type', 'title', 'file', 'items', 'video', 'image', 'franchise', 'category', 'description', 'min_desc', 'created_at', 'updated_at', 'status']; // добавь все поля, которые реально используешь

    // Указываем, что primary key — это page_url
    protected $primaryKey = 'page_url';

    // Он не автоинкрементный (строковый)
    public $incrementing = false;

    // Тип ключа — строка
    protected $keyType = 'string';

    /**
     * Scope для подключения аналитики и выбора необходимых полей
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string|null $franchiseForComparison Если передан, добавит поле same_franchise для сравнения
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithAnalytics($query, $franchiseForComparison = null)
    {
        $selectRaw = 'packs.*, 
            IFNULL(page_analytics_summary.views, 0) as views, 
            IFNULL(page_analytics_summary.downloads, 0) as downloads, 
            IFNULL(page_analytics_summary.avg_rating, 5) as rating';
        
        if ($franchiseForComparison) {
            $selectRaw .= ', CASE WHEN packs.franchise = ? THEN 1 ELSE 0 END as same_franchise';
        }

        $query = $query
            ->leftJoin('page_analytics_summary', 'packs.page_url', '=', 'page_analytics_summary.page_url');
        
        if ($franchiseForComparison) {
            $query = $query->selectRaw($selectRaw, [$franchiseForComparison]);
        } else {
            $query = $query->selectRaw($selectRaw);
        }
        
        return $query;
    }
    
}
