<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\AnalyticsService;

class AnalyticsController extends Controller
{
    public function index(AnalyticsService $analytics)
    {
        return view('analytics.index', [
            'summary' => $analytics->summary(),
            'borrowsByMonth' => $analytics->borrowsByMonth(),
            'popularBooks' => $analytics->popularBooks(),
            'borrowsByCategory' => $analytics->borrowsByCategory(),
            'finesByMonth' => $analytics->finesByMonth(),
            'activeMembers' => $analytics->activeMembers(),
        ]);
    }

    public function summary(AnalyticsService $analytics)
    {
        return response()->json($analytics->summary());
    }
}