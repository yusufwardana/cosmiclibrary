<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\BorrowRecord;
use App\Models\Reservation;
use App\Observers\BorrowRecordObserver;
use App\Observers\ReservationObserver;
use App\Services\MenuEngine;
use App\Services\ThemeEngine;
use App\Services\WidgetEngine;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ThemeEngine::class);
        $this->app->singleton(MenuEngine::class);
        $this->app->singleton(WidgetEngine::class);
    }

    public function boot(): void
    {
        BorrowRecord::observe(BorrowRecordObserver::class);
        Reservation::observe(ReservationObserver::class);

        try {
            $this->app->make(ThemeEngine::class)->boot();
        } catch (\Throwable $e) {
            // DB may not be available during install — safe skip
        }

        Blade::directive('themeCss', function () {
            return '<?php echo app(App\Services\ThemeEngine::class)->cssVariables(); ?>';
        });

        Blade::directive('themeLogo', function () {
            return '<?php echo app(App\Services\ThemeEngine::class)->logo(); ?>';
        });

        Blade::directive('themeFavicon', function () {
            return '<?php echo app(App\Services\ThemeEngine::class)->favicon(); ?>';
        });

        Blade::directive('menu', function ($expression) {
            return "<?php \$__tree = app(App\Services\MenuEngine::class)->tree{$expression}; ?><?php \$__exists = view()->exists('partials.menu'); if (\$__exists) echo \$__view = view('partials.menu', ['items' => \$__tree])->render(); ?>";
        });

        Blade::directive('widgets', function ($expression) {
            return "<?php echo app(App\Services\WidgetEngine::class)->render{$expression}; ?>";
        });
    }
}
