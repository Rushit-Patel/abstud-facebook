<?php

namespace App\Services;

class SidebarMenuService
{
    /**
     * Generate the sidebar menu configuration
     * 
     * @param \App\Models\User|null $user
     * @return array
     */
    public static function getMenuItems($user = null): array
    {
        $menuItems = [
            [
                'type' => 'item',
                'icon' => 'ki-filled ki-element-11',
                'label' => 'Dashboards',
                'hasSubmenu' => true,
                'children' => [
                    [
                        'type' => 'item',
                        'label' => 'Light Sidebar',
                        'route' => 'team.dashboard',
                        'active' => request()->routeIs('team.dashboard')
                    ],
                    [
                        'type' => 'item',
                        'label' => 'Dark Sidebar',
                        'route' => 'team.dashboard.dark',
                        'active' => request()->routeIs('team.dashboard.dark')
                    ]
                ]
            ],
            
            [
                'type' => 'heading',
                'label' => 'User'
            ],
            
            [
                'type' => 'item',
                'icon' => 'ki-filled ki-profile-circle',
                'label' => 'Public Profile',
                'hasSubmenu' => true,
                'expanded' => true,
                'children' => [
                    [
                        'type' => 'group',
                        'label' => 'Profiles',
                        'visibleCount' => 6, // Show first 6, rest in "show more"
                        'children' => [
                            [
                                'label' => 'Default',
                                'route' => 'team.profile.default',
                                'active' => request()->routeIs('team.profile.default')
                            ],
                            [
                                'label' => 'Creator',
                                'route' => 'team.profile.creator',
                                'active' => request()->routeIs('team.profile.creator')
                            ],
                            [
                                'label' => 'Company',
                                'route' => 'team.profile.company',
                                'active' => request()->routeIs('team.profile.company')
                            ],
                            [
                                'label' => 'NFT',
                                'route' => 'team.profile.nft',
                                'active' => request()->routeIs('team.profile.nft')
                            ],
                            [
                                'label' => 'Blogger',
                                'route' => 'team.profile.blogger',
                                'active' => request()->routeIs('team.profile.blogger')
                            ],
                            [
                                'label' => 'CRM',
                                'route' => 'team.profile.crm',
                                'active' => request()->routeIs('team.profile.crm')
                            ],
                            // Hidden by default (shown in "show more")
                            [
                                'label' => 'Gamer',
                                'route' => 'team.profile.gamer',
                                'active' => request()->routeIs('team.profile.gamer')
                            ],
                            [
                                'label' => 'Feeds',
                                'route' => 'team.profile.feeds',
                                'active' => request()->routeIs('team.profile.feeds')
                            ],
                            [
                                'label' => 'Plain',
                                'route' => 'team.profile.plain',
                                'active' => request()->routeIs('team.profile.plain')
                            ],
                            [
                                'label' => 'Modal',
                                'route' => 'team.profile.modal',
                                'active' => request()->routeIs('team.profile.modal')
                            ]
                        ]
                    ],
                    [
                        'type' => 'group',
                        'label' => 'Projects',
                        'children' => [
                            [
                                'label' => '3 Columns',
                                'route' => 'team.projects.three-columns',
                                'active' => request()->routeIs('team.projects.three-columns')
                            ],
                            [
                                'label' => '2 Columns',
                                'route' => 'team.projects.two-columns',
                                'active' => request()->routeIs('team.projects.two-columns')
                            ]
                        ]
                    ],
                    // Direct items
                    [
                        'type' => 'item',
                        'label' => 'Works',
                        'route' => 'team.works',
                        'active' => request()->routeIs('team.works')
                    ],
                    [
                        'type' => 'item',
                        'label' => 'Teams',
                        'route' => 'team.teams',
                        'active' => request()->routeIs('team.teams')
                    ],
                    [
                        'type' => 'item',
                        'label' => 'Network',
                        'route' => 'team.network',
                        'active' => request()->routeIs('team.network')
                    ],
                    [
                        'type' => 'item',
                        'label' => 'Activity',
                        'route' => 'team.activity',
                        'active' => request()->routeIs('team.activity')
                    ]
                ]
            ],
            
            [
                'type' => 'item',
                'icon' => 'ki-filled ki-setting-2',
                'label' => 'My Account',
                'hasSubmenu' => true,
                'children' => [
                    [
                        'type' => 'group',
                        'label' => 'Account Home',
                        'children' => [
                            [
                                'label' => 'Get Started',
                                'route' => 'team.account.get-started',
                                'active' => request()->routeIs('team.account.get-started')
                            ],
                            [
                                'label' => 'User Profile',
                                'route' => 'team.account.user-profile',
                                'active' => request()->routeIs('team.account.user-profile')
                            ],
                            [
                                'label' => 'Company Profile',
                                'route' => 'team.account.company-profile',
                                'active' => request()->routeIs('team.account.company-profile')
                            ]
                        ]
                    ],
                    [
                        'type' => 'group',
                        'label' => 'Billing',
                        'children' => [
                            [
                                'label' => 'Basic',
                                'route' => 'team.billing.basic',
                                'active' => request()->routeIs('team.billing.basic')
                            ],
                            [
                                'label' => 'Enterprise',
                                'route' => 'team.billing.enterprise',
                                'active' => request()->routeIs('team.billing.enterprise')
                            ],
                            [
                                'label' => 'Plans',
                                'route' => 'team.billing.plans',
                                'active' => request()->routeIs('team.billing.plans')
                            ]
                        ]
                    ],
                    [
                        'type' => 'group',
                        'label' => 'Security',
                        'children' => [
                            [
                                'label' => 'Overview',
                                'route' => 'team.security.overview',
                                'active' => request()->routeIs('team.security.overview')
                            ],
                            [
                                'label' => 'Privacy Settings',
                                'route' => 'team.security.privacy',
                                'active' => request()->routeIs('team.security.privacy')
                            ]
                        ]
                    ],
                    [
                        'type' => 'group',
                        'label' => 'Members & Roles',
                        'children' => [
                            [
                                'label' => 'Team Members',
                                'route' => 'team.members.team',
                                'active' => request()->routeIs('team.members.team')
                            ],
                            [
                                'label' => 'Roles',
                                'route' => 'team.members.roles',
                                'active' => request()->routeIs('team.members.roles')
                            ]
                        ]
                    ]
                ]
            ]
        ];

        // Add user-specific menu items if user has permissions
        if ($user) {
            $menuItems = self::addPermissionBasedMenuItems($menuItems, $user);
        }

        return $menuItems;
    }

