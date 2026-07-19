<?php

declare(strict_types=1);

namespace App\Http\Controllers\Installer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Installer\SmtpRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SmtpController extends Controller
{
    public function index(): View
    {
        return view('installer.steps.smtp');
    }

    public function store(SmtpRequest $request): RedirectResponse
    {
        session([
            'installer.mail_driver' => $request->mail_driver,
            'installer.mail_host' => $request->mail_host ?? null,
            'installer.mail_port' => $request->mail_port ?? null,
            'installer.mail_username' => $request->mail_username ?? null,
            'installer.mail_password' => $request->mail_password ?? null,
            'installer.mail_encryption' => $request->mail_encryption ?? null,
            'installer.mail_from_address' => $request->mail_from_address,
            'installer.mail_from_name' => $request->mail_from_name,
        ]);

        return redirect()->route('installer.confirm');
    }
}
