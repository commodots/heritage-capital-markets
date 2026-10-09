<?php

namespace App\Http\Controllers;

use App\Models\MarketIndex;
use App\Support\PublicPageContent;
use Inertia\Inertia;

class PublicResourcesController extends Controller
{
    public function index()
    {
        $markets = MarketIndex::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        return Inertia::render('Resources/Index', [
            'markets' => $markets,
            'content' => PublicPageContent::values(),
        ]);
    }
}
