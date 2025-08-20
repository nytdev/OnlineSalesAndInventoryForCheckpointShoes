<?php

namespace App\Providers;

use App\Helpers\NavigationHelper;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register navigation helper as a Blade directive
        Blade::directive('navIcon', function ($expression) {
            return "<?php echo App\Helpers\NavigationHelper::getIcon($expression); ?>";
        });
        
        Blade::directive('isActiveRoute', function ($expression) {
            return "<?php echo App\Helpers\NavigationHelper::isActiveRoute($expression) ? 'true' : 'false'; ?>";
        });
        
        Blade::directive('activeClass', function ($expression) {
            return "<?php echo App\Helpers\NavigationHelper::getActiveClass($expression); ?>";
        });
    }
}
