<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PopularInMonth;
use App\Models\Pack;

class HomeController extends Controller
{
    public function index()
    {
        
        $packs = Pack::leftJoin('popular_in_month', 'packs.page_url', '=', 'popular_in_month.page_url')
            ->orderByDesc('popular_in_month.downloads')
            ->select('packs.*', 'popular_in_month.downloads')
            ->limit(5)
            ->get();

        return view('home', ['topPacks' => $packs]);
    }
}
