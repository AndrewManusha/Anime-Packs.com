<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\FranchisePackService;

class FPService extends Command
{
    protected $signature = 'franchise:update {franchise?}';
    protected $description = 'Обновляет или создает франшизные паки';

    public function handle(FranchisePackService $service)
    {
        $franchise = $this->argument('franchise');
        $service->process($franchise);

        $this->info('Франшизные паки обновлены');
    }
}
