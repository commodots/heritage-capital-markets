<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Event;
use Inertia\Inertia;

class ResourcesNewsController extends Controller
{
    public function index()
    {
        $articles = Article::query()
            ->where('is_published', true)
            ->orderByDesc('published_at')
            ->orderBy('sort_order')
            ->get();

        $upcomingEvents = Event::query()
            ->where('is_published', true)
            ->whereNotNull('event_date')
            ->where('event_date', '>=', now())
            ->orderBy('event_date')
            ->orderBy('sort_order')
            ->get();

        return Inertia::render('ResourcesNews', [
            'articles' => $articles,
            'upcomingEvents' => $upcomingEvents,
        ]);
    }

    public function show(string $slug)
    {
        $article = Article::query()
            ->where('slug', $slug)
            ->where('is_published', true)
            ->firstOrFail();

        $relatedArticles = Article::query()
            ->where('is_published', true)
            ->where('id', '!=', $article->id)
            ->orderByDesc('published_at')
            ->take(3)
            ->get();

        return Inertia::render('ArticleDetail', [
            'article' => $article,
            'relatedArticles' => $relatedArticles,
        ]);
    }
}
