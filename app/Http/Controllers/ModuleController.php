<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\ModuleRegistry;

class ModuleController extends Controller
{
    public function __construct(
        private readonly ModuleRegistry $registry
    ) {}

    public function index()
    {
        $modules = $this->registry->all();

        return view('module.index', compact('modules'));
    }

    public function enable(string $module)
    {
        if (! $this->registry->enable($module)) {
            return back()->withErrors(['module' => __('Modul tidak ditemukan.')]);
        }

        return redirect()->route('module.index')->with('status', __('Modul diaktifkan.'));
    }

    public function disable(string $module)
    {
        if (! $this->registry->disable($module)) {
            return back()->withErrors(['module' => __('Modul tidak ditemukan.')]);
        }

        return redirect()->route('module.index')->with('status', __('Modul dinonaktifkan.'));
    }
}
