<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full" data-kt-theme="true" data-kt-theme-mode="light" dir="ltr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ $appData['companyName'] }} - Team Portal</title>
    
    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ $appData['companyFavicon'] }}" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />

    <!-- Team Module Styles -->
    <link rel="stylesheet" href="{{ asset('assets/css/team/vendors/styles.bundle.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/team/styles.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/app.css') }}">

</head>

<body class="antialiased flex h-full text-base text-foreground bg-background">
    <!-- Main Container -->
    <div class="flex flex-col min-h-screen w-full">
        <!-- Header Navigation -->
        <header class="kt-header bg-card border-b border-border">
            <div class="kt-container-fixed">
                <div class="flex items-center justify-between h-16">
                    <!-- Logo -->
                    <div class="flex items-center gap-3">
                        <img src="{{ $appData['companyLogo'] }}" alt="{{ $appData['companyName'] }}" class="h-8">
                    </div>
                    
                    <!-- Navigation Links -->
                    <nav class="hidden md:flex items-center gap-6">
                        @if (Route::has('team.login'))
                            @auth('web')
                                <a href="{{ route('team.dashboard') }}" 
                                   class="kt-btn kt-btn-outline kt-btn-sm">
                                    <i class="ki-filled ki-element-11 mr-2"></i>
                                    Go to Dashboard
                                </a>
                            @else
                                <a href="{{ route('team.login') }}" 
                                   class="kt-btn kt-btn-primary kt-btn-sm">
                                    <i class="ki-filled ki-profile-circle mr-2"></i>
                                    Team Login
                                </a>
                            @endauth
                        @endif
                    </nav>
                </div>
            </div>
        </header>

        <!-- Hero Section -->
        <main class="flex-1 flex items-center justify-center py-16 px-4">
            <div class="kt-container-fixed">
                <div class="grid lg:grid-cols-2 gap-12 items-center">
                    <!-- Content Column -->
                    <div class="space-y-8">
                        <!-- Hero Title -->
                        <div class="space-y-4">
                            <div class="kt-badge kt-badge-outline kt-badge-primary kt-badge-lg mb-4">
                                <i class="ki-filled ki-abstract-26 mr-2"></i>
                                Team Management Portal
                            </div>
                            <h1 class="text-4xl lg:text-5xl font-bold text-mono leading-tight">
                                Welcome to 
                                <span class="text-primary">{{ $appData['companyName'] }}</span>
                                Team Portal
                            </h1>
                            <p class="text-lg text-muted-foreground leading-relaxed">
                                Streamline your student management, lead tracking, and coaching operations with our comprehensive team portal. Built for education consultants and study abroad agencies.
                            </p>
                        </div>

                        <!-- Feature Grid -->
                        <div class="grid sm:grid-cols-2 gap-4">
                            <div class="kt-card kt-card-accent p-4">
                                <div class="flex items-start gap-3">
                                    <div class="kt-badge kt-badge-success kt-badge-circle">
                                        <i class="ki-filled ki-people text-lg"></i>
                                    </div>
                                    <div>
                                        <h3 class="font-semibold text-mono mb-1">Lead Management</h3>
                                        <p class="text-sm text-muted-foreground">Track and manage student leads efficiently</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="kt-card kt-card-accent p-4">
                                <div class="flex items-start gap-3">
                                    <div class="kt-badge kt-badge-info kt-badge-circle">
                                        <i class="ki-filled ki-bank text-lg"></i>
                                    </div>
                                    <div>
                                        <h3 class="font-semibold text-mono mb-1">Visa Processing</h3>
                                        <p class="text-sm text-muted-foreground">Complete visa application workflow</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="kt-card kt-card-accent p-4">
                                <div class="flex items-start gap-3">
                                    <div class="kt-badge kt-badge-warning kt-badge-circle">
                                        <i class="ki-filled ki-book-open text-lg"></i>
                                    </div>
                                    <div>
                                        <h3 class="font-semibold text-mono mb-1">Coaching Programs</h3>
                                        <p class="text-sm text-muted-foreground">Manage IELTS, PTE & TOEFL coaching</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="kt-card kt-card-accent p-4">
                                <div class="flex items-start gap-3">
                                    <div class="kt-badge kt-badge-primary kt-badge-circle">
                                        <i class="ki-filled ki-setting-2 text-lg"></i>
                                    </div>
                                    <div>
                                        <h3 class="font-semibold text-mono mb-1">System Settings</h3>
                                        <p class="text-sm text-muted-foreground">Comprehensive configuration options</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- CTA Buttons -->
                        <div class="flex flex-col sm:flex-row gap-4">
                            @if (Route::has('team.login'))
                                @auth('web')
                                    <a href="{{ route('team.dashboard') }}" 
                                       class="kt-btn kt-btn-primary kt-btn-lg">
                                        <i class="ki-filled ki-element-11 mr-2"></i>
                                        Go to Dashboard
                                    </a>
                                @else
                                    <a href="{{ route('team.login') }}" 
                                       class="kt-btn kt-btn-primary kt-btn-lg">
                                        <i class="ki-filled ki-profile-circle mr-2"></i>
                                        Access Team Portal
                                    </a>
                                @endauth
                            @endif
                            
                            <button class="kt-btn kt-btn-outline kt-btn-lg" onclick="showFeatures()">
                                <i class="ki-filled ki-eye mr-2"></i>
                                Explore Features
                            </button>
                        </div>
                    </div>

                    <!-- Visual Column -->
                    <div class="relative">
                        <!-- Dashboard Preview Card -->
                        <div class="kt-card bg-gradient-to-br from-primary/5 to-secondary/5 p-8 text-center">
                            <div class="space-y-6">
                                <!-- Stats Grid -->
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="kt-card bg-background p-4">
                                        <div class="text-2xl font-bold text-primary mb-1">950</div>
                                        <div class="text-sm text-muted-foreground">Total Leads</div>
                                    </div>
                                    <div class="kt-card bg-background p-4">
                                        <div class="text-2xl font-bold text-success mb-1">343</div>
                                        <div class="text-sm text-muted-foreground">Applications</div>
                                    </div>
                                    <div class="kt-card bg-background p-4">
                                        <div class="text-2xl font-bold text-warning mb-1">250</div>
                                        <div class="text-sm text-muted-foreground">Offers</div>
                                    </div>
                                    <div class="kt-card bg-background p-4">
                                        <div class="text-2xl font-bold text-info mb-1">123</div>
                                        <div class="text-sm text-muted-foreground">Completed</div>
                                    </div>
                                </div>

                                <!-- Chart Placeholder -->
                                <div class="kt-card bg-background p-6">
                                    <div class="flex items-center justify-center h-32 bg-muted/30 rounded-lg">
                                        <div class="text-center">
                                            <i class="ki-filled ki-chart-line text-4xl text-primary mb-2"></i>
                                            <p class="text-sm text-muted-foreground">Analytics Dashboard</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Team Features -->
                                <div class="flex justify-center gap-2">
                                    <div class="kt-badge kt-badge-success kt-badge-sm">Lead Tracking</div>
                                    <div class="kt-badge kt-badge-info kt-badge-sm">Visa Management</div>
                                    <div class="kt-badge kt-badge-warning kt-badge-sm">Coaching</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <!-- Features Section -->
        <section id="features" class="py-16 bg-muted/30">
            <div class="kt-container-fixed">
                <div class="text-center mb-12">
                    <h2 class="text-3xl font-bold text-mono mb-4">Comprehensive Team Features</h2>
                    <p class="text-lg text-muted-foreground max-w-2xl mx-auto">
                        Everything you need to manage your educational consultancy business efficiently
                    </p>
                </div>

                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- Lead Management -->
                    <div class="kt-card p-6 text-center">
                        <div class="kt-badge kt-badge-primary kt-badge-circle kt-badge-lg mb-4 mx-auto">
                            <i class="ki-filled ki-people text-2xl"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-mono mb-2">Lead Management</h3>
                        <p class="text-muted-foreground mb-4">Track student inquiries, manage follow-ups, and convert leads efficiently.</p>
                        <ul class="text-sm text-muted-foreground space-y-1">
                            <li>• Lead source tracking</li>
                            <li>• Status management</li>
                            <li>• Follow-up scheduling</li>
                        </ul>
                    </div>

                    <!-- Student Visa -->
                    <div class="kt-card p-6 text-center">
                        <div class="kt-badge kt-badge-success kt-badge-circle kt-badge-lg mb-4 mx-auto">
                            <i class="ki-filled ki-bank text-2xl"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-mono mb-2">Student Visa</h3>
                        <p class="text-muted-foreground mb-4">Complete visa application processing and document management.</p>
                        <ul class="text-sm text-muted-foreground space-y-1">
                            <li>• Application tracking</li>
                            <li>• Document checklist</li>
                            <li>• Status updates</li>
                        </ul>
                    </div>

                    <!-- Coaching Management -->
                    <div class="kt-card p-6 text-center">
                        <div class="kt-badge kt-badge-warning kt-badge-circle kt-badge-lg mb-4 mx-auto">
                            <i class="ki-filled ki-book-open text-2xl"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-mono mb-2">Coaching Programs</h3>
                        <p class="text-muted-foreground mb-4">Manage IELTS, PTE, TOEFL coaching programs and student progress.</p>
                        <ul class="text-sm text-muted-foreground space-y-1">
                            <li>• Program scheduling</li>
                            <li>• Progress tracking</li>
                            <li>• Result management</li>
                        </ul>
                    </div>

                    <!-- User Management -->
                    <div class="kt-card p-6 text-center">
                        <div class="kt-badge kt-badge-info kt-badge-circle kt-badge-lg mb-4 mx-auto">
                            <i class="ki-filled ki-profile-circle text-2xl"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-mono mb-2">User Management</h3>
                        <p class="text-muted-foreground mb-4">Manage team members, roles, and permissions effectively.</p>
                        <ul class="text-sm text-muted-foreground space-y-1">
                            <li>• Role-based access</li>
                            <li>• Permission control</li>
                            <li>• Team collaboration</li>
                        </ul>
                    </div>

                    <!-- Branch Management -->
                    <div class="kt-card p-6 text-center">
                        <div class="kt-badge kt-badge-secondary kt-badge-circle kt-badge-lg mb-4 mx-auto">
                            <i class="ki-filled ki-geolocation text-2xl"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-mono mb-2">Branch Management</h3>
                        <p class="text-muted-foreground mb-4">Manage multiple branch locations and their operations.</p>
                        <ul class="text-sm text-muted-foreground space-y-1">
                            <li>• Multi-branch support</li>
                            <li>• Location management</li>
                            <li>• Branch analytics</li>
                        </ul>
                    </div>

                    <!-- Analytics & Reports -->
                    <div class="kt-card p-6 text-center">
                        <div class="kt-badge kt-badge-danger kt-badge-circle kt-badge-lg mb-4 mx-auto">
                            <i class="ki-filled ki-chart-line text-2xl"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-mono mb-2">Analytics & Reports</h3>
                        <p class="text-muted-foreground mb-4">Comprehensive reporting and analytics for business insights.</p>
                        <ul class="text-sm text-muted-foreground space-y-1">
                            <li>• Performance metrics</li>
                            <li>• Custom reports</li>
                            <li>• Data visualization</li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="kt-footer bg-card border-t border-border py-8">
            <div class="kt-container-fixed">
                <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                    <div class="flex items-center gap-3">
                        <img src="{{ $appData['companyLogo'] }}" alt="{{ $appData['companyName'] }}" class="h-6">
                        <span class="text-sm text-muted-foreground">© 2025 {{ $appData['companyName'] }}. All rights reserved.</span>
                    </div>
                    <div class="flex items-center gap-4">
                        @if (Route::has('team.login'))
                            @guest('web')
                                <a href="{{ route('team.login') }}" class="text-sm text-muted-foreground hover:text-primary">
                                    Team Login
                                </a>
                            @endguest
                        @endif
                    </div>
                </div>
            </div>
        </footer>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('assets/js/team/core.bundle.js') }}"></script>
    
    <script>
        function showFeatures() {
            document.getElementById('features').scrollIntoView({ 
                behavior: 'smooth' 
            });
        }
    </script>
</body>

</html>
