<div class="grid grid-cols-2 gap-5 lg:gap-7.5 h-full items-stretch">
    <style>
        .channel-stats-bg {
            background-image: url('/default/images/2600x1600/bg-3.png');
        }

        .dark .channel-stats-bg {
            background-image: url('/default/images/2600x1600/bg-3-dark.png');
        }
    </style>

    <a href="{{ route('team.lead.index',['date' => isset($date) ? $date : '','branch' => isset($branchFilter) ? $branchFilter : '' ,'owner' => isset($userFilter) ? $userFilter : '']) }}" target="_blank">
        <div
            class="kt-card flex-col justify-between gap-6 h-full bg-cover rtl:bg-[left_top_-1.7rem] bg-[right_top_-1.7rem] bg-no-repeat channel-stats-bg">
            <i class="ki-filled ki-users w-7 mt-4 ms-5 text-4xl text-primary"></i>
            <div class="flex flex-col gap-1 pb-4 px-5">
                <span class="text-3xl font-semibold text-mono">
                    {{$totalLeads ? $totalLeads : '0'}}
                </span>
                <span class="text-sm font-normal text-secondary-foreground">
                    Total Leads
                </span>
            </div>
        </div>
    </a>
    <a href="{{ route('team.lead.index',['status' => base64_encode('1') , 'date' => isset($date) ? $date : '','branch' => isset($branchFilter) ? $branchFilter : '' ,'owner' => isset($userFilter) ? $userFilter : '']) }}" target="_blank">
        <div
            class="kt-card flex-col justify-between gap-6 h-full bg-cover rtl:bg-[left_top_-1.7rem] bg-[right_top_-1.7rem] bg-no-repeat channel-stats-bg">
            <i class="ki-filled ki-watch w-7 mt-4 ms-5 text-4xl text-primary"></i>
            <div class="flex flex-col gap-1 pb-4 px-5">
                <span class="text-3xl font-semibold text-mono">
                    {{$openLeads ? $openLeads : '0'}}
                </span>
                <span class="text-sm font-normal text-secondary-foreground">
                    Open Leads
                </span>
            </div>
        </div>
    </a>

    <a href="{{ route('team.lead.index',['status' => base64_encode('2') ,'date' => isset($date) ? $date : '','branch' => isset($branchFilter) ? $branchFilter : '' ,'owner' => isset($userFilter) ? $userFilter : '']) }}" target="_blank">
        <div
            class="kt-card flex-col justify-between gap-6 h-full bg-cover rtl:bg-[left_top_-1.7rem] bg-[right_top_-1.7rem] bg-no-repeat channel-stats-bg">
            <i class="ki-filled ki-message-question w-7 mt-4 ms-5 text-4xl text-primary"></i>
            <div class="flex flex-col gap-1 pb-4 px-5">
                <span class="text-3xl font-semibold text-mono">
                    {{$closeLeads ? $closeLeads : '0'}}
                </span>
                <span class="text-sm font-normal text-secondary-foreground">
                    Close Leads
                </span>
            </div>
        </div>
    </a>
    <a href="{{ route('team.lead.index',['status' => base64_encode('3'),'date' => isset($date) ? $date : '','branch' => isset($branchFilter) ? $branchFilter : '' ,'owner' => isset($userFilter) ? $userFilter : '']) }}" target="_blank">
        <div
            class="kt-card flex-col justify-between gap-6 h-full bg-cover rtl:bg-[left_top_-1.7rem] bg-[right_top_-1.7rem] bg-no-repeat channel-stats-bg">
            <i class="ki-filled ki-security-user w-7 mt-4 ms-5 text-4xl text-primary"></i>
            <div class="flex flex-col gap-1 pb-4 px-5">
                <span class="text-3xl font-semibold text-mono">
                    {{$registerLeads ? $registerLeads : '0'}}
                </span>
                <span class="text-sm font-normal text-secondary-foreground">
                    Registered
                </span>
            </div>
        </div>
    </a>
</div>
