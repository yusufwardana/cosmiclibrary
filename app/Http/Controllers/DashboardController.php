<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\BorrowRecord;
use App\Models\Member;
use App\Services\WidgetEngine;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        private readonly WidgetEngine $widgetEngine,
    ) {
    }

    public function index(): View
    {
        $totalBooks = Book::count();
        $activeBorrows = BorrowRecord::where('status', 'borrowed')->count();
        $overdueBorrows = BorrowRecord::where('status', 'borrowed')
            ->where('due_date', '<', now())
            ->count();
        $totalMembers = Member::where('status', 'active')->count();

        $sidebarWidgets = $this->widgetEngine->area('dashboard.sidebar');

        return view('dashboard.index', compact(
            'totalBooks', 'activeBorrows', 'overdueBorrows', 'totalMembers', 'sidebarWidgets'
        ));
    }
}
