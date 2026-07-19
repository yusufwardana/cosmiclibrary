<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Book;
use App\Models\BookItem;
use App\Models\BorrowRecord;
use App\Models\Fine;
use App\Models\Member;
use App\Models\Reservation;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class LibraryService extends BaseService
{
    public function name(): string
    {
        return 'library';
    }

    public function version(): string
    {
        return '1.0.0';
    }

    public function listBooks(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return Book::query()
            ->when($filters['search'] ?? null, fn (Builder $q, $search) => $q->whereFullText(['title', 'author', 'description'], $search))
            ->when($filters['category_id'] ?? null, fn (Builder $q, $id) => $q->where('category_id', $id))
            ->when($filters['isbn'] ?? null, fn (Builder $q, $isbn) => $q->where('isbn', $isbn))
            ->latest()
            ->paginate($perPage);
    }

    public function createBook(array $data): Book
    {
        return DB::transaction(function () use ($data): Book {
            $book = Book::create($data);
            $this->log('info', 'book.created', ['book_id' => $book->id]);

            return $book;
        });
    }

    public function updateBook(Book $book, array $data): Book
    {
        $book->update($data);
        $this->log('info', 'book.updated', ['book_id' => $book->id]);

        return $book->fresh();
    }

    public function deleteBook(Book $book): void
    {
        $book->delete();
        $this->log('info', 'book.deleted', ['book_id' => $book->id]);
    }

    public function addBookItem(Book $book, array $data): BookItem
    {
        $data['book_id'] = $book->id;
        $item = BookItem::create($data);
        $this->syncBookCopyCount($book);
        $this->log('info', 'book_item.created', ['book_item_id' => $item->id]);

        return $item;
    }

    public function updateBookItem(BookItem $item, array $data): BookItem
    {
        $item->update($data);
        $this->syncBookCopyCount($item->book);
        $this->log('info', 'book_item.updated', ['book_item_id' => $item->id]);

        return $item->fresh();
    }

    public function deleteBookItem(BookItem $item): void
    {
        $book = $item->book;
        $item->delete();
        $this->syncBookCopyCount($book);
        $this->log('info', 'book_item.deleted', ['book_item_id' => $item->id]);
    }

    private function syncBookCopyCount(?Book $book): void
    {
        if (! $book instanceof Book) {
            return;
        }

        $book->forceFill([
            'total_copies' => $book->items()->count(),
            'available_copies' => $book->items()->where('status', 'available')->count(),
        ])->save();
    }

    public function listMembers(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return Member::query()
            ->when($filters['search'] ?? null, function (Builder $q, $search) {
                $q->where('member_number', 'like', "%{$search}%")
                    ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"));
            })
            ->when($filters['status'] ?? null, fn (Builder $q, $status) => $q->where('status', $status))
            ->latest()
            ->paginate($perPage);
    }

    public function createMember(array $data): Member
    {
        $data['member_number'] = $data['member_number'] ?? $this->generateMemberNumber();
        $member = Member::create($data);
        $this->log('info', 'member.created', ['member_id' => $member->id]);

        return $member;
    }

    public function updateMember(Member $member, array $data): Member
    {
        $member->update($data);
        $this->log('info', 'member.updated', ['member_id' => $member->id]);

        return $member->fresh();
    }

    public function deleteMember(Member $member): void
    {
        $member->delete();
        $this->log('info', 'member.deleted', ['member_id' => $member->id]);
    }

    private function generateMemberNumber(): string
    {
        return 'M-'.strtoupper(Str::random(8));
    }

    public function borrowBook(int $memberId, int $bookItemId, int $librarianOutId, ?int $loanPeriodDays = null): BorrowRecord
    {
        return DB::transaction(function () use ($memberId, $bookItemId, $librarianOutId, $loanPeriodDays): BorrowRecord {
            $item = BookItem::lockForUpdate()->findOrFail($bookItemId);
            abort_if($item->status !== 'available', 422, 'Item tidak tersedia');

            $days = $loanPeriodDays ?? (int) config('library.loan_period_days', 7);
            $record = BorrowRecord::create([
                'member_id' => $memberId,
                'book_item_id' => $bookItemId,
                'librarian_out_id' => $librarianOutId,
                'borrow_date' => Carbon::today(),
                'due_date' => Carbon::today()->addDays($days),
                'status' => 'borrowed',
            ]);

            $item->update(['status' => 'borrowed']);
            $this->syncBookCopyCount($item->book);
            $this->log('info', 'borrow.created', ['record_id' => $record->id]);

            return $record;
        });
    }

    public function returnBook(BorrowRecord $record, int $librarianInId): BorrowRecord
    {
        return DB::transaction(function () use ($record, $librarianInId): BorrowRecord {
            abort_if($record->status === 'returned', 422, 'Sudah dikembalikan');

            $record->update([
                'return_date' => Carbon::today(),
                'librarian_in_id' => $librarianInId,
                'status' => 'returned',
            ]);

            $record->bookItem->update(['status' => 'available']);
            $this->syncBookCopyCount($record->bookItem->book);

            if ($record->due_date->isPast()) {
                $this->createOverdueFine($record);
            }

            $this->log('info', 'borrow.returned', ['record_id' => $record->id]);

            return $record->fresh();
        });
    }

    public function extendLoan(BorrowRecord $record, ?int $extraDays = null): BorrowRecord
    {
        $maxExtends = (int) config('library.max_extend_count', 2);
        abort_if($record->extend_count >= $maxExtends, 422, 'Batas perpanjangan tercapai');
        abort_if($record->status !== 'borrowed', 422, 'Tidak dapat memperpanjang');

        $days = $extraDays ?? (int) config('library.extend_days', 7);
        $record->update([
            'due_date' => $record->due_date->addDays($days),
            'extend_count' => $record->extend_count + 1,
        ]);
        $this->log('info', 'borrow.extended', ['record_id' => $record->id]);

        return $record->fresh();
    }

    public function reserveBook(int $memberId, int $bookId): Reservation
    {
        $hours = (int) config('library.reservation_hold_hours', 48);

        return DB::transaction(function () use ($memberId, $bookId, $hours): Reservation {
            $res = Reservation::create([
                'member_id' => $memberId,
                'book_id' => $bookId,
                'expires_at' => Carbon::now()->addHours($hours),
                'status' => 'pending',
            ]);
            $this->log('info', 'reservation.created', ['reservation_id' => $res->id]);

            return $res;
        });
    }

    public function cancelReservation(Reservation $reservation): void
    {
        $reservation->update(['status' => 'cancelled']);
        $this->log('info', 'reservation.cancelled', ['reservation_id' => $reservation->id]);
    }

    public function createOverdueFine(BorrowRecord $record, ?float $dailyRate = null): Fine
    {
        $rate = $dailyRate ?? (float) config('library.overdue_fine_per_day', 1000);
        $days = max(0, $record->due_date->diffInDays($record->return_date ?? Carbon::today()));

        return Fine::create([
            'borrow_record_id' => $record->id,
            'fine_type' => 'overdue',
            'fine_amount' => $rate * $days,
            'status' => 'unpaid',
        ]);
    }

    public function payFine(Fine $fine, ?float $amount = null): Fine
    {
        $amount = $amount ?? (float) $fine->fine_amount;
        $paid = (float) $fine->paid_amount + $amount;
        $status = $paid >= (float) $fine->fine_amount ? 'paid' : 'partially_paid';

        $fine->update([
            'paid_amount' => $paid,
            'payment_date' => Carbon::today(),
            'status' => $status,
        ]);
        $this->log('info', 'fine.paid', ['fine_id' => $fine->id]);

        return $fine->fresh();
    }

    public function waiveFine(Fine $fine, int $waivedBy, string $note = ''): Fine
    {
        $fine->update([
            'status' => 'waived',
            'waived_by' => $waivedBy,
            'notes' => $note,
        ]);
        $this->log('info', 'fine.waived', ['fine_id' => $fine->id]);

        return $fine->fresh();
    }

    public function overdueRecords(): Collection
    {
        return BorrowRecord::where('status', 'borrowed')
            ->where('due_date', '<', Carbon::today())
            ->with(['member.user', 'bookItem.book'])
            ->get();
    }

    public function memberHistory(int $memberId): Collection
    {
        return BorrowRecord::where('member_id', $memberId)
            ->with(['bookItem.book'])
            ->orderByDesc('borrow_date')
            ->get();
    }
}
