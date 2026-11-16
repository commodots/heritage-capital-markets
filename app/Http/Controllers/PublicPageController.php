<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Inertia\Inertia;

class PublicPageController extends Controller
{
    public function show(string $slug)
    {
        $page = Page::query()
            ->where('slug', $slug)
            ->where('is_published', true)
            ->firstOrFail();

        return Inertia::render('PublicPage', [
            'page' => $page,
        ]);
    }
}
