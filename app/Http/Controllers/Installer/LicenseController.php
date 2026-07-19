<?php

declare(strict_types=1);

namespace App\Http\Controllers\Installer;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class LicenseController extends Controller
{
    public function index(): View
    {
        return view('installer.steps.license');
    }

    public function accept(): RedirectResponse
    {
        session(['installer.license_accepted' => true]);

        return redirect()->route('installer.requirements');
    }
}
