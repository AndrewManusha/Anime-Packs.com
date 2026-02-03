<?php

namespace App\Services;

use App\Models\Pack;

class PackService
{

    public function getAnalyticData()
    {
        // Логика получения аналитических данных для админ-панели
        return Pack::selectRaw('COUNT(*) as total_packs, SUM(downloads) as total_downloads')
            ->first();
    }

}