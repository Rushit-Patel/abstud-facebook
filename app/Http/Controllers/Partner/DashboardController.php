<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use App\Models\CompanySetting;

class DashboardController extends Controller
{
    public function index()
    {
        $partner = auth('partner')->user();
        $company = CompanySetting::getSettings();

        return view('partner.dashboard', compact('partner', 'company'));
    }
}
