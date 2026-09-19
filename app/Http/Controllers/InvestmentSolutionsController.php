<?php

namespace App\Http\Controllers;

use App\Models\InvestmentSolution;
use Inertia\Inertia;

class InvestmentSolutionsController extends Controller
{
    public function index()
    {
        $solutions = InvestmentSolution::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        return Inertia::render('InvestmentSolutions', [
            'solutions' => $solutions,
        ]);
    }
}