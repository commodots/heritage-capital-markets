<?php

namespace App\Support;

class AdminFeatures
{
    public static function all(): array
    {
        return [
            'dashboard' => 'Dashboard',
            'homepage' => 'Homepage Settings',
            'pages' => 'Website Pages',
            'board_management' => 'Board & Management',
            'investment_solutions' => 'Investment Solutions',
            'market_indices' => 'Market Indices',
            'research' => 'Research Reports',
            'articles' => 'News & Articles',
            'events' => 'Events',
            'faqs' => 'FAQs',
            'media' => 'Media Library',
            'newsletter' => 'Newsletter Subscribers',
            'enquiries' => 'Enquiries',
            'account_applications' => 'Account Applications',
            'settings' => 'Site Settings',
            'user_management' => 'User Management',
        ];
    }

    public static function keys(): array
    {
        return array_keys(self::all());
    }
}
