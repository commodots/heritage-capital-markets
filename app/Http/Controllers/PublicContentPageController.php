<?php

namespace App\Http\Controllers;

use App\Support\PublicPageContent;
use Inertia\Inertia;

class PublicContentPageController extends Controller
{
    public function boardManagement()
    {
        return Inertia::render('BoardManagement', [
            'content' => PublicPageContent::values(),
        ]);
    }

    public function corporateGovernance()
    {
        return Inertia::render('CorporateGovernance', [
            'content' => PublicPageContent::values(),
        ]);
    }

    public function feesCharges()
    {
        return Inertia::render('FeesCharges', [
            'content' => PublicPageContent::values(),
        ]);
    }
}
