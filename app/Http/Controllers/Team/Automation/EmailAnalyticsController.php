<?php

namespace App\Http\Controllers\Team\Automation;

use App\Http\Controllers\Controller;
use App\Models\EmailCampaign;
use App\Models\EmailAutomationLog;
use Illuminate\Http\Request;

class EmailAnalyticsController extends Controller
{
    public function index()
    {
        $breadcrumbs = [
            ['title' => 'Dashboard', 'url' => route('team.dashboard.index')],
            ['title' => 'Automation', 'url' => route('team.automation.index')],
            ['title' => 'Analytics', 'url' => null],
        ];

        // Overall statistics
        $totalEmailsSent = EmailAutomationLog::where('status', 'sent')->count();
        $totalPending = EmailAutomationLog::where('status', 'pending')->count();
        $totalFailed = EmailAutomationLog::where('status', 'failed')->count();
        $totalCampaigns = EmailCampaign::count();

        // Campaign performance
        $campaignStats = EmailCampaign::withCount([
            'logs as total_emails',
            'logs as sent_emails' => function($query) {
                $query->where('status', 'sent');
            },
            'logs as failed_emails' => function($query) {
                $query->where('status', 'failed');
            }
        ])->get();

        // Recent activity
        $recentActivity = EmailAutomationLog::with(['campaign', 'clientLead.client'])
            ->latest()
            ->take(10)
            ->get();

        // Email status distribution
        $statusDistribution = EmailAutomationLog::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        return view('team.automation.analytics.index', compact(
            'breadcrumbs',
            'totalEmailsSent',
            'totalPending', 
            'totalFailed',
            'totalCampaigns',
            'campaignStats',
            'recentActivity',
            'statusDistribution'
        ));
    }

    public function campaignDetails(EmailCampaign $campaign)
    {
        $breadcrumbs = [
            ['title' => 'Dashboard', 'url' => route('team.dashboard.index')],
            ['title' => 'Automation', 'url' => route('team.automation.index')],
            ['title' => 'Analytics', 'url' => route('team.automation.analytics')],
            ['title' => $campaign->name . ' Details', 'url' => null],
        ];

        // Campaign specific stats
        $totalEmails = $campaign->logs()->count();
        $sentEmails = $campaign->logs()->where('status', 'sent')->count();
        $failedEmails = $campaign->logs()->where('status', 'failed')->count();
        $pendingEmails = $campaign->logs()->where('status', 'pending')->count();

        // Email logs for this campaign
        $emailLogs = $campaign->logs()
            ->with(['clientLead.client'])
            ->latest()
            ->paginate(20);

        // Performance over time (last 30 days)
        $performanceData = $campaign->logs()
            ->selectRaw('DATE(created_at) as date, status, COUNT(*) as count')
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date', 'status')
            ->orderBy('date')
            ->get();

        return view('team.automation.analytics.campaign-details', compact(
            'breadcrumbs',
            'campaign',
            'totalEmails',
            'sentEmails',
            'failedEmails', 
            'pendingEmails',
            'emailLogs',
            'performanceData'
        ));
    }
}
