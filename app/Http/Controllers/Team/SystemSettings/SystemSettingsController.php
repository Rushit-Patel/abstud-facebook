<?php

namespace App\Http\Controllers\Team\SystemSettings;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Branch;
use App\Models\CompanySetting;
use App\Models\Student;
use App\Models\Partner;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class SystemSettingsController extends Controller
{
    public function index()
    {
        return view('team.settings.index');
    }
}