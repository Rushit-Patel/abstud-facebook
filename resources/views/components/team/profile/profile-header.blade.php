<div class=" border border-gray-200 rounded-lg shadow-sm mb-6 overflow-hidden hero-bg">
    <div class="bg-gradient-to-r from-slate-50 to-blue-50 px-4 py-4 ">
        <div class="flex items-center gap-6">
            <!-- Avatar -->
            <div class="flex-shrink-0">

                <!-- Avatar Display with Clickable Upload -->
                <!-- Avatar Upload UI -->
                <div class="w-20 h-20 rounded-full shadow-lg overflow-hidden bg-primary flex items-center justify-center text-white text-2xl font-semibold cursor-pointer relative group" id="avatar-wrapper">
                    <!-- Avatar Preview -->
                    <div id="avatar-preview" class="w-full h-full">
                        @if ($client->client_profile_photo && Storage::disk('public')->exists($client->client_profile_photo))
                            <img src="{{ Storage::url($client->client_profile_photo) }}" alt="Profile Image" class="w-full h-full object-cover rounded-full">
                        @else
                            <span class="flex items-center justify-center w-full h-full">
                                {{ strtoupper(substr($client->first_name, 0, 1)) }}{{ strtoupper(substr($client->last_name, 0, 1)) }}
                            </span>
                        @endif
                        <div class="flex items-center justify-center cursor-pointer h-5 left-0 right-0 bottom-0 bg-black/25 absolute">
                            <i class="ki-filled ki-camera text-white text-lg"></i>
                        </div>
                    </div>

                    <!-- File Upload Form (hidden but clickable) -->
                    <form id="avatar-upload-form" enctype="multipart/form-data" class="absolute inset-0 opacity-0 cursor-pointer">
                        @csrf
                        <input type="hidden" name="client_id" value="{{ $client->id }}">
                        <input type="file" name="profile_image" id="avatar-input" accept="image/*" class="w-full h-full cursor-pointer">
                    </form>
                </div>

            </div>

            <!-- Client Info -->
            <div class="flex-grow">
                <h1 class="text-2xl font-bold text-gray-900 mb-1">
                    {{ $client->first_name }} {{ $client->middle_name }} {{ $client->last_name }}
                </h1>
                <div class="flex flex-wrap gap-4 text-sm text-gray-600">
                    <span class="flex items-center gap-1">
                        <i class="ki-filled ki-sms text-gray-400"></i>
                        {{ $client->email_id }}
                    </span>
                    <span class="flex items-center gap-1">
                        <i class="ki-filled ki-phone text-gray-400"></i>
                        +{{ $client->country_code }} {{ $client->mobile_no }}
                    </span>
                    <span class="flex items-center gap-1">
                        <i class="ki-filled ki-geolocation text-gray-400"></i>
                        {{ $client?->getCountry?->name }}, {{ $client->getState?->name }}, {{ $client->getCity?->name }}
                    </span>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex-shrink-0 flex flex-cloumn gap-2">
                <button class="kt-btn kt-btn-sm kt-btn-primary">
                    <i class="ki-filled ki-phone mr-1"></i>
                    Call
                </button>
                <button class="kt-btn kt-btn-sm kt-btn-secondary">
                    <i class="ki-filled ki-message-text-2 mr-1"></i>
                    Message
                </button>
            </div>
        </div>
    </div>
</div>
{{--
<!-- Tab Navigation -->
@
<div class="bg-white border border-gray-200 rounded-lg shadow-sm mb-6">
    <div class="border-b border-gray-200">
        <nav class="flex space-x-8 px-6" role="tablist">
            <button class="tab-link active relative py-4 px-1 text-sm font-medium text-gray-900 whitespace-nowrap border-b-2 border-primary"
                    data-tab="overview" role="tab">
                <i class="ki-filled ki-user text-sm mr-2"></i>
                Overview
            </button>
            <button class="tab-link relative py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 whitespace-nowrap border-b-2 border-transparent hover:border-gray-300"
                    data-tab="documents" role="tab">
                <i class="ki-filled ki-document text-sm mr-2"></i>
                Documents
            </button>
            <button class="tab-link relative py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 whitespace-nowrap border-b-2 border-transparent hover:border-gray-300"
                    data-tab="registrations" role="tab">
                <i class="ki-filled ki-notepad-edit text-sm mr-2"></i>
                Registrations
            </button>
            <button class="tab-link relative py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 whitespace-nowrap border-b-2 border-transparent hover:border-gray-300"
                    data-tab="coaching" role="tab">
                <i class="ki-filled ki-teacher text-sm mr-2"></i>
                Coaching
            </button>
            <button class="tab-link relative py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 whitespace-nowrap border-b-2 border-transparent hover:border-gray-300"
                    data-tab="student-visa" role="tab">
                <i class="ki-filled ki-passport text-sm mr-2"></i>
                Student Visa
            </button>
            <button class="tab-link relative py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 whitespace-nowrap border-b-2 border-transparent hover:border-gray-300"
                    data-tab="visitor-visa" role="tab">
                <i class="ki-filled ki-airplane text-sm mr-2"></i>
                Visitor Visa
            </button>
            <button class="tab-link relative py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 whitespace-nowrap border-b-2 border-transparent hover:border-gray-300"
                    data-tab="tracking" role="tab">
                <i class="ki-filled ki-delivery-3 text-sm mr-2"></i>
                Tracking
            </button>
        </nav>
    </div>
</div> --}}


