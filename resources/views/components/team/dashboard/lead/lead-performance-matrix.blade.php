<div class="grid grid-cols-2 gap-5 lg:gap-7.5 h-full items-stretch">
    <div
        class="kt-card flex flex-col items-center justify-center gap-5 h-full bg-cover bg-no-repeat channel-stats-bg rtl:bg-[left_top_-1.7rem] bg-[right_top_-1.7rem]">
        <!-- Card Title and Percentage -->
        <div
            class="flex flex-wrap items-center lg:items-end justify-between gap-5 w-full px-5 mt-5">
            <h6 class="text-gray-900 text-xl">Inquiries</h6>
            {{-- <h4 class="font-semibold mb-0 text-green-600">+26.5%</h4> --}}
            <h4 class="font-semibold mb-0 {{ ($percentage['total'] ?? 0) >= 0 ? 'text-green-600' : 'text-destructive' }}">
                {{ ($percentage['total'] ?? 0) > 0 ? '+' : '' }}{{ $percentage['total'] ?? 0 }}%
            </h4>
        </div>
        <!-- Data Rows -->
        <div class="flex flex-col gap-2 w-full px-5">
            <div class="flex items-center gap-2 w-full">
                <span class="block w-2 h-2 bg-primary rounded-full"></span>
                <h6 class="text-sm text-muted mb-0">{{$thisMonthData['label']}}</h6>
                <h6 class="text-sm mb-0 ms-auto text-muted">
                    <a href="{{ route('team.lead.index',['date' => isset($thisMonthData['this_month_date']) ? $thisMonthData['this_month_date'] : '','branch' => isset($branchFilter) ? $branchFilter : '']) }}" >
                        {{$thisMonthData['data']->total ?? 0}}
                    </a>
                </h6>
            </div>
            <div class="flex items-center gap-2 w-full mb-4">
                <span class="block w-2 h-2 bg-accent/50 rounded-full "></span>
                <h6 class="text-sm text-muted mb-0">{{$previousMonthData['label']}}</h6>
                <h6 class="text-sm mb-0 ms-auto text-muted">
                    <a href="{{ route('team.lead.index',['date' => isset($previousMonthData['previous_month_date']) ? $previousMonthData['previous_month_date'] : '','branch' => isset($branchFilter) ? $branchFilter : '']) }}" >
                        {{$previousMonthData['data']->total ?? 0 }}
                    </a>
                </h6>
            </div>
        </div>
    </div>

    <div
        class="kt-card flex flex-col items-center justify-center gap-5 h-full bg-cover bg-no-repeat channel-stats-bg rtl:bg-[left_top_-1.7rem] bg-[right_top_-1.7rem]">
        <!-- Card Title and Percentage -->
        <div
            class="flex flex-wrap items-center lg:items-end justify-between gap-5 w-full px-5 mt-5">
            <h6 class="text-gray-900 text-xl">Close Inquiry</h6>
            {{-- <h4 class="font-semibold mb-0 text-destructive">+26.5%</h4> --}}
            <h4 class="font-semibold mb-0 {{ ($percentage['close'] ?? 0) >= 0 ? 'text-green-600' : 'text-destructive' }}">
                {{ ($percentage['close'] ?? 0) > 0 ? '+' : '' }}{{ $percentage['close'] ?? 0 }}%
            </h4>
        </div>
        <!-- Data Rows -->
        <div class="flex flex-col gap-2 w-full px-5">
            <div class="flex items-center gap-2 w-full">
                <span class="block w-2 h-2 bg-primary rounded-full"></span>
                <h6 class="text-sm text-muted mb-0">{{$thisMonthData['label']}}</h6>
                <h6 class="text-sm mb-0 ms-auto text-muted">
                    <a href="{{ route('team.lead.index',['status' => base64_encode('2') , 'date' => isset($thisMonthData['this_month_date']) ? $thisMonthData['this_month_date'] : '','branch' => isset($branchFilter) ? $branchFilter : '']) }}" >
                        {{$thisMonthData['data']->close ?? 0}}
                    </a>
                    </h6>
            </div>
            <div class="flex items-center gap-2 w-full mb-4">
                <span class="block w-2 h-2 bg-accent/50 rounded-full "></span>
                <h6 class="text-sm text-muted mb-0">{{$previousMonthData['label']}}</h6>
                <h6 class="text-sm mb-0 ms-auto text-muted">
                    <a href="{{ route('team.lead.index',['status' => base64_encode('2') , 'date' => isset($previousMonthData['previous_month_date']) ? $previousMonthData['previous_month_date'] : '','branch' => isset($branchFilter) ? $branchFilter : '']) }}" >
                        {{$previousMonthData['data']->close ?? 0}}
                    </a>
                </h6>
            </div>
        </div>
    </div>
    <div
        class="kt-card flex flex-col items-center justify-center gap-5 h-full bg-cover bg-no-repeat channel-stats-bg rtl:bg-[left_top_-1.7rem] bg-[right_top_-1.7rem]">
        <!-- Card Title and Percentage -->
        <div class="flex  items-center lg:items-end justify-between gap-5 w-full px-5 mt-5">
            <h6 class="text-gray-900 " style="font-size: 17px;">Coaching Registration</h6>
            {{-- <h4 class="font-semibold mb-0 text-green-600 mx-2">+16.5%</h4> --}}
            <h4 class="font-semibold mb-0 {{ ($percentage['register_coaching'] ?? 0) >= 0 ? 'text-green-600' : 'text-destructive' }}">
                {{ ($percentage['register_coaching'] ?? 0) > 0 ? '+' : '' }}{{ $percentage['register_coaching'] ?? 0 }}%
            </h4>
        </div>
        <!-- Data Rows -->
        <div class="flex flex-col gap-2 w-full px-5">
            <div class="flex items-center gap-2 w-full">
                <span class="block w-2 h-2 bg-primary rounded-full"></span>
                <h6 class="text-sm text-muted mb-0">{{$thisMonthData['label']}}</h6>
                <h6 class="text-sm mb-0 ms-auto text-muted">
                    <a href="{{ route('team.lead.index',['status' => base64_encode('3') ,'purpose' => base64_encode('2') , 'date' => isset($thisMonthData['this_month_date']) ? $thisMonthData['this_month_date'] : '','branch' => isset($branchFilter) ? $branchFilter : '']) }}" >
                        {{$thisMonthData['data']->register_coaching ?? 0}}
                    </a>
                </h6>
            </div>
            <div class="flex items-center gap-2 w-full mb-4">
                <span class="block w-2 h-2 bg-accent/50 rounded-full "></span>
                <h6 class="text-sm text-muted mb-0">{{$previousMonthData['label']}}</h6>
                <h6 class="text-sm mb-0 ms-auto text-muted">
                    <a href="{{ route('team.lead.index',['status' => base64_encode('3') ,'purpose' => base64_encode('2') , 'date' => isset($previousMonthData['previous_month_date']) ? $previousMonthData['previous_month_date'] : '','branch' => isset($branchFilter) ? $branchFilter : '']) }}" >
                        {{$previousMonthData['data']->register_coaching ?? 0}}
                    </a>
                </h6>
            </div>
        </div>
    </div>
    <div
        class="kt-card flex flex-col items-center justify-center gap-5 h-full bg-cover bg-no-repeat channel-stats-bg rtl:bg-[left_top_-1.7rem] bg-[right_top_-1.7rem]">
        <!-- Card Title and Percentage -->
        <div class="flex  items-center lg:items-end justify-between gap-5 w-full px-5 mt-5">
            <h6 class="text-gray-900 " style="font-size: 17px;">Application Registration
            </h6>
            {{-- <h4 class="font-semibold mb-0 text-destructive mx-2">+46.5%</h4> --}}
            <h4 class="font-semibold mb-0 {{ ($percentage['register_application'] ?? 0) >= 0 ? 'text-green-600' : 'text-destructive' }}">
                {{ ($percentage['register_application'] ?? 0) > 0 ? '+' : '' }}{{ $percentage['register_application'] ?? 0 }}%
            </h4>
        </div>
        <!-- Data Rows -->
        <div class="flex flex-col gap-2 w-full px-5">
            <div class="flex items-center gap-2 w-full">
                <span class="block w-2 h-2 bg-primary rounded-full"></span>
                <h6 class="text-sm text-muted mb-0">{{$thisMonthData['label']}}</h6>
                <h6 class="text-sm mb-0 ms-auto text-muted">
                    <a href="{{ route('team.lead.index',['status' => base64_encode('3') ,'purpose' => base64_encode('1') , 'date' => isset($thisMonthData['this_month_date']) ? $thisMonthData['this_month_date'] : '','branch' => isset($branchFilter) ? $branchFilter : '']) }}" >
                        {{$thisMonthData['data']->register_application ?? 0}}
                    </a>
                </h6>
            </div>
            <div class="flex items-center gap-2 w-full mb-4">
                <span class="block w-2 h-2 bg-accent/50 rounded-full "></span>
                <h6 class="text-sm text-muted mb-0">{{$previousMonthData['label']}}</h6>
                <h6 class="text-sm mb-0 ms-auto text-muted">
                    <a href="{{ route('team.lead.index',['status' => base64_encode('3') ,'purpose' => base64_encode('1') , 'date' => isset($previousMonthData['previous_month_date']) ? $previousMonthData['previous_month_date'] : '','branch' => isset($branchFilter) ? $branchFilter : '']) }}" >
                        {{$previousMonthData['data']->register_application ?? 0}}
                    </a>
                </h6>
            </div>
        </div>
    </div>
</div>
