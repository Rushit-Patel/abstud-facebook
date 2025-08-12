<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SMTP Test Email</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .email-container {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #e9ecef;
        }
        .header h1 {
            color: #2c5aa0;
            margin: 0;
            font-size: 24px;
        }
        .header .subtitle {
            color: #6c757d;
            margin: 5px 0 0 0;
            font-size: 14px;
        }
        .success-badge {
            background-color: #d4edda;
            color: #155724;
            padding: 10px 15px;
            border-radius: 5px;
            margin: 20px 0;
            text-align: center;
            border-left: 4px solid #28a745;
        }
        .test-details {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
            border-left: 4px solid #007bff;
        }
        .test-details h3 {
            margin-top: 0;
            color: #495057;
            font-size: 16px;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            margin: 8px 0;
            padding: 5px 0;
            border-bottom: 1px solid #e9ecef;
        }
        .detail-row:last-child {
            border-bottom: none;
        }
        .detail-label {
            font-weight: bold;
            color: #495057;
            min-width: 140px;
        }
        .detail-value {
            color: #6c757d;
            font-family: monospace;
            background-color: #f1f3f4;
            padding: 2px 6px;
            border-radius: 3px;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
            color: #6c757d;
            font-size: 14px;
        }
        .status-indicator {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background-color: #28a745;
            display: inline-block;
            margin-right: 8px;
        }
        @media (max-width: 600px) {
            body {
                padding: 10px;
            }
            .email-container {
                padding: 20px;
            }
            .detail-row {
                flex-direction: column;
            }
            .detail-label {
                margin-bottom: 5px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="header">
            <h1>🔗 SMTP Test Email</h1>
            <p class="subtitle">{{ $testData['app_name'] }} - Email Configuration Verification</p>
        </div>

        <!-- Success Message -->
        <div class="success-badge">
            <span class="status-indicator"></span>
            <strong>Success!</strong> Your SMTP configuration is working correctly.
        </div>

        <!-- Main Content -->
        <div style="margin: 25px 0;">
            <p>Hello!</p>
            <p>This is a test email from <strong>{{ $testData['app_name'] }}</strong> to verify that your SMTP configuration is working correctly.</p>
            <p>If you received this email, it means your email settings are properly configured and emails can be sent successfully from your application.</p>
        </div>

        <!-- Test Details -->
        <div class="test-details">
            <h3>📋 Test Configuration Details</h3>
            
            <div class="detail-row">
                <span class="detail-label">Sent At:</span>
                <span class="detail-value">{{ $testData['sent_at'] }}</span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">From Address:</span>
                <span class="detail-value">{{ $testData['from_address'] }}</span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">From Name:</span>
                <span class="detail-value">{{ $testData['from_name'] }}</span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">Mail Driver:</span>
                <span class="detail-value">{{ strtoupper($testData['mail_driver']) }}</span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">SMTP Host:</span>
                <span class="detail-value">{{ $testData['smtp_host'] }}</span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">SMTP Port:</span>
                <span class="detail-value">{{ $testData['smtp_port'] }}</span>
            </div>
            
            @if($testData['smtp_encryption'])
            <div class="detail-row">
                <span class="detail-label">Encryption:</span>
                <span class="detail-value">{{ strtoupper($testData['smtp_encryption']) }}</span>
            </div>
            @endif
            
            <div class="detail-row">
                <span class="detail-label">Environment:</span>
                <span class="detail-value">{{ strtoupper($testData['environment']) }}</span>
            </div>
            
            @if(isset($testData['smtp_username']))
            <div class="detail-row">
                <span class="detail-label">SMTP Username:</span>
                <span class="detail-value">{{ $testData['smtp_username'] }}</span>
            </div>
            @endif
            
            @if(isset($testData['uuid']))
            <div class="detail-row">
                <span class="detail-label">Message UUID:</span>
                <span class="detail-value">{{ $testData['uuid'] }}</span>
            </div>
            @endif
            
            @if(isset($testData['server_ip']))
            <div class="detail-row">
                <span class="detail-label">Server IP:</span>
                <span class="detail-value">{{ $testData['server_ip'] }}</span>
            </div>
            @endif
            
            @if(isset($testData['timestamp']))
            <div class="detail-row">
                <span class="detail-label">Timestamp:</span>
                <span class="detail-value">{{ $testData['timestamp'] }}</span>
            </div>
            @endif
            
            @if($testData['test_email'])
            <div class="detail-row">
                <span class="detail-label">Test Email:</span>
                <span class="detail-value">{{ $testData['test_email'] }}</span>
            </div>
            @endif
        </div>

        <!-- Next Steps -->
        <div style="background-color: #e7f3ff; padding: 15px; border-radius: 5px; margin: 20px 0; border-left: 4px solid #007bff;">
            <h4 style="margin-top: 0; color: #0056b3;">✅ What this means:</h4>
            <ul style="margin: 10px 0; padding-left: 20px; color: #495057;">
                <li>Your SMTP server is reachable and accepting connections</li>
                <li>Authentication credentials are correct</li>
                <li>Email delivery is working properly</li>
                <li>Your application can send emails to users</li>
            </ul>
        </div>

        <!-- Troubleshooting Section -->
        <div style="background-color: #fff3cd; padding: 15px; border-radius: 5px; margin: 20px 0; border-left: 4px solid #ffc107;">
            <h4 style="margin-top: 0; color: #856404;">🔍 Not receiving emails? Here's what to check:</h4>
            <ol style="margin: 10px 0; padding-left: 20px; color: #856404; font-size: 14px;">
                <li><strong>Spam/Junk Folder:</strong> Check your spam or junk mail folder first</li>
                <li><strong>Email Address:</strong> Verify the recipient email address is correct</li>
                <li><strong>Email Provider:</strong> Some providers (Gmail, Outlook) may block new senders</li>
                <li><strong>Domain Reputation:</strong> Your sending domain may need time to build reputation</li>
                <li><strong>Content Filters:</strong> Email content might trigger spam filters</li>
            </ol>
            @if(isset($testData['uuid']))
            <p style="margin: 10px 0; color: #856404; font-size: 12px;">
                <strong>Tracking ID:</strong> {{ $testData['uuid'] }} - Use this when contacting support
            </p>
            @endif
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>This email was automatically generated by the SMTP test feature in {{ $testData['app_name'] }}.</p>
            <p style="margin: 5px 0; font-size: 12px;">
                If you did not request this test email, please contact your system administrator.
            </p>
            <hr style="border: none; border-top: 1px solid #e9ecef; margin: 15px 0;">
            <p style="margin: 0;">
                <strong>{{ $testData['app_name'] }}</strong><br>
                <em>Email System Verification</em>
            </p>
        </div>
    </div>
</body>
</html>
