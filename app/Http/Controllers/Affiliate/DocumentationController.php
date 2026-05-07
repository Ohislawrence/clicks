<?php

namespace App\Http\Controllers\Affiliate;

use App\Http\Controllers\Controller;
use Inertia\Inertia;

class DocumentationController extends Controller
{
    public function index()
    {
        return Inertia::render('Affiliate/Documentation/Index');
    }
}
