<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\UpdateSettingsRequest;
use App\Services\SettingEngine;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function __construct(
        private readonly SettingEngine $settingEngine,
    ) {
    }

    /**
     * Show all settings grouped by their group.
     */
    public function index(Request $request): View
    {
        $groups = $this->settingEngine->all();

        return view('settings.index', [
            'groups' => $groups,
        ]);
    }

    /**
     * Save settings from the form submission.
     */
    public function update(UpdateSettingsRequest $request): RedirectResponse
    {
        $settings = $request->validated();
        if (empty($settings)) {
            return redirect()->route('settings.index')
                ->with('warning', 'Tidak ada pengaturan yang diubah.');
        }

        foreach ($settings as $key => $value) {
            $type = $this->settingEngine->getType($key) ?? 'string';
            $this->settingEngine->set($key, $value, $type);
        }

        $this->settingEngine->clearCache();

        return redirect()->route('settings.index')
            ->with('success', 'Pengaturan berhasil disimpan.');
    }
}