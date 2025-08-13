@extends('layouts.team')

@section('title', 'Facebook Webhook Settings')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Facebook Webhook Configuration</h5>
                </div>
                <div class="card-body">
                    @if($isConfigured)
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle me-2"></i>
                            Webhook is properly configured!
                        </div>
                    @else
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Webhook is not configured. Please set the required environment variables.
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Webhook URL</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" value="{{ $webhookUrl ? url($webhookUrl) : 'Not configured' }}" readonly>
                                    @if($webhookUrl)
                                        <button class="btn btn-outline-secondary" type="button" onclick="copyToClipboard('{{ url($webhookUrl) }}')">
                                            <i class="fas fa-copy"></i>
                                        </button>
                                    @endif
                                </div>
                                <small class="text-muted">Set FACEBOOK_WEBHOOK_URL in your .env file</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Verify Token</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" value="{{ $verifyToken ? str_repeat('*', strlen($verifyToken)) : 'Not configured' }}" readonly>
                                    @if($verifyToken)
                                        <button class="btn btn-outline-secondary" type="button" onclick="copyToClipboard('{{ $verifyToken }}')">
                                            <i class="fas fa-copy"></i>
                                        </button>
                                    @endif
                                </div>
                                <small class="text-muted">Set FACEBOOK_WEBHOOK_VERIFY_TOKEN in your .env file</small>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        @if($isConfigured)
                            <form method="POST" action="{{ route('facebook.webhook-settings.test') }}" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-play me-1"></i>Test Webhook
                                </button>
                            </form>
                        @endif
                        
                        <button type="button" class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#webhookInfoModal">
                            <i class="fas fa-info-circle me-1"></i>Setup Instructions
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">Environment Configuration</h6>
                </div>
                <div class="card-body">
                    <p class="text-muted">Configure webhook settings in your <code>.env</code> file:</p>
                    
                    <div class="bg-light p-3 rounded">
                        <code>
                            FACEBOOK_WEBHOOK_URL="/facebook/webhook"<br>
                            FACEBOOK_WEBHOOK_VERIFY_TOKEN="your_secure_token_here"
                        </code>
                    </div>

                    <div class="mt-3">
                        <small class="text-muted">
                            <strong>Note:</strong> After updating your .env file, make sure to restart your application server to apply the changes.
                        </small>
                    </div>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <h6 class="card-title mb-0">Facebook App Configuration</h6>
                </div>
                <div class="card-body">
                    <p class="text-muted">In your Facebook App settings:</p>
                    <ol class="small">
                        <li>Go to Products → Webhooks</li>
                        <li>Add webhook URL: <code>{{ $webhookUrl ? url($webhookUrl) : 'Configure webhook URL first' }}</code></li>
                        <li>Add verify token: <code>{{ $verifyToken ? 'Your configured token' : 'Configure token first' }}</code></li>
                        <li>Subscribe to lead events</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Webhook Info Modal -->
<div class="modal fade" id="webhookInfoModal" tabindex="-1" aria-labelledby="webhookInfoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="webhookInfoModalLabel">Facebook Webhook Setup Instructions</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h6>1. Environment Configuration</h6>
                <p>Add the following variables to your <code>.env</code> file:</p>
                <div class="bg-light p-3 rounded mb-3">
                    <code>
                        FACEBOOK_WEBHOOK_URL="/facebook/webhook"<br>
                        FACEBOOK_WEBHOOK_VERIFY_TOKEN="your_very_secure_random_token_here"
                    </code>
                </div>

                <h6>2. Facebook App Configuration</h6>
                <ol>
                    <li>Go to your Facebook App in <a href="https://developers.facebook.com" target="_blank">Facebook Developers</a></li>
                    <li>Navigate to Products → Webhooks</li>
                    <li>Click "Add Product" if Webhooks is not already added</li>
                    <li>Click "Set up" next to Webhooks</li>
                    <li>Add your webhook:</li>
                    <ul>
                        <li><strong>Callback URL:</strong> {{ request()->getSchemeAndHttpHost() }}/facebook/webhook</li>
                        <li><strong>Verify Token:</strong> Use the same token from your .env file</li>
                    </ul>
                    <li>Subscribe to the following events:</li>
                    <ul>
                        <li>leadgen (Lead Generation)</li>
                    </ul>
                </ol>

                <h6>3. Testing</h6>
                <p>Once configured, use the "Test Webhook" button to verify your configuration is working correctly.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        // Show success notification
        const toast = new bootstrap.Toast(document.getElementById('copyToast') || createToast('Copied to clipboard!'));
        toast.show();
    }).catch(function(err) {
        console.error('Could not copy text: ', err);
    });
}

function createToast(message) {
    const toast = document.createElement('div');
    toast.id = 'copyToast';
    toast.className = 'toast position-fixed top-0 end-0 m-3';
    toast.innerHTML = `
        <div class="toast-body">
            ${message}
        </div>
    `;
    document.body.appendChild(toast);
    return toast;
}
</script>
@endsection
