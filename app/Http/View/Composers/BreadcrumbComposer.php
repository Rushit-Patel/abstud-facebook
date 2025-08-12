<?php

namespace App\Http\View\Composers;

use Illuminate\View\View;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class BreadcrumbComposer
{
    /**
     * Bind data to the view.
     */
    public function compose(View $view): void
    {
        $view->with([
            'breadcrumbs' => $this->generateBreadcrumbs(),
        ]);
    }

    /**
     * Generate breadcrumbs based on current route
     */
    private function generateBreadcrumbs(): array
    {
        $breadcrumbs = [];
        $routeName = Route::currentRouteName();
        $segments = request()->segments();

        // Remove 'admin' from segments if present
        if (isset($segments[0]) && $segments[0] === 'admin') {
            array_shift($segments);
        }

        // Generate breadcrumbs based on route patterns
        switch (true) {
            case Str::startsWith($routeName, 'team.dashboard'):
                // Dashboard has no additional breadcrumbs (just home icon)
                break;

            case Str::startsWith($routeName, 'admin.servers'):
                $breadcrumbs = $this->getServersBreadcrumbs($routeName, $segments);
                break;

            case Str::startsWith($routeName, 'admin.students'):
                $breadcrumbs = $this->getStudentsBreadcrumbs($routeName, $segments);
                break;

            case Str::startsWith($routeName, 'admin.partners'):
                $breadcrumbs = $this->getPartnersBreadcrumbs($routeName, $segments);
                break;

            case Str::startsWith($routeName, 'admin.settings'):
                $breadcrumbs = $this->getSettingsBreadcrumbs($routeName, $segments);
                break;

            default:
                $breadcrumbs = $this->getDefaultBreadcrumbs($segments);
                break;
        }

        return $breadcrumbs;
    }

    /**
     * Get servers related breadcrumbs
     */
    private function getServersBreadcrumbs(string $routeName, array $segments): array
    {
        $breadcrumbs = [
            ['title' => 'Servers', 'url' => route('admin.servers.index')]
        ];

        if (Str::contains($routeName, 'show') || Str::contains($routeName, 'edit')) {
            $serverId = request()->route('server');
            $breadcrumbs[] = ['title' => "Server #{$serverId}"];
        }

        return $breadcrumbs;
    }

    /**
     * Get students related breadcrumbs
     */
    private function getStudentsBreadcrumbs(string $routeName, array $segments): array
    {
        $breadcrumbs = [
            ['title' => 'Students', 'url' => route('admin.students.index')]
        ];

        if (Str::contains($routeName, 'create')) {
            $breadcrumbs[] = ['title' => 'Add Student'];
        } elseif (Str::contains($routeName, 'show') || Str::contains($routeName, 'edit')) {
            $studentId = request()->route('student');
            $breadcrumbs[] = ['title' => "Student #{$studentId}"];
        }

        return $breadcrumbs;
    }

    /**
     * Get partners related breadcrumbs
     */
    private function getPartnersBreadcrumbs(string $routeName, array $segments): array
    {
        $breadcrumbs = [
            ['title' => 'Partners', 'url' => route('admin.partners.index')]
        ];

        if (Str::contains($routeName, 'create')) {
            $breadcrumbs[] = ['title' => 'Add Partner'];
        } elseif (Str::contains($routeName, 'show') || Str::contains($routeName, 'edit')) {
            $partnerId = request()->route('partner');
            $breadcrumbs[] = ['title' => "Partner #{$partnerId}"];
        }

        return $breadcrumbs;
    }

    /**
     * Get settings related breadcrumbs
     */
    private function getSettingsBreadcrumbs(string $routeName, array $segments): array
    {
        $breadcrumbs = [
            ['title' => 'Settings', 'url' => route('team.settings.index')]
        ];

        // Add specific settings page
        if (isset($segments[1])) {
            $breadcrumbs[] = ['title' => ucfirst($segments[1])];
        }

        return $breadcrumbs;
    }

    /**
     * Get default breadcrumbs based on URL segments
     */
    private function getDefaultBreadcrumbs(array $segments): array
    {
        $breadcrumbs = [];
        $url = '/admin';

        foreach ($segments as $segment) {
            $url .= '/' . $segment;
            $breadcrumbs[] = [
                'title' => ucfirst(str_replace('-', ' ', $segment)),
                'url' => $url
            ];
        }

        return $breadcrumbs;
    }
}
