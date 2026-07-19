<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Book;
use App\Models\BookItem;
use App\Models\BorrowRecord;
use App\Models\Fine;
use App\Models\Member;
use Illuminate\Support\Facades\DB;

class AnalyticsService
{
    public function summary(): array
    {
        return [
            'books' => Book::count(),
            'members' => Member::count(),
            'active_borrows' => BorrowRecord::where('status', 'borrowed')->count(),
            'overdue' => BorrowRecord::where('status', 'overdue')->count(),
            'fines_total' => Fine::sum('fine_amount'),
            'fines_unpaid' => Fine::whereIn('status', ['unpaid', 'partially_paid'])->sum('fine_amount'),
        ];
    }

    public function borrowsByMonth(int $months = 12): array
    {
        $since = now()->subMonths($months);

        $rows = BorrowRecord::where('borrow_date', '>=', $since)
            ->selectRaw("strftime('%Y-%m', borrow_date) as ym, COUNT(*) as total")
            ->groupBy('ym')
            ->orderBy('ym')
            ->pluck('total', 'ym')
            ->toArray();

        // Fill gaps with zeros
        $result = [];
        for ($i = $months - 1; $i >= 0; $i--) {
            $ym = now()->subMonths($i)->format('Y-m');
            $result[$ym] = $rows[$ym] ?? 0;
        }

        return $result;
    }

    public function popularBooks(int $limit = 10): array
    {
        return DB::table('borrow_records')
            ->join('book_items', 'book_items.id', '=', 'borrow_records.book_item_id')
            ->join('books', 'books.id', '=', 'book_items.book_id')
            ->select('books.title', DB::raw('COUNT(*) as total'))
            ->groupBy('books.id', 'books.title')
            ->orderByDesc('total')
            ->limit($limit)
            ->pluck('total', 'books.title')
            ->toArray();
    }

    public function borrowsByCategory(): array
    {
        return DB::table('categories')
            ->join('books', 'books.category_id', '=', 'categories.id')
            ->join('book_items', 'book_items.book_id', '=', 'books.id')
            ->join('borrow_records', 'borrow_records.book_item_id', '=', 'book_items.id')
            ->select('categories.name', DB::raw('COUNT(*) as total'))
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('total')
            ->pluck('total', 'categories.name')
            ->toArray();
    }

    public function finesByMonth(int $months = 12): array
    {
        $since = now()->subMonths($months);

        $rows = Fine::where('created_at', '>=', $since)
            ->selectRaw("strftime('%Y-%m', created_at) as ym, SUM(fine_amount) as total")
            ->groupBy('ym')
            ->orderBy('ym')
            ->pluck('total', 'ym')
            ->toArray();

        $result = [];
        for ($i = $months - 1; $i >= 0; $i--) {
            $ym = now()->subMonths($i)->format('Y-m');
            $result[$ym] = $rows[$ym] ?? 0;
        }

        return $result;
    }

    public function activeMembers(int $limit = 10): array
    {
        return DB::table('members')
            ->join('borrow_records', 'borrow_records.member_id', '=', 'members.id')
            ->select('members.member_number', DB::raw('COUNT(*) as total'))
            ->groupBy('members.id', 'members.member_number')
            ->orderByDesc('total')
            ->limit($limit)
            ->pluck('total', 'members.member_number')
            ->toArray();
    }
}