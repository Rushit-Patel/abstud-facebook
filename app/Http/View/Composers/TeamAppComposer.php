<?php

namespace App\Http\View\Composers;

use App\Models\ClientLead;
use App\Models\TeamNotification;
use Illuminate\View\View;
use App\Models\CompanySetting;
use App\Models\LeadFollowUp;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class TeamAppComposer
{
    /**
     * Bind data to the view.
     */
    public function compose(View $view): void
    {
        $view->with([
            'appData' => $this->getAppData(),
        ]);
    }

    /**
     * Get all header-specific data
     */
    private function getAppData(): array
    {
        return [
            'companyLogo' => $this->getCompanyLogo(),
            'companyFavicon' => $this->getCompanyFavicon(),
            'companyName' => $this->getCompanyName(),
            'user' => $this->getUserData(),
            'notifications' => $this->getNotificationData(),
            'pendingCounts' => $this->getPendingCounts(),
            'leadCounts' => $this->getLeadCounts(),
        ];
    }

    /**
     * Get company logo URL with fallback
     */
    private function getCompanyLogo(): string
    {
        $settings = CompanySetting::getSettings();

        if ($settings && $settings->company_logo && Storage::disk('public')->exists($settings->company_logo)) {
            return Storage::disk('public')->url($settings->company_logo);
        }

        return $this->getDefaultLogo();
    }

    private function getCompanyFavicon(): string
    {
        $settings = CompanySetting::getSettings();

        if ($settings && $settings->company_favicon && Storage::disk('public')->exists($settings->company_favicon)) {
            return Storage::disk('public')->url($settings->company_favicon);
        }

        return $this->getDefaultFavicon();
    }

    /**
     * Get company name with fallback
     */
    private function getCompanyName(): string
    {
        $settings = CompanySetting::getSettings();

        return $settings?->company_name ?? config('app.name', 'AbstudERP');
    }

    /**
     * Get authenticated user data
     */
    private function getUserData(): array
    {
        $user = Auth::user();

        if (!$user) {
            return [
                'name' => 'Guest',
                'email' => '',
                'initials' => 'G',
                'avatar' => null,
            ];
        }

        return [
            'name' => $user->name,
            'email' => $user->email,
            'initials' => $this->getUserInitials($user->name),
            'avatar' => $user->avatar ?? null,
        ];
    }

    /**
     * Get notification data for header
     */
    private function getNotificationData(): array
    {
        $user = Auth::user();
        
        if (!$user) {
            return [
                'count' => 0,
                'hasUnread' => false,
                'items' => []
            ];
        }

        // Get notifications for current user
        $notifications = TeamNotification::with(['notificationType'])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();
        // Get unread count
        $unreadCount = TeamNotification::where('user_id', $user->id)
            ->where('is_seen', false)
            ->count();

        // Format items for the UI
        $items = $notifications->map(function ($notification) {
            return [
                'id' => $notification->id,
                'title' => $notification->title,
                'message' => $notification->message,
                'link' => $notification->link,
                'icon' => $notification->notificationType->icon ?? 'ki-notification',
                'color' => $notification->notificationType->color ?? 'blue',
                'is_seen' => $notification->is_seen,
                'created_at' => $notification->created_at,
                'time_ago' => $notification->created_at->diffForHumans(),
                'data' => $notification->data,
                'type' => $notification->notificationType->type_key ?? 'client',
            ];
        })->toArray();

        return [
            'count' => $unreadCount,
            'hasUnread' => $unreadCount > 0,
            'items' => $items,
            'total' => $notifications->count(),
        ];
    }

    /**
     * Get user initials from name
     */
    private function getUserInitials(string $name): string
    {
        // $words = explode(' ', $name);
        // $initials = '';

        // foreach ($words as $word) {
        //     if (!empty($word)) {
        //         $initials .= strtoupper(substr($word, 0, 1));
        //         if (strlen($initials) >= 2) break;
        //     }
        // }

        return $name[0] ?: 'U';
    }

    /**
     * Get pending work counts for sidebar badges
     */
    private function getPendingCounts(): array
    {
        if(!Auth::check()){
            return [
                'followUps' => 0,
            ];
        }

        $today = Carbon::today()->format('Y-m-d');

        // Get pending follow-ups count (status = 0 and followup_date <= today)
        $pendingFollowUps = LeadFollowUp::where('status', '0')
            ->where(function ($query) use ($today) {
                $query->where('followup_date', $today)
                      ->orWhere('followup_date', '<', $today);
            })
            ->where('created_by', Auth::user()->id)
            ->count();
        return [
            'followUps' => $pendingFollowUps,
            // Add more pending counts here as needed
            // 'visaApplications' => $pendingVisaApplications,
            // 'coachingSessions' => $pendingCoachingSessions,
        ];
    }
    private function getLeadCounts(): array
    {
        if (!Auth::check()) {
            return [
                'unassignedLeads' => 0,
            ];
        }

        $user = Auth::user();

        // Unassigned or uncovered leads logic
        if ($user->can('lead:show-branch')) {
            // Case 1: User has branch permission
            $unassignedLeads = ClientLead::where(function ($q) {
                    $q->where('assign_owner', 0)
                    ->orWhereNull('assign_owner');
                })
                ->where('branch', $user->branch_id)
                ->count();
        } else {
            // Case 2: No branch permission
            $unassignedLeads = ClientLead::where('assign_owner', $user->id)
                ->whereDoesntHave('getFollowUps')
                ->count();
        }

        return [
            'unassignedLeads' => $unassignedLeads,
        ];
    }

    /**
     * Get default logo path
     */
    private function getDefaultLogo(): string
    {
        return asset('default/images/logo/logo.png');
    }
    private function getDefaultFavicon(): string
    {
        return asset('default/images/logo/fav.png');
    }
}
