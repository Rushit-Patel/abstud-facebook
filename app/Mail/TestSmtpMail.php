<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class TestSmtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public $testData;

    /**
     * Create a new message instance.
     */
    public function __construct($testEmail = null)
    {
        $this->testData = [
            'app_name' => config('app.name'),
            'test_email' => $testEmail,
            'sent_at' => now()->format('Y-m-d H:i:s'),
            'from_address' => config('mail.from.address'),
            'from_name' => config('mail.from.name'),
            'mail_driver' => config('mail.default'),
            'smtp_host' => config('mail.mailers.smtp.host'),
            'smtp_port' => config('mail.mailers.smtp.port'),
            'smtp_encryption' => config('mail.mailers.smtp.encryption'),
            'smtp_username' => config('mail.mailers.smtp.username') ? '***configured***' : 'not configured',
            'environment' => config('app.env'),
            'timestamp' => now()->timestamp,
            'uuid' => \Str::uuid()->toString(),
            'server_ip' => $_SERVER['SERVER_ADDR'] ?? 'unknown',
            'delivery_note' => 'If you are not receiving this email, check your spam/junk folder, verify email address, and contact your email provider.',
        ];
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'SMTP Test Email - ' . config('app.name'),
            from: config('mail.from.address'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.test-smtp',
            with: ['testData' => $this->testData],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
