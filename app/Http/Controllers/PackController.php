<?php

namespace App\Http\Controllers;

use App\Services\PackService;
use Illuminate\Http\Request;

class PackController extends Controller
{
    protected PackService $packService;

    public function __construct(PackService $packService)
    {
        $this->packService = $packService;
    }

    public function index(Request $request, $section, $franchise, $name = null, $action = null)
    {
        $pageData = $this->packService->getPackPageData($section, $franchise, $name, $action);

        return view($pageData['view'], $pageData['data']);
    }
}
