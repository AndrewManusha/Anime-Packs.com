<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SitemapController extends Controller
{
    public function index()
    {
        // Генерация карты сайта
        $urls = [
            route('home'),
            route('catalog'),
            route('terms-of-use'),
            route('privacy-policy'),
            route('commission'),
            // Добавьте другие страницы сайта, которые хотите включить
        ];
        
        $franchises = \App\Models\Pack::select('franchise')->distinct()->get();
        foreach ($franchises as $franchise) {
            $urls[] = route('catalog') . '/' . $franchise->franchise;
        }
        
        // Пример динамического получения всех паков из базы данных
        $packs = \App\Models\Pack::select('page_url')->distinct()->get();
        foreach ($packs as $pack) {
            $urls[] = url($pack->page_url);
        }

        $cacheKey = 'sitemap';
        $sitemap = Cache::remember($cacheKey, 60, function () use ($urls) {
            // Создаем XML документ
            $xml = new \SimpleXMLElement('<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"/>');
            foreach ($urls as $url) {
                $urlElement = $xml->addChild('url');
                $urlElement->addChild('loc', $url);
                $urlElement->addChild('lastmod', now()->toAtomString());
                $urlElement->addChild('priority', '0.5');
            }
            return $xml->asXML();
        });

        return response($sitemap, 200)
            ->header('Content-Type', 'application/xml');
    }
}