    /**
     * Add menu items based on user permissions
     * 
     * @param array $menuItems
     * @param \App\Models\User $user
     * @return array
     */
    private static function addPermissionBasedMenuItems(array $menuItems, $user): array
    {
        // Example: Add admin-only menu items
        if ($user->hasRole('admin')) {
            $menuItems[] = [
                'type' => 'heading',
                'label' => 'Administration'
            ];

            $menuItems[] = [
                'type' => 'item',
                'icon' => 'ki-filled ki-user-tick',
                'label' => 'User Management',
                'route' => 'admin.users.index',
                'active' => request()->routeIs('admin.users.*')
            ];

            $menuItems[] = [
                'type' => 'item',
                'icon' => 'ki-filled ki-setting-3',
                'label' => 'System Settings',
                'route' => 'admin.settings.index',
                'active' => request()->routeIs('admin.settings.*')
            ];
        }

        // Add manager-specific items
        if ($user->hasRole('manager')) {
            $menuItems[] = [
                'type' => 'item',
                'icon' => 'ki-filled ki-chart-line',
                'label' => 'Reports',
                'route' => 'manager.reports.index',
                'active' => request()->routeIs('manager.reports.*'),
                'badge' => '3' // Example notification badge
            ];
        }

        return $menuItems;
    }

    /**
     * Generate menu items for a specific module/section
     * 
     * @param string $module
     * @param \App\Models\User|null $user
     * @return array
     */
    public static function getModuleMenuItems(string $module, $user = null): array
    {
        switch ($module) {
            case 'student':
                return self::getStudentMenuItems($user);
            case 'partner':
                return self::getPartnerMenuItems($user);
            default:
                return self::getMenuItems($user);
        }
    }

    /**
     * Get student module menu items
     * 
     * @param \App\Models\User|null $user
     * @return array
     */
    private static function getStudentMenuItems($user = null): array
    {
        return [
            [
                'type' => 'item',
                'icon' => 'ki-filled ki-home',
                'label' => 'Dashboard',
                'route' => 'student.dashboard',
                'active' => request()->routeIs('student.dashboard')
            ],
            [
                'type' => 'heading',
                'label' => 'Academic'
            ],
            [
                'type' => 'item',
                'icon' => 'ki-filled ki-book',
                'label' => 'Courses',
                'route' => 'student.courses.index',
                'active' => request()->routeIs('student.courses.*')
            ],
            [
                'type' => 'item',
                'icon' => 'ki-filled ki-calendar',
                'label' => 'Schedule',
                'route' => 'student.schedule.index',
                'active' => request()->routeIs('student.schedule.*')
            ]
        ];
    }

    /**
     * Get partner module menu items
     * 
     * @param \App\Models\User|null $user
     * @return array
     */
    private static function getPartnerMenuItems($user = null): array
    {
        return [
            [
                'type' => 'item',
                'icon' => 'ki-filled ki-home',
                'label' => 'Dashboard',
                'route' => 'partner.dashboard',
                'active' => request()->routeIs('partner.dashboard')
            ],
            [
                'type' => 'heading',
                'label' => 'Business'
            ],
            [
                'type' => 'item',
                'icon' => 'ki-filled ki-profile-user',
                'label' => 'Clients',
                'route' => 'partner.clients.index',
                'active' => request()->routeIs('partner.clients.*')
            ],
            [
                'type' => 'item',
                'icon' => 'ki-filled ki-chart-pie',
                'label' => 'Analytics',
                'route' => 'partner.analytics.index',
                'active' => request()->routeIs('partner.analytics.*')
            ]
        ];
    }
}
