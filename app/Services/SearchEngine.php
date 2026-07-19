<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Book;
use App\Models\BorrowRecord;
use App\Models\Member;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

final readonly class SearchEngine
{
    public function searchBooks(string $query = '', array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $q = Book::query()
            ->with(['category', 'items'])
            ->when($query, function (Builder $builder) use ($query): void {
                $term = '%' . $query . '%';
                $builder->where(function (Builder $b) use ($term): void {
                    $b->where('title', 'like', $term)
                        ->orWhere('author', 'like', $term)
                        ->orWhere('isbn', 'like', $term)
                        ->orWhere('publisher', 'like', $term)
                        ->orWhere('ddc_classification', 'like', $term);
                });
            })
            ->when($filters['category_id'] ?? null, fn (Builder $b, $id) => $b->where('category_id', $id))
            ->when($filters['status'] ?? null, function (Builder $b, string $status): void {
                if ($status === 'available') {
                    $b->where('available_copies', '>', 0);
                } elseif ($status === 'unavailable') {
                    $b->where('available_copies', 0);
                }
            })
            ->orderBy('title');

        return $q->paginate($perPage);
    }

    public function searchMembers(string $query = '', array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $q = Member::query()
            ->with('user')
            ->when($query, function (Builder $builder) use ($query): void {
                $term = '%' . $query . '%';
                $builder->where(function (Builder $b) use ($term): void {
                    $b->where('member_number', 'like', $term)
                        ->orWhere('phone', 'like', $term)
                        ->orWhere('address', 'like', $term)
                        ->orWhereHas('user', fn (Builder $u) => $u->where('name', 'like', $term)->orWhere('email', 'like', $term));
                });
            })
            ->when($filters['type'] ?? null, fn (Builder $b, $t) => $b->where('type', $t))
            ->when($filters['class_name'] ?? null, fn (Builder $b, $c) => $b->where('class_name', $c))
            ->when($filters['status'] ?? null, fn (Builder $b, $s) => $b->where('status', $s))
            ->orderBy('member_number');

        return $q->paginate($perPage);
    }

    public function searchBorrowRecords(string $query = '', array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $q = BorrowRecord::query()
            ->with(['member.user', 'bookItem.book'])
            ->when($query, function (Builder $builder) use ($query): void {
                $term = '%' . $query . '%';
                $builder->where(function (Builder $b) use ($term): void {
                    $b->whereHas('member.user', fn (Builder $u) => $u->where('name', 'like', $term))
                        ->orWhereHas('bookItem.book', fn (Builder $bk) => $bk->where('title', 'like', $term))
                        ->orWhereHas('bookItem', fn (Builder $bi) => $bi->where('barcode', 'like', $term));
                });
            })
            ->when($filters['status'] ?? null, fn (Builder $b, $s) => $b->where('status', $s))
            ->when($filters['member_id'] ?? null, fn (Builder $b, $id) => $b->where('member_id', $id))
            ->orderByDesc('borrow_date');

        return $q->paginate($perPage);
    }
}