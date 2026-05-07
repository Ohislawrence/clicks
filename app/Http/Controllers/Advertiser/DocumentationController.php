<?php

namespace App\Http\Controllers\Advertiser;

use App\Http\Controllers\Controller;
use Inertia\Inertia;

class DocumentationController extends Controller
{
    public function index()
    {
        return Inertia::render('Advertiser/Documentation/Index');
    }
}
