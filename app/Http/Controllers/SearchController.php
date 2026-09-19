<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\InvestmentSolution;
use App\Models\ResearchReport;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q'));

        $results = collect();

        if ($q !== '' && mb_strlen($q) >= 2) {
            $like = '%' . $q . '%';

            $solutions = InvestmentSolution::query()
                ->where('is_active', true)
                ->where(function ($query) use ($like) {
                    $query->where('title', 'like', $like)
                        ->orWhere('short_description', 'like', $like);
                })
                ->orderBy('sort_order')
                ->take(10)
                ->get()
                ->map(fn ($item) => [
                    'type' => 'Investment Solution',
                    'title' => $item->title,
                    'excerpt' => $item->short_description,
                    'url' => '/investment-solutions/' . $item->slug,
                ]);

            $articles = Article::query()
                ->where('is_published', true)
                ->where(function ($query) use ($like) {
                    $query->where('title', 'like', $like)
                        ->orWhere('excerpt', 'like', $like);
                })
                ->orderByDesc('published_at')
                ->take(10)
                ->get()
                ->map(fn ($item) => [
                    'type' => 'News & Insights',
                    'title' => $item->title,
                    'excerpt' => $item->excerpt,
                    'url' => '/resources-news/' . $item->slug,
                ]);

            $reports = ResearchReport::query()
                ->where('is_published', true)
                ->where('title', 'like', $like)
                ->orderByDesc('published_at')
                ->take(10)
                ->get()
                ->map(fn ($item) => [
                    'type' => 'Research Report',
                    'title' => $item->title,
                    'excerpt' => null,
                    'url' => '/research-centre/reports/' . $item->id,
                ]);

            $results = $solutions
                ->concat($articles)
                ->concat($reports)
                ->values();
        }

        return Inertia::render('Search', [
            'query' => $q,
            'results' => $results,
        ]);
    }
}
