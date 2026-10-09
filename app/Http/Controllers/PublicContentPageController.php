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

    public function legal(string $group)
    {
        $pageGroup = PublicPageContent::group($group);

        return Inertia::render('LegalPage', [
            'pageKey' => $group,
            'pagePath' => match ($group) {
                'privacy' => '/privacy-policy',
                'terms' => '/terms-of-use',
                'risk' => '/risk-disclosure',
                default => abort(404),
            },
            'content' => PublicPageContent::groupValues($group),
            'pageTitle' => $pageGroup['title'],
        ]);
    }

    public function privacyPolicy()
    {
        return $this->legal('privacy');
    }

    public function termsOfUse()
    {
        return $this->legal('terms');
    }

    public function riskDisclosure()
    {
        return $this->legal('risk');
    }
}
