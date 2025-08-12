<?php

namespace App\Providers;

use App\Http\View\Composers\GuestAppComposer;
use App\Http\View\Composers\TeamAppComposer;
use App\Http\View\Composers\HeaderComposer;
use App\Http\View\Composers\BreadcrumbComposer;
use App\Providers\UsernameUserProvider;
use App\Services\TeamNotificationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(TeamNotificationService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);
        
        // Register View Composers
        $this->registerViewComposers();
        
        // Register Custom Auth Provider
        $this->registerAuthProvider();
        
        // Configure Authentication Redirects
        $this->configureAuthRedirects();
        
        // Register Custom Blade Directives
        $this->registerBladeDirectives();
    }

    /**
     * Register view composers
     */
    private function registerViewComposers(): void
    {
        // Team layout composers
        View::composer([
            'components.team.layout.app',
            'components.team.auth.branding',
            'components.team.forms.login-form',
            'welcome'
        ], TeamAppComposer::class);

        // Client/Guest composers
        View::composer('client.*', GuestAppComposer::class);
        
        // Breadcrumb composer
        View::composer('team.components.breadcrumbs', BreadcrumbComposer::class);
    }

    /**
     * Register custom authentication provider
     */
    private function registerAuthProvider(): void
    {
        Auth::provider('username_eloquent', function ($app, array $config) {
            return new UsernameUserProvider($app['hash'], $config['model']);
        });
    }

    /**
     * Configure authentication redirects based on request path
     */
    private function configureAuthRedirects(): void
    {
        Authenticate::redirectUsing(function ($request) {
            if ($request->is('team') || $request->is('team/*')) {
                return route('team.login');
            }
            if ($request->is('student') || $request->is('student/*')) {
                return route('team.login'); // TODO: Change when student login is created
            }
            if ($request->is('partner') || $request->is('partner/*')) {
                return route('team.login'); // TODO: Change when partner login is created
            }
            return route('team.login');
        });
    }

    /**
     * Register custom Blade directives
     */
    private function registerBladeDirectives(): void
    {
        Blade::if('haspermission', function ($permission) {
            if (Str::contains($permission, '*')) {
                $base = rtrim($permission, '*');

                return auth()->check() && auth()->user()->getAllPermissions()->contains(function ($perm) use ($base) {
                    return Str::startsWith($perm->name, $base);
                });
            }
            return auth()->check() && auth()->user()->can($permission);
        });
    }
}
