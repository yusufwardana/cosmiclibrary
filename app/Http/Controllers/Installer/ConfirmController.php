<?php

declare(strict_types=1);

namespace App\Http\Controllers\Installer;

use App\Http\Controllers\Controller;
use App\Services\InstallerEngine;
use Illuminate\Http\RedirectResponse;
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
            $installer->install($data);
        } catch (\Throwable $e) {
            report($e);

            return redirect()->route('installer.confirm')
                ->with('error', 'Instalasi gagal: '.$e->getMessage());
        }

        $installerKeys = array_filter(
            array_keys(session()->all()),
            fn (string $k): bool => str_starts_with($k, 'installer.')
        );
        session()->forget($installerKeys);

        return redirect()
            ->route('auth.login')
            ->with('success', 'Instalasi CosmicLib berhasil. Silakan login dengan akun administrator.');
    }

    /**
     * @return array<string, mixed>
     */
    private function wizardData(): array
    {
        $data = [];
        foreach (session()->all() as $key => $value) {
            if (str_starts_with($key, 'installer.')) {
                $data[str_replace('installer.', '', $key)] = $value;
            }
        }

        return $data;
    }
}