@php
    $url = request()->path();
@endphp

<div class="bg-white border border-gray-200 rounded-lg shadow-sm mb-6">
    <div class="border-b border-gray-200">
        <nav class="flex space-x-8 px-6" role="tablist">

            <a href="{{ route('team.client.show', $client->id) }}" class="tab-link relative py-4 px-1 text-sm font-medium whitespace-nowrap border-b-2
                {{ request()->is('team/client/*') ? 'text-gray-900 border-primary' : 'text-gray-500 hover:text-gray-700 border-transparent hover:border-gray-300' }}"
                data-tab="overview" role="tab">
                <i class="ki-filled ki-user text-sm mr-2"></i>
                Overview
            </a>

            <a href="{{ route('team.document', $client->id) }}" class="tab-link relative py-4 px-1 text-sm font-medium whitespace-nowrap border-b-2
                {{ request()->is('team/document/*') ? 'text-gray-900 border-primary' : 'text-gray-500 hover:text-gray-700 border-transparent hover:border-gray-300' }}"
                data-tab="documents" role="tab">
                <i class="ki-filled ki-document text-sm mr-2"></i>
                Documents
            </button>

            <a href="{{ route('team.get.registrations', $client->id) }}" class="tab-link relative py-4 px-1 text-sm font-medium whitespace-nowrap border-b-2
                {{ request()->is('team/registrations/*') ? 'text-gray-900 border-primary' : 'text-gray-500 hover:text-gray-700 border-transparent hover:border-gray-300' }}"
                data-tab="registrations" role="tab">
                <i class="ki-filled ki-notepad-edit text-sm mr-2"></i>
                Registrations
            </a>

            <a href="{{ route('team.get.coaching', $client->id) }}" class="tab-link relative py-4 px-1 text-sm font-medium whitespace-nowrap border-b-2
                {{ request()->is('team/coaching/*') ? 'text-gray-900 border-primary' : 'text-gray-500 hover:text-gray-700 border-transparent hover:border-gray-300' }}"
                data-tab="coaching" role="tab">
                <i class="ki-filled ki-teacher text-sm mr-2"></i>
                Coaching
            </a>

            <button class="tab-link relative py-4 px-1 text-sm font-medium whitespace-nowrap border-b-2
                {{ request()->is('team/student-visa/*') ? 'text-gray-900 border-primary' : 'text-gray-500 hover:text-gray-700 border-transparent hover:border-gray-300' }}"
                data-tab="student-visa" role="tab">
                <i class="ki-filled ki-passport text-sm mr-2"></i>
                Student Visa
            </button>

            <button class="tab-link relative py-4 px-1 text-sm font-medium whitespace-nowrap border-b-2
                {{ request()->is('team/visitor-visa/*') ? 'text-gray-900 border-primary' : 'text-gray-500 hover:text-gray-700 border-transparent hover:border-gray-300' }}"
                data-tab="visitor-visa" role="tab">
                <i class="ki-filled ki-airplane text-sm mr-2"></i>
                Visitor Visa
            </button>

            <button class="tab-link relative py-4 px-1 text-sm font-medium whitespace-nowrap border-b-2
                {{ request()->is('team/tracking/*') ? 'text-gray-900 border-primary' : 'text-gray-500 hover:text-gray-700 border-transparent hover:border-gray-300' }}"
                data-tab="tracking" role="tab">
                <i class="ki-filled ki-delivery-3 text-sm mr-2"></i>
                Tracking
            </button>

        </nav>
    </div>
</div>
