<?php

namespace App\Http\Controllers\Team\Automation;

use App\DataTables\Team\Automation\EmailAutomationLogDataTable;
use App\Http\Controllers\Controller;
use App\Models\EmailCampaign;
use App\Models\EmailAutomationLog;
use App\Models\EmailTemplate;
use App\Services\TemplateVariableService;
use Illuminate\Http\Request;

class EmailAutomationController extends Controller
{
    public function index()
    {
        $breadcrumbs = [
            ['title' => 'Dashboard', 'url' => route('team.dashboard.index')],
            ['title' => 'Automation', 'url' => route('team.automation.index')],
            ['title' => 'Email Automation', 'url' => null],
        ];

        // Get campaign statistics
        $totalCampaigns = EmailCampaign::count();
        $activeCampaigns = EmailCampaign::where('is_active', true)->count();
        $totalEmailsSent = EmailAutomationLog::where('status', 'sent')->count();
        $pendingEmails = EmailAutomationLog::where('status', 'pending')->count();

        // Recent campaigns
        $recentCampaigns = EmailCampaign::with('emailTemplate')
            ->latest()
            ->take(5)
            ->get();

        // Recent email logs
        $recentLogs = EmailAutomationLog::with(['clientLead.client', 'campaign'])
            ->latest()
            ->take(10)
            ->get();

        return view('team.automation.email.index', compact(
            'breadcrumbs',
            'totalCampaigns',
            'activeCampaigns', 
            'totalEmailsSent',
            'pendingEmails',
            'recentCampaigns',
            'recentLogs'
        ));
    }

    public function logs(EmailAutomationLogDataTable $dataTable)
    {
        return $dataTable->render('team.automation.email.logs');
    }

    public function retryEmail(EmailAutomationLog $log)
    {
        if ($log->status === 'failed') {
            $log->update([
                'status' => 'pending',
                'scheduled_at' => now(),
                'error_message' => null
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Email has been scheduled for retry.'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Only failed emails can be retried.'
        ], 400);
    }
}
