<?php

namespace App\Services;

use App\Models\Pack;
use Illuminate\Http\Request;

class PackService
{
    /**
     * Получить данные пака по параметрам маршрута
     *
     * @param string $section
     * @param string $franchise
     * @param string|null $name
     * @return Pack|null
     */
    public function getPackByUrl(string $section, string $franchise, ?string $name = null): ?Pack
    {
        $pageUrl = "/{$section}/{$franchise}" . ($name ? "/{$name}" : "");

        return Pack::query()
            ->where('packs.page_url', $pageUrl)
            ->withAnalytics()
            ->first();
    }

    /**
     * Получить рекомендации для пака
     *
     * @param Pack $item
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getRecommendations(Pack $item)
    {
        $franchiseCount = Pack::where('franchise', $item->franchise)->count();

        return Pack::query()
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
    }

    /**
     * Получить включаемые предметы для франшизного пака
     *
     * @param string $franchise
     * @return array|null
     */
    public function getIncludedItems(string $franchise): ?array
    {
        $includes = Pack::query()
            ->select(['items'])
            ->where('franchise', $franchise)
            ->where('status', 'posted')
            ->whereNotNull('items')
            ->where('items', '!=', '')
            ->orderBy('updated_at', 'desc')
            ->pluck('items')
            ->map(function ($item) {
                return is_array($item) ? $item : json_decode($item, true);
            })
            ->flatten(1)
            ->values()
            ->all();

        return count($includes) > 0 ? $includes : null;
    }

    /**
     * Получить URL файла для пака
     *
     * @param Pack $item
     * @return string
     */
    public function getFileUrl(Pack $item): string
    {
        return $item->fileUrl;
    }

    /**
     * Подготовить данные для отображения пака
     *
     * @param string $section
     * @param string $franchise
     * @param string|null $name
     * @param string|null $action
     * @return array
     */
    public function getPackPageData(string $section, string $franchise, ?string $name = null, ?string $action = null): array
    {
        if ($name === $franchise) {
            abort(404);
        }

        if ($name === 'download') {
            $action = 'download';
            $name = null;
        }

        $item = $this->getPackByUrl($section, $franchise, $name);

        if (!$item) {
            abort(404, 'File not found');
        }

        $fileUrl = $this->getFileUrl($item);
        $userId = auth()->check() ? auth()->user()->google_id : null;

        // Если в URL есть "download", возвращаем данные для скачивания
        if ($action == 'download') {
            return [
                'view' => 'download',
                'data' => [
                    'item' => $item,
                    'userId' => $userId,
                    'fileUrl' => $fileUrl
                ]
            ];
        }

        // Получаем рекомендации и включаемые предметы
        $recommendations = $this->getRecommendations($item);
        $includes = !$name ? $this->getIncludedItems($item->franchise) : null;

        // Декодируем JSON items
        $item['items'] = json_decode($item['items'], true);

        return [
            'view' => 'pack',
            'data' => [
                'item' => $item,
                'name' => $name,
                'userId' => $userId,
                'fileUrl' => $fileUrl,
                'recommendations' => $recommendations,
                'includes' => $includes
            ]
        ];
    }
}
