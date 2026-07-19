<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreReservationRequest;
use App\Models\Reservation;
use App\Services\LibraryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ReservationController extends Controller
{
    public function __construct(protected LibraryService $libraryService) {}

    public function index(): View
    {
        $reservations = Reservation::with(['member.user', 'book'])->latest()->paginate(15);

        return view('library.reservations.index', compact('reservations'));
    }

    public function create(): View
    {
        return view('library.reservations.create');
    }

    public function store(StoreReservationRequest $request): RedirectResponse
    {
        $valid = $request->validated();

        $this->libraryService->reserveBook((int) $valid['member_id'], (int) $valid['book_id']);

        return redirect()->route('library.reservations.index')
            ->with('success', 'Reservasi berhasil dibuat.');
    }

    public function show(Reservation $reservation): View
    {
        $reservation->load(['member.user', 'book']);

        return view('library.reservations.show', compact('reservation'));
    }

    public function cancel(Reservation $reservation): RedirectResponse
    {
        $this->libraryService->cancelReservation($reservation);

        return redirect()->route('library.reservations.index')
            ->with('success', 'Reservasi dibatalkan.');
    }
}
