<?php

namespace App\Models;

use Spatie\Permission\Models\Permission as SpatiePermission;

/**
 * Custom Permission Model
 * 
 * This model extends Spatie's Permission model to provide dynamic categorization
 * of permissions based on modules. To add new permission categories:
 * 
 * 1. Add module mapping to $moduleCategories array
 * 2. Or use Permission::addModuleCategory() method dynamically
 * 
 * Example:
 * Permission::addModuleCategory('invoice', 'billing', 'Invoice Management', 'fas fa-receipt');
 */

class Permission extends SpatiePermission
{
    /**
     * Module categories mapping for permissions
     * This defines how permission modules are categorized
     */
    protected static $moduleCategories = [
        'user' => [
            'category' => 'users',
            'label' => 'User Management',
            'icon' => 'fas fa-users'
        ],
        'branch' => [
            'category' => 'branches',
            'label' => 'Branch Management',
            'icon' => 'fas fa-building'
        ],
        'lead' => [
            'category' => 'leads',
            'label' => 'Lead Management',
            'icon' => 'fas fa-user-plus'
        ],
        'follow-up' => [
            'category' => 'follow_ups',
            'label' => 'Follow-ups',
            'icon' => 'fas fa-tasks'
        ],
        'master-module' => [
            'category' => 'master_data',
            'label' => 'Master',
            'icon' => 'fas fa-database'
        ],
        'role' => [
            'category' => 'roles_permissions',
            'label' => 'Roles & Permissions',
            'icon' => 'fas fa-shield-alt'
        ],
        'permission' => [
            'category' => 'roles_permissions',
            'label' => 'Roles & Permissions',
            'icon' => 'fas fa-shield-alt'
        ],
        'student' => [
            'category' => 'students',
            'label' => 'Student Management',
            'icon' => 'fas fa-graduation-cap'
        ],
        'partner' => [
            'category' => 'partners',
            'label' => 'Partner Management',
            'icon' => 'fas fa-handshake'
        ],
        'report' => [
            'category' => 'reports',
            'label' => 'Reports',
            'icon' => 'fas fa-chart-bar'
        ],
        'announcement' => [
            'category' => 'communications',
            'label' => 'Communications',
            'icon' => 'fas fa-bullhorn'
        ],
        'notification' => [
            'category' => 'communications',
            'label' => 'Communications',
            'icon' => 'fas fa-bell'
        ],
        'backup' => [
            'category' => 'system',
            'label' => 'System Management',
            'icon' => 'fas fa-cog'
        ],
        'restore' => [
            'category' => 'system',
            'label' => 'System Management',
            'icon' => 'fas fa-cog'
        ],
        'system' => [
            'category' => 'system',
            'label' => 'System Management',
            'icon' => 'fas fa-cog'
        ],
        'audit' => [
            'category' => 'system',
            'label' => 'System Management',
            'icon' => 'fas fa-cog'
        ],
        'integration' => [
            'category' => 'system',
            'label' => 'System Management',
            'icon' => 'fas fa-cog'
        ],
        'dashboard' => [
            'category' => 'dashboard',
            'label' => 'Dashboard',
            'icon' => 'fas fa-tachometer-alt'
        ],
        'company' => [
            'category' => 'company_settings',
            'label' => 'Company Settings',
            'icon' => 'fas fa-building-flag'
        ]
    ];

    /**
     * Get the category for this permission based on its module
     */
    public function getCategoryAttribute()
    {
        return $this->getModuleCategory()['category'] ?? 'other';
    }

    /**
     * Get the category label for this permission
     */
    public function getCategoryLabelAttribute()
    {
        return $this->getModuleCategory()['label'] ?? 'Other';
    }

    /**
     * Get the category icon for this permission
     */
    public function getCategoryIconAttribute()
    {
        return $this->getModuleCategory()['icon'] ?? 'fas fa-cog';
    }

    /**
     * Get the module from the permission name
     */
    public function getModuleAttribute()
    {
        // Split by colon to get module from module:action format
        if (str_contains($this->name, ':')) {
            return explode(':', $this->name)[0];
        }

        // Fallback for permissions without colon - extract from permission name
        if (str_contains($this->name, '_')) {
            return explode('_', $this->name)[0];
        }

        return $this->name;
    }

    /**
     * Get module category information
     */
    protected function getModuleCategory()
    {
        $module = $this->module;
        
        // Check if module exists in our mapping
        if (isset(static::$moduleCategories[$module])) {
            return static::$moduleCategories[$module];
        }

        // Fallback for legacy permission names
        foreach (static::$moduleCategories as $moduleKey => $categoryInfo) {
            if (str_contains($this->name, $moduleKey)) {
                return $categoryInfo;
            }
        }

        // Default fallback
        return [
            'category' => $module,
            'label' => ucwords(str_replace(['_', '-'], ' ', $module)),
            'icon' => 'fas fa-cog'
        ];
    }

    /**
     * Scope to group permissions by category
     */
    public function scopeGroupedByCategory($query)
    {
        return $query->get()->groupBy('category');
    }

    /**
     * Get all available categories
     */
    public static function getAvailableCategories()
    {
        $categories = [];
        foreach (static::$moduleCategories as $module => $categoryInfo) {
            $categoryKey = $categoryInfo['category'];
            if (!isset($categories[$categoryKey])) {
                $categories[$categoryKey] = [
                    'label' => $categoryInfo['label'],
                    'icon' => $categoryInfo['icon']
                ];
            }
        }
        return $categories;
    }

    /**
     * Add a new module category mapping
     */
    public static function addModuleCategory($module, $category, $label, $icon = 'fas fa-cog')
    {
        static::$moduleCategories[$module] = [
            'category' => $category,
            'label' => $label,
            'icon' => $icon
        ];
    }

    /**
     * Get module categories configuration
     */
    public static function getModuleCategories()
    {
        return static::$moduleCategories;
    }
}
