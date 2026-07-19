<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Widget;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;

class WidgetEngine extends BaseService
{
    public function name(): string
    {
        return 'widget';
    }

    public function version(): string
    {
        return '1.0.0';
    }

    public function all(): Collection
    {
        return Cache::remember('widget.engine.all', 3600, fn () => Widget::orderBy('sort_order')->get());
    }

    public function area(string $area): Collection
    {
        return $this->all()
            ->where('area', $area)
            ->where('is_active', true)
            ->sortBy('sort_order')
            ->values();
    }

    public function has(string $slug): bool
    {
        return Widget::where('slug', $slug)->exists();
    }

    public function register(string $slug, array $definition): Widget
    {
        $widget = Widget::updateOrCreate(
            ['slug' => $slug],
            [
                'name' => $definition['name'] ?? $slug,
                'description' => $definition['description'] ?? null,
                'view' => $definition['view'] ?? 'widgets.default',
                'area' => $definition['area'] ?? 'sidebar',
                'settings' => $definition['settings'] ?? null,
                'is_active' => $definition['is_active'] ?? true,
                'sort_order' => $definition['sort_order'] ?? 0,
            ]
        );
        Cache::forget('widget.engine.all');

        return $widget;
    }

    public function enable(string $slug): void
    {
        Widget::where('slug', $slug)->update(['is_active' => true]);
        Cache::forget('widget.engine.all');
    }

    public function disable(string $slug): void
    {
        Widget::where('slug', $slug)->update(['is_active' => false]);
        Cache::forget('widget.engine.all');
    }

    /**
     * Render widgets for an area as HTML string.
     */
    public function render(string $area): string
    {
        $widgets = $this->area($area);
        if ($widgets->isEmpty()) {
            return '';
        }

        $html = [];
        foreach ($widgets as $widget) {
            if (! View::exists($widget->view)) {
                continue;
            }
            $html[] = View::make($widget->view, ['widget' => $widget, 'settings' => $widget->settings ?? []])->render();
        }

        return implode("\n", $html);
    }
}
