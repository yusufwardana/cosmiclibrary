<?php

declare(strict_types=1);

namespace App\Http\Controllers\Installer;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class RequirementsController extends Controller
{
    public function index(): View
    {
        $requirements = $this->checkRequirements();

        return view('installer.steps.requirements', compact('requirements'));
    }

    public function verify(): RedirectResponse
    {
        $failures = collect($this->checkRequirements())
            ->filter(fn (array $req) => $req['meets'] === false)
            ->values();

        if ($failures->isNotEmpty()) {
            return redirect()->route('installer.requirements')
                ->with('error', 'Beberapa persyaratan tidak terpenuhi.');
        }

        session(['installer.requirements_met' => true]);

        return redirect()->route('installer.database');
    }

    private function checkRequirements(): array
    {
        return [
            'php_version' => [
                'label' => 'PHP >= 8.2',
                'meets' => version_compare(PHP_VERSION, '8.2.0', '>='),
                'current' => PHP_VERSION,
            ],
            'bcmath' => [
                'label' => 'BCMath',
                'meets' => extension_loaded('bcmath'),
            ],
            'ctype' => [
                'label' => 'CType',
                'meets' => extension_loaded('ctype'),
            ],
            'curl' => [
                'label' => 'cURL',
                'meets' => extension_loaded('curl'),
            ],
            'dom' => [
                'label' => 'DOM',
                'meets' => extension_loaded('dom'),
            ],
            'fileinfo' => [
                'label' => 'FileInfo',
                'meets' => extension_loaded('fileinfo'),
            ],
            'gd' => [
                'label' => 'GD',
                'meets' => extension_loaded('gd'),
            ],
            'json' => [
                'label' => 'JSON',
                'meets' => extension_loaded('json'),
            ],
            'mbstring' => [
                'label' => 'Multibyte String',
                'meets' => extension_loaded('mbstring'),
            ],
            'openssl' => [
                'label' => 'OpenSSL',
                'meets' => extension_loaded('openssl'),
            ],
            'pdo_mysql' => [
                'label' => 'PDO MySQL',
                'meets' => extension_loaded('pdo_mysql'),
            ],
            'tokenizer' => [
                'label' => 'Tokenizer',
                'meets' => extension_loaded('tokenizer'),
            ],
            'xml' => [
                'label' => 'XML',
                'meets' => extension_loaded('xml'),
            ],
            'zip' => [
                'label' => 'ZIP',
                'meets' => extension_loaded('zip'),
            ],
            'writable_env' => [
                'label' => '.env Writeable',
                'meets' => file_exists(base_path('.env')) ? is_writable(base_path('.env')) : is_writable(base_path()),
            ],
            'writable_storage' => [
                'label' => 'storage/ Writeable',
                'meets' => is_writable(storage_path()),
            ],
            'writable_bootstrap_cache' => [
                'label' => 'bootstrap/cache/ Writeable',
                'meets' => is_writable(base_path('bootstrap/cache')),
            ],
        ];
    }
}
