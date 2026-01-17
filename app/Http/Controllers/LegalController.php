<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LegalController extends Controller
{
    public function terms()
    {
        return view('terms-of-use');
    }
    
    public function privacy()
    {
        return view('privacy-policy');
    }
}
