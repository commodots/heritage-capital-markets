<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Inertia\Inertia;

class AboutController extends Controller
{
    public function index()
    {
        $page = Page::query()
            ->where('slug', 'about-us')
            ->where('is_published', true)
            ->firstOrFail();

        return Inertia::render('About', [
            'page' => $page,
        ]);
    }
}