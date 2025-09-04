<!DOCTYPE html>
<html class="h-full" data-kt-theme="true" data-kt-theme-mode="light" dir="ltr" lang="en">
<head>
    <title>
        {{ $title }} | {{ $appData['companyName'] }}
    </title>
    <meta charset="utf-8" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta content="follow, index" name="robots" />
    <link href="works.html" rel="canonical" />
    <meta content="width=device-width, initial-scale=1, shrink-to-fit=no" name="viewport" />
    <link href="{{ $appData['companyFavicon'] }}" rel="shortcut icon" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&amp;display=swap" rel="stylesheet" />

    <link rel="stylesheet" href="{{ asset('assets/css/team/vendors/apexcharts.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/team/vendors/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/team/vendors/styles.bundle.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/team/styles.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/team/vendors/flatpickr.min.css') }}">

    @stack('styles')
</head>

@props([
    'title' => 'Dashboard',
    'breadcrumbs' => [],
    'showNotifications' => true,
    'showChat' => true,
    'showApps' => true,
    'showUserMenu' => true
])

<body class="antialiased flex h-full text-base text-foreground bg-background [--header-height-default:95px] data-kt-[sticky-header=on]:[--header-height:60px] [--header-height:var(--header-height-default)] [--header-height-mobile:70px]">
    <script>
        const defaultThemeMode = 'light';
        let themeMode;
        if (document.documentElement) {
            if (localStorage.getItem('kt-theme')) {
                themeMode = localStorage.getItem('kt-theme');
            } else if (
                document.documentElement.hasAttribute('data-kt-theme-mode')
            ) {
                themeMode =
                        document.documentElement.getAttribute('data-kt-theme-mode');
            } else {
                themeMode = defaultThemeMode;
            }

            if (themeMode === 'system') {
                themeMode = window.matchMedia('(prefers-color-scheme: dark)').matches
                        ? 'dark'
                        : 'light';
            }
            document.documentElement.classList.add(themeMode);
        }
    </script>
    <div class="flex grow flex-col in-data-kt-[sticky-header=on]:pt-(--header-height-default)">
        {{-- <x-team.navigation.sidebar :appData="$appData" /> --}}
    
        <x-team.layout.header
            :title="$title"
            :appData="$appData"
            :breadcrumbs="$breadcrumbs"
            :showNotifications="$showNotifications"
            :showChat="$showChat"
            :showApps="$showApps"
            :showUserMenu="$showUserMenu" />
        <main class="grow" id="content" role="content">
            <div class="kt-container-fixed" id="contentContainer">
            </div>
            <div class="kt-container-fixed">
                {{ $content }}
            </div>
        </main>
    </div>
    <!-- jQuery Full Version -->
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script src="https://cdn.datatables.net/2.3.2/js/dataTables.min.js"></script>
    <!-- Firebase App (Core SDK) -->
    <script src="https://www.gstatic.com/firebasejs/10.12.2/firebase-app-compat.js"></script>

    <!-- Firebase Messaging SDK (for FCM) -->
    <script src="https://www.gstatic.com/firebasejs/10.12.2/firebase-messaging-compat.js"></script>

    <script src="{{ asset('assets/js/team/core.bundle.js') }}"></script>
    <script src="{{ asset('assets/js/team/vendors/abstud.min.js') }}"></script>
    <script src="{{ asset('assets/js/team/vendors/general.js') }}"></script>
    <script src="{{ asset('assets/js/team/vendors/demo.js') }}"></script>
    <script src="{{ asset('assets/js/team/vendors/flatpickr.js') }}"></script>
    <script src="{{ asset('assets/js/team/vendors/select2.min.js') }}"></script>
    <script src="{{ asset('assets/js/app.js') }}"></script>
    <script src="{{ asset('assets/js/notifications.js') }}"></script>
    @stack('scripts')

    <script>
    const firebaseConfig = {
            apiKey: "AIzaSyDFauyP2JDBm8JYg_HYGXbcRPxJ3RibcxY",
            authDomain: "abstud-erp.firebaseapp.com",
            projectId: "abstud-erp",
            storageBucket: "abstud-erp.firebasestorage.app",
            messagingSenderId: "985069216195",
            appId: "1:985069216195:web:01e0d020c7169c80e8dd76"
        };

        firebase.initializeApp(firebaseConfig);
        const messaging = firebase.messaging();

        // fcm-token-handler.js
        function initializeFCM(userType = 'user') {
            if (Notification.permission === "granted") {
                getTokenAndStore(userType);
            } else {
                Notification.requestPermission().then(permission => {
                    if (permission === "granted") {
                        getTokenAndStore(userType);
                    }
                });
            }
        }

        function getTokenAndStore(userType = 'user') {
            messaging.getToken({ vapidKey: "BICuQTR9sllD9T9lT2X7pq4yj-AwuoC-Ty0ZAAjn7x4QvCKL269yTY3asvaEid5tCv0lnXiPYMlZdLQilNDblac" }).then((currentToken) => {
                if (currentToken && localStorage.getItem('stored_fcm_token') !== currentToken) {
                    // Save to DB
                    fetch('/save-fcm-token', {
                        method: 'POST',
                        headers: {
                            // 'Content-Type': 'application/json',
                            // 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        },
                        body: JSON.stringify({ fcm_token: currentToken, type: userType , '_token':document.querySelector('meta[name="csrf-token"]').content })
                    }).then(() => {
                        localStorage.setItem('stored_fcm_token', currentToken);
                    });
                }
            }).catch((err) => {
                console.error('FCM getToken error:', err);
            });
        }

        function clearFcmTokenOnLogout() {
            localStorage.removeItem('stored_fcm_token');

            // Optional: Also tell server to delete it from DB
            fetch('/remove-fcm-token', {
                method: 'POST',
                headers: {
                    // 'Content-Type': 'application/json',
                    // 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify({ '_token':document.querySelector('meta[name="csrf-token"]').content})
            });
        }

        document.addEventListener('DOMContentLoaded', () => {
            const logoutButton = document.querySelector('.logout_button');
            if (logoutButton) {
                logoutButton.addEventListener('click', clearFcmTokenOnLogout);
            }
        });


        messaging.onMessage(function(payload) {
            console.log('Foreground message:', payload);

            // const data = payload.notification || payload.data;

            // if (!data) return;

            // // Display notification popup if needed
            // new Notification(data.title, {
            //     body: data.body,
            //     icon: '/logo.png'
            // });

            // Optional: update your UI or notification list here
        });

        if ('requestIdleCallback' in window) {
            requestIdleCallback(() => {
                initializeFCM();
            });
        } else {
            // Fallback for older browsers
            setTimeout(() => {
                initializeFCM();
            }, 1000);
        }

    </script>

    <script>

        document.addEventListener('DOMContentLoaded', function() {
            // Global Toast Handler for Laravel Session Messages
            function initGlobalToasts() {
                // Handle success messages
                @if(session('success'))
                    KTToast.show({
                        message: "{{ session('success') }}",
                        icon: '<i class="ki-filled ki-check text-success text-xl"></i>',
                        progress: true,
                        pauseOnHover: true,
                        variant: "success",
                        duration: 5000
                    });
                @endif

                // Handle error messages
                @if(session('error'))
                    KTToast.show({
                        message: "{{ session('error') }}",
                        icon: '<i class="ki-filled ki-information-4 text-danger text-xl"></i>',
                        progress: true,
                        pauseOnHover: true,
                        variant: "destructive",
                        duration: 7000
                    });
                @endif

                // Handle warning messages
                @if(session('warning'))
                    KTToast.show({
                        message: "{{ session('warning') }}",
                        icon: '<i class="ki-filled ki-information-2 text-warning text-xl"></i>',
                        progress: true,
                        pauseOnHover: true,
                        variant: "warning",
                        duration: 6000
                    });
                @endif

                // Handle info messages
                @if(session('info'))
                    KTToast.show({
                        message: "{{ session('info') }}",
                        icon: '<i class="ki-filled ki-information text-info text-xl"></i>',
                        progress: true,
                        pauseOnHover: true,
                        variant: "info",
                        duration: 5000
                    });
                @endif

                // Handle validation errors
                @if($errors->any())
                    @foreach($errors->all() as $error)
                        KTToast.show({
                            message: "{{ $error }}",
                            icon: '<i class="ki-filled ki-information-4 text-warning text-xl"></i>',
                            progress: true,
                            pauseOnHover: true,
                            variant: "warning",
                            duration: 6000
                        });
                    @endforeach
                @endif
            }
            initGlobalToasts();
        });
    </script>



    <div class="kt-modal" data-kt-modal="true" id="search_modal">
    <div class="kt-modal-content max-w-[600px] top-[15%]">
        <div class="kt-modal-header py-4 px-5">
            <i class="ki-filled ki-magnifier text-muted-foreground text-xl"></i>
            <input class="kt-input kt-input-ghost" id="search_input" name="query" placeholder="Tap to start search" type="text" />
            <button class="kt-btn kt-btn-sm kt-btn-icon kt-btn-dim shrink-0" data-kt-modal-dismiss="true">
                <i class="ki-filled ki-cross"></i>
            </button>
        </div>

        <div class="kt-modal-body p-0 pb-5">
            <div class="kt-scrollable-y-auto max-h-[300px] overflow-y-auto" id="search_scroll_area">
                <div id="search_modal_mixed">
                    {{-- Default content --}}
                    @include('team.comman-search.search_results', ['leads' => $defaultLeads ?? []])
                </div>
            </div>
        </div>
    </div>
</div>



</body>
</html>


<script>
    $(document).ready(function () {
        let debounceTimeout;
        $('#search_input').on('input', function () {
            clearTimeout(debounceTimeout);
            let query = $(this).val().trim();
            debounceTimeout = setTimeout(function () {
                if (query.length > 0) {
                    $.ajax({
                        url: '{{ route("team.leads.search") }}',
                        method: 'GET',
                        data: { query: query },
                        success: function (response) {
                            $('#search_modal_mixed').html(response);
                        },
                        error: function () {
                            $('#search_modal_mixed').html('<div class="p-4 text-red-500">Something went wrong!</div>');
                        }
                    });
                } else {
                    $('#search_modal_mixed').html('');
                }
            }, 300);
        });
    });
</script>

