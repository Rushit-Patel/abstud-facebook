<?php

namespace App\Http\Controllers\Team\Automation;

use App\Http\Controllers\Controller;
use App\Models\EmailCampaign;
use App\Models\EmailAutomationLog;
use App\Models\WhatsappMessage;
use App\Models\WhatsappProvider;
use App\Models\WhatsappTemplateVariableMapping;

class AutomationController extends Controller
{
    public function index()
    {
        $breadcrumbs = [
            ['label' => 'Dashboard', 'url' => route('team.dashboard'), 'title' => 'Dashboard'],
            ['label' => 'Automation', 'url' => null, 'title' => 'Automation'],
        ];

        // Get email automation statistics
        $emailStats = [
            'totalCampaigns' => EmailCampaign::count(),
            'activeCampaigns' => EmailCampaign::where('is_active', true)->count(),
            'totalEmailsSent' => EmailAutomationLog::where('status', 'sent')->count(),
            'pendingEmails' => EmailAutomationLog::where('status', 'pending')->count(),
        ];

        // Get WhatsApp automation statistics
        $whatsappStats = [
            'totalProviders' => WhatsappProvider::count(),
            'activeProviders' => WhatsappProvider::where('is_active', true)->count(),
            'totalMessagesSent' => WhatsappMessage::whereIn('status', ['sent', 'delivered', 'read'])->count(),
            'pendingMessages' => WhatsappMessage::where('status', 'pending')->count(),
            'failedMessages' => WhatsappMessage::where('status', 'failed')->count(),
            'totalTemplates' => WhatsappTemplateVariableMapping::distinct('template_name')->count(),
        ];

        // Recent email campaigns
        $recentEmailCampaigns = EmailCampaign::with('emailTemplate')
            ->latest()
            ->take(3)
            ->get();

        return view('team.automation.index', compact(
            'breadcrumbs',
            'emailStats',
            'whatsappStats',
            'recentEmailCampaigns'
        ));
    }
}
