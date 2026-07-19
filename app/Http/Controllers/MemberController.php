<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreMemberRequest;
use App\Http\Requests\UpdateMemberRequest;
use App\Models\Member;
use App\Services\LibraryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MemberController extends Controller
{
    public function __construct(private readonly LibraryService $library) {}

    public function index(Request $request): View
    {
        $members = $this->library->listMembers($request->only(['search', 'status']));

        return view('library.members.index', compact('members'));
    }

    public function create(): View
    {
        return view('library.members.create');
    }

    public function store(StoreMemberRequest $request): RedirectResponse
    {
        $this->library->createMember($request->validated());

        return redirect()
            ->route('members.index')
            ->with('success', 'Anggota berhasil ditambahkan.');
    }

    public function show(Member $member): View
    {
        $member->load('user');
        $history = $this->library->memberHistory($member->id);

        return view('library.members.show', compact('member', 'history'));
    }

    public function edit(Member $member): View
    {
        return view('library.members.edit', compact('member'));
    }

    public function update(UpdateMemberRequest $request, Member $member): RedirectResponse
    {
        $this->library->updateMember($member, $request->validated());

        return redirect()
            ->route('members.index')
            ->with('success', 'Anggota berhasil diperbarui.');
    }

    public function destroy(Member $member): RedirectResponse
    {
        $this->library->deleteMember($member);

        return redirect()
            ->route('members.index')
            ->with('success', 'Anggota berhasil dihapus.');
    }
}
