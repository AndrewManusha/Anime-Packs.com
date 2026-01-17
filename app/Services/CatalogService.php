<?php

namespace App\Services;

use App\Models\Pack;
use Illuminate\Support\Facades\Auth;

class CatalogService
{
    /**
     * Получить данные для каталога
     */
    public function getCatalogData($param1 = null, $param2 = null, $param3 = null, $param4 = null)
    {
        // Получаем все нужные поля сразу
        $data = Pack::distinct()->get(['type', 'franchise', 'category']);
        
        // Формируем массивы уникальных значений
        $validSections = $data->pluck('type')->unique()->values()->toArray();
        $validFranchises = $data->pluck('franchise')->unique()->values()->toArray();
        $validCategories = $data->pluck('category')
            ->flatMap(function ($cat) {
                return array_map('trim', explode(',', $cat));
            })
            ->unique()
            ->values()
            ->toArray();

        // Инициализируем переменные фильтров
        $filters = [
            'section' => null,
            'franchise' => null,
            'category' => null,
            'page' => 1,
            'search' => null,
        ];

        // Разбор параметров запроса
        $params = [$param1, $param2, $param3, $param4];
        foreach ($params as $param) {
            if ($param) {
                if (preg_match('/^page-(\d+)$/', $param, $matches)) {
                    $filters['page'] = (int)$matches[1];
                } elseif (preg_match('/^category-([^ ]+)$/', $param, $matches)) {
                    $filters['category'] = $matches[1];
                } elseif (in_array($param, $validFranchises)) {
                    $filters['franchise'] = $param;
                } elseif (in_array($param, $validSections)) {
                    $filters['section'] = $param;
                } elseif (preg_match('/^search:([^ ]+)$/', $param, $matches)) {
                    $filters['search'] = $matches[1];
                }
            }
        }

        // Строим базовый запрос с LEFT JOIN
        $query = Pack::query()
            ->withAnalytics()
            ->orderBy('packs.updated_at', 'desc');

        // Применяем фильтры
        if ($filters['section']) {
            $query->where('packs.type', $filters['section']);
        }
        
        if ($filters['franchise']) {
            $query->where('packs.franchise', $filters['franchise']);
        }
        
        if ($filters['category']) {
            $categories = explode('-', $filters['category']);
            $query->where(function ($q) use ($categories) {
                foreach ($categories as $cat) {
                    $q->orWhere('packs.category', 'LIKE', '%' . $cat . '%');
                }
            });
        }
        
        if ($filters['search']) {
            $searchTerms = str_replace('-', ' ', $filters['search']);
            $searchArray = explode(' ', $searchTerms);
            foreach ($searchArray as $term) {
                $query->where(function ($q) use ($term) {
                    $q->where('packs.title', 'LIKE', '%' . $term . '%')
                      ->orWhere('packs.min_desc', 'LIKE', '%' . $term . '%');
                });
            }
        }
        
        if (!Auth::check() || !Auth::user()->hasRole('admin')) {
            $query->where('packs.status', 'posted');
        }

        // Пагинация
        $items = $query->paginate(15, ['*'], 'page', $filters['page']);
        $lastPage = $items->lastPage();

        // Проверка на существование страницы
        if ($filters['page'] < 1 || $filters['page'] > $lastPage) {
            return ['error' => 404];
        }
        
        $category = array_filter(explode('-', $filters['category']));

        // Возвращаем данные для представления
        return [
            'items' => $items,
            'franchise' => $filters['franchise'],
            'category' => $category,
            'page' => $filters['page'],
            'search' => $filters['search'],
            'section' => $filters['section'],
            'franchises' => $validFranchises,
            'categories' => $validCategories,
        ];
    }
}
