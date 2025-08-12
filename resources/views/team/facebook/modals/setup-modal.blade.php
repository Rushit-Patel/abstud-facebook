<!-- Facebook Setup Modal -->
<x-team.modal 
    id="facebook-setup-modal" 
    size="lg"
    centered="true"
    backdrop="static"
    keyboard="false">
    
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                </svg>
            </div>
            <div>
                <h2 class="text-xl font-semibold text-gray-900">Setup Facebook Integration</h2>
                <p class="text-sm text-gray-600">Connect your Facebook business account in 3 easy steps</p>
            </div>
        </div>
    </x-slot>

    <!-- Step Progress Indicator -->
    <div class="mb-8">
        <div class="flex items-center justify-center">
            <div class="flex items-center space-x-4">
                <!-- Step 1 -->
                <div class="flex items-center">
                    <div class="step-circle w-10 h-10 bg-blue-600 text-white rounded-full flex items-center justify-center font-semibold" data-step="1">1</div>
                    <span class="ml-3 text-sm font-medium text-gray-900 hidden sm:block">Business Info</span>
                </div>
                
                <!-- Connector -->
                <div class="w-16 h-0.5 bg-gray-300 step-connector" data-step="1"></div>
                
                <!-- Step 2 -->
                <div class="flex items-center">
                    <div class="step-circle w-10 h-10 bg-gray-300 text-gray-600 rounded-full flex items-center justify-center font-semibold" data-step="2">2</div>
                    <span class="ml-3 text-sm font-medium text-gray-500 hidden sm:block">Connect Facebook</span>
                </div>
                
                <!-- Connector -->
                <div class="w-16 h-0.5 bg-gray-300 step-connector" data-step="2"></div>
                
                <!-- Step 3 -->
                <div class="flex items-center">
                    <div class="step-circle w-10 h-10 bg-gray-300 text-gray-600 rounded-full flex items-center justify-center font-semibold" data-step="3">3</div>
                    <span class="ml-3 text-sm font-medium text-gray-500 hidden sm:block">Setup Complete</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Step Content Container -->
    <div id="setup-content">
        
        <!-- Step 1: Business Information -->
        <div class="setup-step active" data-step="1">
            <div class="space-y-6">
                <div class="text-center mb-6">
                    <h3 class="text-lg font-semibold text-gray-900">Business Information</h3>
                    <p class="text-sm text-gray-600">Tell us about your business to get started</p>
                </div>

                <form id="business-info-form">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <x-team.forms.input 
                            name="business_name" 
                            label="Business Name" 
                            placeholder="Enter your business name"
                            required="true" />
                            
                        <x-team.forms.input 
                            name="business_email" 
                            type="email"
                            label="Business Email" 
                            placeholder="business@example.com"
                            required="true" />
                    </div>

                    <div class="mt-6">
                        <x-team.forms.input 
                            name="business_website" 
                            type="url"
                            label="Business Website" 
                            placeholder="https://yourwebsite.com"
                            required="false" />
                    </div>

                    <div class="mt-6">
                        <x-team.forms.textarea 
                            name="business_description" 
                            label="Business Description" 
                            placeholder="Brief description of your business..."
                            rows="3"
                            required="false" />
                    </div>
                </form>
            </div>
        </div>

        <!-- Step 2: Connect Facebook -->
        <div class="setup-step" data-step="2">
            <div class="space-y-6">
                <div class="text-center mb-6">
                    <h3 class="text-lg font-semibold text-gray-900">Connect Facebook Account</h3>
                    <p class="text-sm text-gray-600">Authorize access to your Facebook business account</p>
                </div>

                <!-- Facebook Connect Button -->
                <div class="flex flex-col items-center gap-6">
                    <div class="w-20 h-20 bg-blue-50 rounded-full flex items-center justify-center">
                        <svg class="w-10 h-10 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                        </svg>
                    </div>

                    <div class="text-center">
                        <p class="text-sm text-gray-600 mb-4">
                            Click the button below to connect your Facebook business account.
                            <br>You'll be redirected to Facebook to authorize the connection.
                        </p>
                        
                        <button id="facebook-connect-btn" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-8 rounded-lg transition-colors duration-200 flex items-center justify-center space-x-3 mx-auto">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                            </svg>
                            <span>Connect with Facebook</span>
                        </button>
                    </div>

                    <!-- Connection Status -->
                    <div id="connection-status" class="hidden w-full max-w-md">
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="ml-3 text-sm font-medium text-green-800">Successfully connected to Facebook!</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Step 3: Setup Complete -->
        <div class="setup-step" data-step="3">
            <div class="space-y-6">
                <div class="text-center">
                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Setup Complete!</h3>
                    <p class="text-gray-600 mb-6">
                        Your Facebook integration is now active and ready to receive leads.
                    </p>
                </div>

                <!-- Summary Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                            </svg>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-blue-900">Facebook Connected</p>
                                <p class="text-xs text-blue-700">Real-time lead sync active</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                        <div class="flex items-center">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-green-900">Webhooks Active</p>
                                <p class="text-xs text-green-700">Instant lead notifications</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Next Steps -->
                <div class="bg-gray-50 rounded-lg p-6">
                    <h4 class="font-semibold text-gray-900 mb-3">What's Next?</h4>
                    <ul class="space-y-2 text-sm text-gray-600">
                        <li class="flex items-center">
                            <svg class="w-4 h-4 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4"></path>
                            </svg>
                            Configure your lead forms and mapping rules
                        </li>
                        <li class="flex items-center">
                            <svg class="w-4 h-4 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4"></path>
                            </svg>
                            Set up email notifications and automations
                        </li>
                        <li class="flex items-center">
                            <svg class="w-4 h-4 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4"></path>
                            </svg>
                            Test your integration with a sample lead
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <x-slot name="footer">
        <div class="flex items-center justify-between w-full">
            <!-- Previous Button -->
            <button id="prev-step-btn" class="hidden bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium py-2 px-4 rounded-lg transition-colors duration-200">
                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Previous
            </button>

            <div class="flex gap-3 ml-auto">
                <!-- Cancel Button -->
                <button data-kt-modal-dismiss="#facebook-setup-modal" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium py-2 px-4 rounded-lg transition-colors duration-200">
                    Cancel
                </button>
                
                <!-- Next/Complete Button -->
                <button id="next-step-btn" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-6 rounded-lg transition-colors duration-200">
                    Next Step
                    <svg class="w-4 h-4 inline ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </button>
            </div>
        </div>
    </x-slot>
