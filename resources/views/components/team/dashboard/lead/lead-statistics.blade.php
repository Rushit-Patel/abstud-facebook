<div class="grid grid-cols-3 gap-5 lg:gap-7.5 h-full items-stretch">
    <style>
        /* . {
            background-image: url('/default/images/2600x1600/bg-3.png');
        }
        .dark . {
            background-image: url('/default/images/2600x1600/bg-3-dark.png');
        } */
    </style>

    <a href="{{ route('team.lead.index',['date' => isset($date) ? $date : '','branch' => isset($branchFilter) ? $branchFilter : '' ,'owner' => isset($userFilter) ? $userFilter : '']) }}" >
        <div class="kt-card flex flex-col items-center justify-center gap-6 h-full bg-cover rtl:bg-[left_top_-1.7rem] bg-[right_top_-1.7rem] bg-no-repeat  primary-gradient">
            <div class="d-flex align-items-center mt-4 justify-content-center rounded-[9px] rounded-circle text-bg-primary" style="width: 48px; height: 48px;">
                <i class="ki-filled ki-users w-5 mt-3 ms-3.5 text-2xl text-white"></i>
            </div>
            <div class="flex flex-col gap-1 px-5 text-center pb-4">
                <h6 class="text-sm font-normal text-secondary-foreground">Total Leads</h6>
                <h4 class="font-semibold text-2xl">{{$totalLeads ? $totalLeads : '0'}}</h4>
            </div>
        </div>
    </a>
    <a href="{{ route('team.lead.index',['status' => base64_encode('1') , 'date' => isset($date) ? $date : '','branch' => isset($branchFilter) ? $branchFilter : '' ,'owner' => isset($userFilter) ? $userFilter : '']) }}" >
        <div class="kt-card flex flex-col items-center justify-center gap-6 h-full bg-cover rtl:bg-[left_top_-1.7rem] bg-[right_top_-1.7rem] bg-no-repeat  warning-gradient">
            <div class="d-flex align-items-center mt-4 justify-content-center rounded-[9px] rounded-circle text-bg-warning" style="width: 48px; height: 48px;">
                <i class="ki-filled ki-watch w-5 mt-2 ms-2.5 text-3xl text-white"></i>
            </div>
            <div class="flex flex-col gap-1 px-5 text-center pb-4">
                <h6 class="text-sm font-normal text-secondary-foreground">Open Leads</h6>
                <h4 class="font-semibold text-2xl">{{$openLeads ? $openLeads : '0'}}</h4>
            </div>
        </div>
    </a>
    <a href="{{ route('team.demo.attended',['status' => base64_encode('1') , 'date' => isset($date) ? $date : '','branch' => isset($branchFilter) ? $branchFilter : '' ,'owner' => isset($userFilter) ? $userFilter : '']) }}" >
        <div class="kt-card flex flex-col items-center justify-center gap-6 h-full bg-cover rtl:bg-[left_top_-1.7rem] bg-[right_top_-1.7rem] bg-no-repeat  secondary-gradient">
            <div class="d-flex align-items-center mt-4 justify-content-center rounded-[9px] rounded-circle text-bg-info" style="width: 48px; height: 48px;">
                <i class="ki-filled ki-watch w-5 mt-2 ms-2.5 text-3xl text-white"></i>
            </div>
            <div class="flex flex-col gap-1 px-5 text-center pb-4">
                <h6 class="text-sm font-normal text-secondary-foreground">Demo Leads</h6>
                <h4 class="font-semibold text-2xl">{{$demoCount}}</h4>
            </div>
        </div>
    </a>
    <a href="{{ route('team.lead.index',['status' => base64_encode('2') ,'date' => isset($date) ? $date : '','branch' => isset($branchFilter) ? $branchFilter : '' ,'owner' => isset($userFilter) ? $userFilter : '']) }}" >
        <div class="kt-card flex flex-col items-center justify-center gap-6 h-full bg-cover rtl:bg-[left_top_-1.7rem] bg-[right_top_-1.7rem] bg-no-repeat  danger-gradient">
            <div class="d-flex align-items-center mt-4 justify-content-center rounded-[9px] rounded-circle text-bg-danger" style="width: 48px; height: 48px;">
                <i class="ki-filled ki-message-question w-5 mt-2 ms-2.5 text-3xl text-white"></i>
            </div>
            <div class="flex flex-col gap-1 px-5 text-center pb-4">
                <h6 class="text-sm font-normal text-secondary-foreground">Close Leads</h6>
                <h4 class="font-semibold text-2xl">{{$closeLeads ? $closeLeads : '0'}}</h4>
            </div>
        </div>
    </a>
    <a href="{{ route('team.lead.index',['status' => base64_encode('3'),'date' => isset($date) ? $date : '','branch' => isset($branchFilter) ? $branchFilter : '' ,'owner' => isset($userFilter) ? $userFilter : '']) }}" >
        <div class="kt-card flex flex-col items-center justify-center gap-6 h-full bg-cover rtl:bg-[left_top_-1.7rem] bg-[right_top_-1.7rem] bg-no-repeat  success-gradient">
            <div class="d-flex align-items-center mt-4 justify-content-center rounded-[9px] rounded-circle text-bg-success" style="width: 48px; height: 48px;">
                <i class="ki-filled ki-security-user w-5 mt-2 ms-2.5 text-3xl text-white"></i>
            </div>
            <div class="flex flex-col gap-1 px-5 text-center pb-4">
                <h6 class="text-sm font-normal text-secondary-foreground">Registered Clients</h6>
                <h4 class="font-semibold text-2xl">{{$registerLeads ? $registerLeads : '0'}}</h4>
            </div>
        </div>

    </a>
    <a href="#" >
        <div class="kt-card flex flex-col items-center justify-center gap-6 h-full bg-cover rtl:bg-[left_top_-1.7rem] bg-[right_top_-1.7rem] bg-no-repeat  info-gradient">
            <div class="d-flex align-items-center mt-4 justify-content-center rounded-[9px] rounded-circle text-bg-info" style="width: 48px; height: 48px;">
                <i class="ki-filled ki-security-user w-5 mt-2 ms-2.5 text-3xl text-white"></i>
            </div>
            <div class="flex flex-col gap-1 px-5 text-center pb-4">
                <h6 class="text-sm font-normal text-secondary-foreground">Payment</h6>
                <h4 class="font-semibold text-2xl">0</h4>
            </div>
        </div>
    </a>
</div>
