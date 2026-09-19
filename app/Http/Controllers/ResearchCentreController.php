<?php

namespace App\Http\Controllers;

use App\Models\ResearchReport;
use Inertia\Inertia;

class ResearchCentreController extends Controller
{
    public function index()
    {
        $reports = ResearchReport::query()
            ->where('is_published', true)
            ->orderByDesc('published_at')
            ->orderBy('sort_order')
            ->get();

        return Inertia::render('ResearchCentre', [
            'reports' => $reports,
        ]);
    }

    public function show(ResearchReport $researchReport)
    {
        abort_unless($researchReport->is_published, 404);

        $relatedReports = ResearchReport::query()
            ->where('is_published', true)
            ->where('id', '!=', $researchReport->id)
            ->orderByDesc('published_at')
            ->take(3)
            ->get();

        return Inertia::render('ResearchReportDetail', [
            'report' => $researchReport,
            'relatedReports' => $relatedReports,
        ]);
    }
}
