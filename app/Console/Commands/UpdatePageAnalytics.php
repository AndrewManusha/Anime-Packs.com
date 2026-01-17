<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PageAnalyticsSummary;
use App\Models\PageAnalytics;
use App\Models\PageAnalyticsHourly;
use App\Models\Rate;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UpdatePageAnalytics extends Command
{
    protected $signature = 'page_analytics:update';
    protected $description = 'Обновить статистику по просмотрам и загрузкам';

    public function handle()
    {
        // Получаем максимальную дату обновления
        $lastUpdate = PageAnalyticsSummary::max('updated_at') ?? Carbon::create(2000, 1, 1);

        // Получаем статистику по просмотрам и скачиваниям
        $statistics = PageAnalytics::where('DateTime', '>', $lastUpdate)
            ->select(
                'page_url',
                DB::raw("SUM(CASE WHEN action = 'view' THEN 1 ELSE 0 END) AS views"),
                DB::raw("SUM(CASE WHEN action = 'download' THEN 1 ELSE 0 END) AS downloads")
            )
            ->groupBy('page_url')
            ->get();

        $now = Carbon::now();
        $dataToUpsert = [];
        $hourlyDataToInsert = [];
        $ratingToUpsert = [];

        // Получаем текущие данные для суммирования
        $existingData = PageAnalyticsSummary::whereIn('page_url', $statistics->pluck('page_url'))
            ->get()
            ->keyBy('page_url');

        foreach ($statistics as $stat) {
            $existingViews = $existingData[$stat->page_url]->views ?? 0;
            $existingDownloads = $existingData[$stat->page_url]->downloads ?? 0;

            // Обновляем или вставляем для статистики просмотров и скачиваний
            $dataToUpsert[] = [
                'page_url' => $stat->page_url,
                'views' => $existingViews + $stat->views,
                'downloads' => $existingDownloads + $stat->downloads,
                'updated_at' => $now,
            ];

            // Подготовка для вставки данных по часам
            $hourlyDataToInsert[] = [
                'page_url' => $stat->page_url,
                'views' => $stat->views,
                'downloads' => $stat->downloads,
                'updated_at' => $now,
            ];
        }

        // Получаем данные для голосования
        $rates = Rate::where('updated_at', '>', $lastUpdate)
            ->select('page_url', DB::raw('SUM(difference) as total_difference'), DB::raw('SUM(is_new) as total_new'))
            ->groupBy('page_url')
            ->get();

        $existingRatings = PageAnalyticsSummary::whereIn('page_url', $rates->pluck('page_url'))
            ->get()
            ->keyBy('page_url');

        foreach ($rates as $rate) {
            $existingRating = $existingRatings[$rate->page_url]->rating ?? 0;
            $existingVotes = $existingRatings[$rate->page_url]->votes ?? 0;

            // Вычисление нового рейтинга и голосов
            $rating = $existingRating + $rate->total_difference;
            $votes = $existingVotes + $rate->total_new;
            $avg_rating = $votes > 0 ? $rating / $votes : 0;

            // Добавляем данные для рейтингов и голосов
            $ratingToUpsert[] = [
                'page_url' => $rate->page_url,
                'rating' => $rating,
                'votes' => $votes,
                'avg_rating' => $avg_rating,
                'updated_at' => $now,
            ];
        }

        // Массовое обновление или вставка данных (upsert)
        if (!empty($dataToUpsert)) {
            PageAnalyticsSummary::upsert($dataToUpsert, ['page_url'], ['views', 'downloads', 'updated_at']);
        }
        
        if (!empty($ratingToUpsert)) {
            PageAnalyticsSummary::upsert($ratingToUpsert, ['page_url'], ['rating', 'votes', 'avg_rating', 'updated_at']);
        }

        // Массовая вставка данных по часам
        if (!empty($hourlyDataToInsert)) {
            PageAnalyticsHourly::insert($hourlyDataToInsert);
        }

        $this->info('Статистика обновлена!');
    }
}
