<?php

declare(strict_types=1);

namespace App\Http\Controllers\Installer;

use App\Http\Controllers\Controller;
use App\Services\InstallerEngine;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class ConfirmController extends Controller
{
    public function index(): View
    {
        $data = $this->wizardData();

        return view('installer.steps.confirm', compact('data'));
    }

    public function run(InstallerEngine $installer): RedirectResponse
    {
        $data = $this->wizardData();

        try {
            Log::info('ConfirmController::run - Starting installation', ['data_keys' => array_keys($data)]);
            $installer->install($data);
            Log::info('ConfirmController::run - Installation successful');
        } catch (\Throwable $e) {
            Log::error('ConfirmController::run - Installation failed', ['error' => $e->getMessage()]);
            report($e);

            return redirect()->route('installer.confirm')
                ->with('error', 'Instalasi gagal: '.$e->getMessage());
        }

        session()->forget('installer');
        Log::info('ConfirmController::run - Session cleared, redirecting to login');

        return redirect()
            ->route('auth.login')
            ->with('success', 'Instalasi CosmicLib berhasil. Silakan login dengan akun administrator.');
    }

    /**
     * @return array<string, mixed>
     */
    private function wizardData(): array
    {
        return (array) session('installer', []);
    }
}
