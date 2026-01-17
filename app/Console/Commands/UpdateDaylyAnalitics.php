<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\PageAnalyticsHourly;
use App\Models\PageAnalyticsDayly;
use App\Models\PopularInMonth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UpdateDaylyAnalitics extends Command
{
    protected $signature = 'dayly_analytics:update';
    protected $description = 'Обновить посуточную статистику по просмотрам и загрузкам';

    public function handle()
    {
        // Получаем максимальную дату обновления (если таблица не пуста)
        $lastUpdate = PageAnalyticsDayly::max('updated_at') ?? Carbon::create(2000, 1, 1); // Дата по умолчанию
        
        // Получаем только новые записи из модели PageAnalyticsHourly и агрегируем данные за один запрос
        $statistics = PageAnalyticsHourly::where('updated_at', '>', $lastUpdate)
            ->select('page_url', DB::raw("SUM(views) AS views"), DB::raw("SUM(downloads) AS downloads"))
            ->groupBy('page_url')
            ->get();
        
        // Массовая вставка новых данных в PageAnalyticsDayly
        PageAnalyticsDayly::insert($statistics->map(function ($stat) {
            return [
                'page_url' => $stat->page_url,
                'views' => $stat->views,
                'downloads' => $stat->downloads,
                'updated_at' => Carbon::now(),
            ];
        })->toArray());

        //получаем дату 30 дневной давности
        $monthAgo = Carbon::now()->subDays(30);

        // Получаем агрегированные данные за последние 30 дней прямо из базы данных
        $dailyStatistics = PageAnalyticsDayly::where('updated_at', '>', $monthAgo)
            ->select('page_url', DB::raw("SUM(views) AS views"), DB::raw("SUM(downloads) AS downloads"))
            ->groupBy('page_url')
            ->get();
        
        // Очищаем таблицу `PopularInMonth` перед вставкой новых данных
        PopularInMonth::truncate();
        
        // Выполняем массовую вставку агрегированных данных в таблицу `PopularInMonth`
        PopularInMonth::insert($dailyStatistics->map(function ($stat) {
            return [
                'page_url' => $stat->page_url,
                'views' => $stat->views,
                'downloads' => $stat->downloads,
                'updated_at' => Carbon::now(),
            ];
        })->toArray());

        
        
        
        $this->info('Статистика обновлена!');

    }

}
