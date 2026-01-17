<?php

namespace App\Services;

use App\Models\Pack;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use ZipArchive;
use Illuminate\Support\Facades\Storage;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;
use Illuminate\Support\Facades\File;


class FranchisePackService
{
    /**
     * Обработка паков с опциональной фильтрацией по франшизе
     *
     * @param string|null $franchiseToProcess
     * @return void
     */
    public function process(?string $franchiseToProcess = null): void
    {
        $query = Pack::select(
                'page_url',
                'franchise',
                'file',
                'category',
                'updated_at',
                'status'
            )
            ->where('type', 'resource-pack');
        
        if ($franchiseToProcess) {
            $query->where('franchise', $franchiseToProcess);
        }


        // Получаем все паки (уже с where type = 'resource-pack')
        $packsByFranchise = $query->get()->groupBy('franchise');
        
        foreach ($packsByFranchise as $franchise => $group) {
            // Ищем франшизный пак (page_url заканчивается на франшизу)
            $franchisePack = $group->first(function ($pack) use ($franchise) {
                return Str::lower(basename($pack->page_url)) === Str::lower($franchise);
            });
            
            if ($franchisePack) {
                $normalPacks = $group->filter(function ($pack) use ($franchisePack) {
                    return $pack->page_url !== $franchisePack->page_url && $pack->status === 'posted';
                });
            
                // Обновляем франшизный пак
                $this->updateFranchisePack($normalPacks, $franchisePack, 'resource-pack', $franchise);
            } else {
                $normalPacks = $group->filter(function ($pack) {
                    return $pack->status === 'posted';
                });
            
                if ($normalPacks->count() >= 3) {
                    // Создаем франшизный пак
                    $this->createFranchisePack($normalPacks, 'resource-pack', $franchise);
                }
            }
        }
    }

    /**
     * Обновление франшизного пака
     *
     * @param Collection $normalPacks Все паки данной франшизы
     * @param Pack $franchisePack Франшизный пак для обновления
     * @param string $type Тип паков
     * @param string $franchise Название франшизы
     * @return void
     */
    private function updateFranchisePack(Collection $normalPacks, Pack $franchisePack, string $type, string $franchise): void
    {
        // Находим последний по обновлению обычный пак
        $latestNormalPack = $normalPacks->sortByDesc('updated_at')->first();
    
        // Если франшизный пак обновлен позже или в тот же момент — пропускаем
        // if ($franchisePack->updated_at >= $latestNormalPack->updated_at) {
        //     return;
        // }
    
        // Объединяем категории
        $franchiseCategories = array_filter(array_map('trim', explode(',', $franchisePack->category ?? '')));
        $normalCategories = $normalPacks
            ->flatMap(function ($pack) {
                return array_filter(array_map('trim', explode(',', $pack->category ?? '')));
            })
            ->unique()
            ->values()
            ->all();
    
        $mergedCategories = collect(array_merge($franchiseCategories, $normalCategories))
            ->unique()
            ->values()
            ->all();
    
        $franchisePack->category = implode(', ', $mergedCategories);
        $franchisePack->updated_at = $latestNormalPack->updated_at;
        $franchisePack->save();
        
        $this->fileManager($normalPacks, 'resource-pack', $franchise);
    }

