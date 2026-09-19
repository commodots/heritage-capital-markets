<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

class DownloadsController extends Controller
{
    public function index()
    {
        return Inertia::render('Resources/Downloads');
    }
}
