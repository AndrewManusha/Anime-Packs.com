<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CatalogService;
use Jenssegers\Agent\Agent;

class CatalogController extends Controller
{
    private CatalogService $catalogService;

    public function __construct(CatalogService $catalogService)
    {
        $this->catalogService = $catalogService;
    }

    public function index($param1 = null, $param2 = null, $param3 = null, $param4 = null)
    {
        $data = $this->catalogService->getCatalogData($param1, $param2, $param3, $param4);
        
        if (isset($data['error'])) {
            abort($data['error']);
        }

        return view('catalog', $data);
    }
}