<?php

declare(strict_types=1);

namespace App\Http\Controllers\Installer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Installer\SchoolRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SchoolController extends Controller
{
    public function index(): View
    {
        return view('installer.steps.school');
    }

    public function store(SchoolRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('school_logo')) {
            $path = $request->file('school_logo')->store('logos', 'public');
            $data['school_logo'] = $path;
        }

        session([
            'installer.school_name' => $data['school_name'],
            'installer.school_address' => $data['school_address'] ?? null,
            'installer.school_phone' => $data['school_phone'] ?? null,
            'installer.school_email' => $data['school_email'] ?? null,
            'installer.school_logo' => $data['school_logo'] ?? null,
        ]);

        return redirect()->route('installer.smtp');
    }
}
