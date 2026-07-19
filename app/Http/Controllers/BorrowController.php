<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\BorrowBookRequest;
use App\Http\Requests\ExtendLoanRequest;
use App\Http\Requests\ReturnBookRequest;
use App\Models\BookItem;
use App\Models\BorrowRecord;
use App\Services\LibraryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BorrowController extends Controller
{
    public function __construct(private readonly LibraryService $library) {}

    public function index(Request $request): View
    {
        $records = BorrowRecord::with(['member.user', 'bookItem.book'])
            ->when($request->status, fn ($q, $s) => $q->where('status', $s))
            ->when($request->search, function ($q, $search) {
                $q->whereHas('member.user', fn ($u) => $u->where('name', 'like', "%{$search}%"));
            })
            ->latest()
            ->paginate(15);

        return view('library.borrows.index', compact('records'));
    }

    public function create(): View
    {
        $availableItems = BookItem::with('book')
            ->where('status', 'available')
            ->get();

        return view('library.borrows.create', compact('availableItems'));
    }

    public function store(BorrowBookRequest $request): RedirectResponse
    {
        $this->library->borrowBook(
            $request->integer('member_id'),
            $request->integer('book_item_id'),
            $request->user()->id,
            $request->integer('loan_period_days')
        );

        return redirect()
            ->route('borrows.index')
            ->with('success', 'Buku berhasil dipinjamkan.');
    }

    public function show(BorrowRecord $borrow): View
    {
        $borrow->load(['member.user', 'bookItem.book', 'fines']);

        return view('library.borrows.show', compact('borrow'));
    }

    public function returnForm(BorrowRecord $borrow): View
    {
        $borrow->load(['member.user', 'bookItem.book']);

        return view('library.borrows.return', compact('borrow'));
    }

    public function returnProcess(ReturnBookRequest $request, BorrowRecord $borrow): RedirectResponse
    {
        $this->library->returnBook($borrow, $request->user()->id);

        return redirect()
            ->route('borrows.index')
            ->with('success', 'Buku berhasil dikembalikan.');
    }

    public function extendForm(BorrowRecord $borrow): View
    {
        $borrow->load(['member.user', 'bookItem.book']);

        return view('library.borrows.extend', compact('borrow'));
    }

    public function extendProcess(ExtendLoanRequest $request, BorrowRecord $borrow): RedirectResponse
    {
        $this->library->extendLoan($borrow, $request->integer('extra_days'));

        return redirect()
            ->route('borrows.index')
            ->with('success', 'Peminjaman berhasil diperpanjang.');
    }
}
