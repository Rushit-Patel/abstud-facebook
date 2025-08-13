@php
    $breadcrumbs = [
        ['title' => 'Home', 'url' => route('team.dashboard')],
        ['title' => 'Facebook Integration', 'url' => route('facebook.dashboard')],
        ['title' => 'Webhook Settings']
    ];
@endphp

<x-team.layout.app title="Facebook Webhook Settings" :breadcrumbs="$breadcrumbs">
    <x-slot name="content">
        <div class="kt-container-fixed">
            <!-- Header Section -->
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-blue-600 rounded-xl flex items-center justify-center shadow-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Facebook Webhook Settings</h1>
                        <p class="text-gray-600">Configure webhook for real-time lead notifications</p>
                    </div>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('facebook.dashboard') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium py-2 px-4 rounded-lg transition-colors">
                        Back to Dashboard
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Configuration Panel -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        @if($isConfigured)
                            <div class="flex items-center gap-3 p-4 bg-green-50 border border-green-200 rounded-lg mb-6">
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span class="text-green-800 font-medium">Webhook is properly configured!</span>
                            </div>
                        @else
                            <div class="flex items-center gap-3 p-4 bg-yellow-50 border border-yellow-200 rounded-lg mb-6">
                                <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.728-.833-2.5 0L4.268 6.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                </svg>
                                <span class="text-yellow-800 font-medium">Webhook is not configured. Please set the required environment variables.</span>
                            </div>
                        @endif

                        <div class="space-y-6">
                            <!-- Webhook URL -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Webhook URL</label>
                                <div class="flex rounded-lg border border-gray-300 bg-gray-50">
                                    <input type="text" 
                                           class="flex-1 block w-full px-3 py-2 border-0 bg-transparent text-gray-900 placeholder-gray-500 focus:outline-none" 
                                           value="{{ $webhookUrl ? url($webhookUrl) : 'Not configured' }}" 
                                           readonly>
                                    @if($webhookUrl)
                                        <button type="button" 
                                                onclick="copyToClipboard('{{ url($webhookUrl) }}')" 
                                                class="px-3 py-2 text-gray-500 hover:text-gray-700 border-l border-gray-300">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                            </svg>
                                        </button>
                                    @endif
                                </div>
                                <p class="mt-1 text-sm text-gray-500">Set FACEBOOK_WEBHOOK_URL in your .env file</p>
                            </div>

                            <!-- Verify Token -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Verify Token</label>
                                <div class="flex rounded-lg border border-gray-300 bg-gray-50">
                                    <input type="password" 
                                           class="flex-1 block w-full px-3 py-2 border-0 bg-transparent text-gray-900 placeholder-gray-500 focus:outline-none" 
                                           value="{{ $verifyToken ? str_repeat('*', strlen($verifyToken)) : 'Not configured' }}" 
                                           readonly>
                                    @if($verifyToken)
                                        <button type="button" 
                                                onclick="copyToClipboard('{{ $verifyToken }}')" 
                                                class="px-3 py-2 text-gray-500 hover:text-gray-700 border-l border-gray-300">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                            </svg>
                                        </button>
                                    @endif
                                </div>
                                <p class="mt-1 text-sm text-gray-500">Set FACEBOOK_WEBHOOK_VERIFY_TOKEN in your .env file</p>
                            </div>

                            <!-- Actions -->
                            <div class="flex gap-3">
                                @if($isConfigured)
                                    <form method="POST" action="{{ route('facebook.webhook-settings.test') }}">
                                        @csrf
                                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition-colors">
                                            Test Webhook
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Side Panel -->
                <div class="space-y-6">
                    <!-- Environment Configuration -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Environment Configuration</h3>
                        <p class="text-gray-600 text-sm mb-4">Configure webhook settings in your <code class="bg-gray-100 px-1 py-0.5 rounded text-xs">.env</code> file:</p>
                        
                        <div class="bg-gray-900 rounded-lg p-4 text-sm">
                            <code class="text-green-400">
                                FACEBOOK_WEBHOOK_URL<span class="text-white">=</span><span class="text-yellow-300">"/facebook/webhook"</span><br>
                                FACEBOOK_WEBHOOK_VERIFY_TOKEN<span class="text-white">=</span><span class="text-yellow-300">"your_secure_token_here"</span>
                            </code>
                        </div>

                        <div class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                            <p class="text-blue-800 text-sm">
                                <strong>Note:</strong> After updating your .env file, restart your application server to apply the changes.
                            </p>
                        </div>
                    </div>

                    <!-- Facebook App Configuration -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Facebook App Settings</h3>
                        <p class="text-gray-600 text-sm mb-4">Configure in your Facebook App:</p>
                        <ol class="text-sm text-gray-700 space-y-2 list-decimal list-inside">
                            <li>Go to Products → Webhooks</li>
                            <li>Add webhook URL: <code class="bg-gray-100 px-1 py-0.5 rounded text-xs">{{ $webhookUrl ? url($webhookUrl) : 'Configure webhook URL first' }}</code></li>
                            <li>Add verify token: <code class="bg-gray-100 px-1 py-0.5 rounded text-xs">{{ $verifyToken ? 'Your configured token' : 'Configure token first' }}</code></li>
                            <li>Subscribe to lead events</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <script>
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(function() {
                alert('Copied to clipboard!');
            }).catch(function(err) {
                console.error('Could not copy text: ', err);
                alert('Could not copy text');
            });
        }
        </script>
    </x-slot>
</x-team.layout.app>
