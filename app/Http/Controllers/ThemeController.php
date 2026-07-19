<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\ThemeManager;

class ThemeController extends Controller
{
    public function __construct(
        private readonly ThemeManager $themeManager
    ) {}

    public function index()
    {
        $themes = $this->themeManager->list();
        $active = $this->themeManager->active();

        return view('theme.index', compact('themes', 'active'));
    }

    public function activate(string $theme)
    {
        if (! $this->themeManager->activate($theme)) {
            return back()->withErrors(['theme' => __('Tema tidak ditemukan.')]);
        }

        return redirect()->route('theme.index')->with('status', __('Tema berhasil diaktifkan.'));
    }
}