    /**
     * Создание франшизного пака
     *
     * @param Collection $normalPacks Все паки данной франшизы
     * @param string $type Тип паков
     * @param string $franchise Название франшизы
     * @return void
     */
    private function createFranchisePack(Collection $normalPacks, string $type, string $franchise): void
    {
        // Формируем URL: /type/franchise, приводим к нижнему регистру и заменяем пробелы на дефисы
        $typeSegment = strtolower(str_replace(' ', '-', $type));
        $franchiseSegment = strtolower(str_replace(' ', '-', $franchise));

        $franchiseUrl = '/' . $typeSegment . '/' . $franchiseSegment;

        // Собираем уникальные категории из нормальных паков
        $categories = $normalPacks->flatMap(function ($pack) {
            return array_filter(array_map('trim', explode(',', $pack->category ?? '')));
        })->unique()->values()->all();

        $categoriesStr = implode(', ', $categories);

        // Создаем новый франшизный пак
        $newFranchisePack = new Pack();
        $newFranchisePack->page_url = $franchiseUrl;
        $newFranchisePack->file = str_replace('-', '_', $franchise) . '.zip';
        $newFranchisePack->franchise = $franchise;
        $newFranchisePack->type = $type;
        $newFranchisePack->category = $categoriesStr;
        $newFranchisePack->status = 'drafted';
        $newFranchisePack->updated_at = now();

        $newFranchisePack->save();
        
        $this->fileManager($normalPacks, 'resource-pack', $franchise);
    }
    
    /**
     * работа с файлами
     *
     * @param Collection $normalPacks Все простые паки данной франшизы
     * @param string $type Тип паков
     * @param string $franchise Название франшизы
     * @return void
     */
     private function fileManager(Collection $normalPacks, string $type, string $franchise): void
     {
         // Пути
        $basePath = "public/$type/$franchise";
        $franchiseDir = $basePath . '/' . $franchise;
        $franchiseFile = str_replace('-', '_', $franchise) . '.zip';
        $franchiseZipPath = storage_path("app/$franchiseDir/" . $franchiseFile);
    
        // Временная папка для сборки
        $Stemp = $franchiseDir. '/temp';
        $temp = storage_path("app/$Stemp");
        Storage::deleteDirectory($Stemp);
        Storage::makeDirectory($Stemp);
    
        // Распаковываем франшизный архив в temp
        $zip = new ZipArchive();
        if ($zip->open($franchiseZipPath) === true) {
            if ($zip->locateName('pack.png', ZipArchive::FL_NOCASE) !== false) {
                $zip->extractTo($temp, 'pack.png');
            }
            $zip->close();
        }
        
        // Получаем последний нормальный пак для pack.mcmeta
        $latestNormalPack = $normalPacks->sortByDesc('updated_at')->first();
    
        // Добавляем assets и pack.mcmeta из обычных паков
        foreach ($normalPacks as $pack) {
            $packDir = $basePath . '/' . basename($pack->page_url);
            $packZipPath = storage_path("app/$packDir/" . $pack->file);
    
            $zip = new ZipArchive();
            if ($zip->open($packZipPath) === true) {
                $filesToExtract = array();
    
                for ($i = 0; $i < $zip->numFiles; $i++) {
                    $name = $zip->getNameIndex($i);
                    if (strpos($name, 'assets/') === 0) {
                        $filesToExtract[] = $name;
                    }
                }
    
                // Если это последний архив — добавляем pack.mcmeta
                if ($pack->page_url === $latestNormalPack->page_url && $zip->locateName('pack.mcmeta') !== false) {
                    $filesToExtract[] = 'pack.mcmeta';
                }
    
                if (!empty($filesToExtract)) {
                    $zip->extractTo($temp, $filesToExtract);
                }
                $zip->close();
            }
        }
        
        // Добавляем файл правил пользования
        File::put($temp . '/terms_of_use.url', "[InternetShortcut]\nURL=https://anime-packs.com/terms-of-use");
    
        // Собираем новый архив (перезаписываем франшизный)
        $zip = new ZipArchive();
        if ($zip->open($franchiseZipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
            $files = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($temp),
                RecursiveIteratorIterator::LEAVES_ONLY
            );
    
            foreach ($files as $file) {
                if (!$file->isDir()) {
                    $filePath = $file->getRealPath();
                    $relativePath = substr($filePath, strlen($temp) + 1);
                    $zip->addFile($filePath, $relativePath);
                }
            }
            $zip->close();
        }
    
        // Чистим временную директорию
        Storage::deleteDirectory($Stemp);
     }
}
