<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\InstallerRequest;
use App\Services\InstallerEngine;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class InstallerController extends Controller
{
    public function show(): View
    {
        return view('installer.wizard');
    }

    public function install(InstallerRequest $request, InstallerEngine $engine): RedirectResponse
    {
        $engine->install($request->validated());

        return redirect('/');
    }
}