</x-team.modal>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let currentStep = 1;
    const totalSteps = 3;

    // Step navigation functions
    function updateStepDisplay() {
        // Update step circles and connectors
        for (let i = 1; i <= totalSteps; i++) {
            const circle = document.querySelector(`.step-circle[data-step="${i}"]`);
            const connector = document.querySelector(`.step-connector[data-step="${i}"]`);
            
            if (i <= currentStep) {
                circle.classList.remove('bg-gray-300', 'text-gray-600');
                circle.classList.add('bg-blue-600', 'text-white');
                if (connector) {
                    connector.classList.remove('bg-gray-300');
                    connector.classList.add('bg-blue-600');
                }
            } else {
                circle.classList.remove('bg-blue-600', 'text-white');
                circle.classList.add('bg-gray-300', 'text-gray-600');
                if (connector) {
                    connector.classList.remove('bg-blue-600');
                    connector.classList.add('bg-gray-300');
                }
            }
        }

        // Show/hide step content
        document.querySelectorAll('.setup-step').forEach(step => {
            step.classList.remove('active');
        });
        document.querySelector(`.setup-step[data-step="${currentStep}"]`).classList.add('active');

        // Update buttons
        const prevBtn = document.getElementById('prev-step-btn');
        const nextBtn = document.getElementById('next-step-btn');
        
        if (currentStep === 1) {
            prevBtn.classList.add('hidden');
        } else {
            prevBtn.classList.remove('hidden');
        }

        if (currentStep === totalSteps) {
            nextBtn.innerHTML = `
                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                Complete Setup
            `;
        } else {
            nextBtn.innerHTML = `
                Next Step
                <svg class="w-4 h-4 inline ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            `;
        }
    }

    // Previous step button
    document.getElementById('prev-step-btn').addEventListener('click', function() {
        if (currentStep > 1) {
            currentStep--;
            updateStepDisplay();
        }
    });

    // Next step button
    document.getElementById('next-step-btn').addEventListener('click', function() {
        if (currentStep < totalSteps) {
            if (validateCurrentStep()) {
                currentStep++;
                updateStepDisplay();
            }
        } else {
            // Complete setup
            completeSetup();
        }
    });

    // Facebook connect button
    document.getElementById('facebook-connect-btn').addEventListener('click', function() {
        // Simulate Facebook connection process
        this.innerHTML = `
            <svg class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="m4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span>Connecting...</span>
        `;
        
        // In real implementation, this would redirect to Facebook OAuth
        setTimeout(() => {
            this.innerHTML = `
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                </svg>
                <span>Connected</span>
            `;
            this.disabled = true;
            this.classList.add('bg-green-600', 'hover:bg-green-700');
            this.classList.remove('bg-blue-600', 'hover:bg-blue-700');
            
            document.getElementById('connection-status').classList.remove('hidden');
        }, 2000);
    });

    function validateCurrentStep() {
        if (currentStep === 1) {
            const form = document.getElementById('business-info-form');
            const requiredFields = form.querySelectorAll('input[required]');
            for (let field of requiredFields) {
                if (!field.value.trim()) {
                    alert('Please fill in all required fields.');
                    field.focus();
                    return false;
                }
            }
        } else if (currentStep === 2) {
            const connectBtn = document.getElementById('facebook-connect-btn');
            if (!connectBtn.disabled) {
                alert('Please connect your Facebook account first.');
                return false;
            }
        }
        return true;
    }

    function completeSetup() {
        // Here you would make an AJAX call to save the setup
        const formData = new FormData(document.getElementById('business-info-form'));
        
        // Show loading state
        const nextBtn = document.getElementById('next-step-btn');
        nextBtn.innerHTML = `
            <svg class="animate-spin w-4 h-4 inline mr-2" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="m4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Completing Setup...
        `;
        nextBtn.disabled = true;

        // Simulate API call
        setTimeout(() => {
            // Close modal and redirect to dashboard
            location.reload(); // This will show the connected state
        }, 2000);
    }

    // Initialize step display
    updateStepDisplay();
});
</script>

<style>
.setup-step {
    display: none;
}
.setup-step.active {
    display: block;
}
</style>
