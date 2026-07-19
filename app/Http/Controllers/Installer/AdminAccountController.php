<?php

declare(strict_types=1);

namespace App\Http\Controllers\Installer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Installer\AdminRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AdminAccountController extends Controller
{
    public function index(): View
    {
        return view('installer.steps.admin');
    }

    public function store(AdminRequest $request): RedirectResponse
    {
        session([
            'installer.admin_name' => $request->name,
            'installer.admin_email' => $request->email,
            'installer.admin_password' => bcrypt($request->password),
        ]);

        return redirect()->route('installer.school');
    }
}
