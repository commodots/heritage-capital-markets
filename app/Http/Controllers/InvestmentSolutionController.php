<?php

namespace App\Http\Controllers;

use App\Models\InvestmentSolution;
use Inertia\Inertia;

class InvestmentSolutionController extends Controller
{
    public function show(string $slug)
    {
        $solution = InvestmentSolution::query()
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $relatedSolutions = InvestmentSolution::query()
            ->where('is_active', true)
            ->where('id', '!=', $solution->id)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->take(3)
            ->get();

        return Inertia::render('InvestmentSolution', [
            'solution' => $solution,
            'relatedSolutions' => $relatedSolutions,
        ]);
    }
}
