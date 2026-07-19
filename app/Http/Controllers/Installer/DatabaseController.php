<?php

declare(strict_types=1);

namespace App\Http\Controllers\Installer;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DatabaseController extends Controller
{
    public function index(): View
    {
        return view('installer.steps.database');
    }

    public function test(Request $request): RedirectResponse
    {
        $request->validate([
            'db_host' => 'required|string|max:255',
            'db_port' => 'required|integer|min:1|max:65535',
            'db_database' => 'required|string|max:255',
            'db_username' => 'required|string|max:255',
            'db_password' => 'nullable|string|max:255',
        ]);

        try {
            $conn = new \PDO(
                sprintf(
                    'mysql:host=%s;port=%d;dbname=%s',
                    $request->db_host,
                    (int) $request->db_port,
                    $request->db_database
                ),
                $request->db_username,
                $request->db_password,
                [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION, \PDO::ATTR_TIMEOUT => 5]
            );
            $conn = null;
        } catch (\PDOException $e) {
            return redirect()->route('installer.database')
                ->withInput()
                ->with('error', 'Koneksi gagal: '.$e->getMessage());
        }

        session([
            'installer.db_host' => $request->db_host,
            'installer.db_port' => $request->db_port,
            'installer.db_database' => $request->db_database,
            'installer.db_username' => $request->db_username,
            'installer.db_password' => $request->db_password,
            'installer.db_verified' => true,
        ]);

        return redirect()->route('installer.admin');
    }
}
