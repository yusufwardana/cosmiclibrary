<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Fine;
use App\Services\LibraryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FineController extends Controller
{
    public function __construct(protected LibraryService $libraryService) {}

    public function index(): View
    {
        $fines = Fine::with(['borrowRecord.member.user'])->latest()->paginate(15);

        return view('library.fines.index', compact('fines'));
    }

    public function show(Fine $fine): View
    {
        $fine->load(['borrowRecord.member.user', 'borrowRecord.bookItem.book']);

        return view('library.fines.show', compact('fine'));
    }

    public function pay(Request $request, Fine $fine): RedirectResponse
    {
        $valid = $request->validate([
            'amount' => 'nullable|numeric|min:0.01|max:'.$fine->fine_amount,
        ]);

        $this->libraryService->payFine($fine, $valid['amount'] ?? null);

        return redirect()->route('library.fines.index')
            ->with('success', 'Denda berhasil dibayarkan.');
    }

    public function waive(Request $request, Fine $fine): RedirectResponse
    {
        $valid = $request->validate(['note' => 'nullable|string|max:500']);
        $this->libraryService->waiveFine($fine, auth()->id(), $valid['note'] ?? '');

        return redirect()->route('library.fines.index')
            ->with('success', 'Denda berhasil dihapuskan.');
    }
}
