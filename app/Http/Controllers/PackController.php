<?php

namespace App\Http\Controllers;

use App\Models\Pack;
use Illuminate\Http\Request;

class PackController extends Controller
{
    public function index(Request $request, $section, $franchise, $name = null, $action = null)
    {
        if ($name === $franchise) {
            abort(404);
        }
        
        if ($name === 'download') {
            $action = 'download';
            $name = null;
        }
        
        // Формируем page_url из параметров маршрута
        $pageUrl = "/{$section}/{$franchise}" . ($name ? "/{$name}" : "");

        // Используем модель Pack для выполнения запроса и сохраняем первый результат в $item
        $item = Pack::query()
            ->where('packs.page_url', $pageUrl)
            ->withAnalytics()
            ->first();

        // Проверяем, найден ли элемент, если нет, выдаем ошибку 404
        if (!$item) {
            abort(404, 'File not found');
        }
        
        $fileUrl = (basename($item->page_url) === $item->franchise) ? rtrim($item->page_url, '/') . '/' . $item->franchise : $item->page_url;
        
        $userId = auth()->check() ? auth()->user()->google_id : null;

        // Если в URL есть "download", передаем в соответствующий шаблон
        if ($action == 'download') {
            return view('download', ['item' => $item, 'userId' => $userId, 'fileUrl' => $fileUrl]);
        } else {
            
            $franchiseCount = Pack::where('franchise', $item->franchise)->count();
            
            $recommendations = Pack::query()
                ->where('packs.page_url', '!=', $item->page_url)
                ->where('packs.status', 'posted')
                ->where(function($query) use ($item, $franchiseCount) {
                    if ($franchiseCount >= 6) {
                        $query->where('packs.franchise', $item->franchise);
                    } else {
                        $categories = explode(' | ', $item->category);
                        $query->where(function($q) use ($categories) {
                            foreach ($categories as $category) {
                                $q->orWhereRaw('FIND_IN_SET(?, packs.category)', [$category]);
                            }
                        });
                    }
                })
                ->withAnalytics($item->franchise)
                ->orderByDesc('same_franchise')
                ->orderByDesc('downloads')
                ->limit(6)
                ->get();
            
            //названия предметов для переименования у франшизного пака берутся из всех паков этой франшизы
            $includes = null;
            if (!$name) {
                $includes = Pack::query()
                        ->select(['items'])
                        ->where('franchise', $item->franchise)
                        ->where('status', 'posted')
                        ->whereNotNull('items')
                        ->where('items', '!=', '')
                        ->orderBy('updated_at', 'desc')
                        ->pluck('items') // получим только значения поля item
                        ->map(function ($item) {
                            return is_array($item) ? $item : json_decode($item, true);
                        })
                        ->flatten(1) // объединит все массивы в один
                        ->values()
                        ->all(); // получим обычный массив
            }
            
            $item['items'] = json_decode($item['items'], true);
            
            return view('pack', ['item' => $item, 'name' => $name, 'userId' => $userId, 'fileUrl' => $fileUrl, 'recommendations' => $recommendations, 'includes' => $includes ?? null]);
        }
    }
}
