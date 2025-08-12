<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EmailTemplateMail extends Mailable
{
    use SerializesModels;
    
    private $content;
    public $subject;
    private $attachment;


    public function __construct($subject, $content, $attachment = null)
    {
        $this->content = $content;
        $this->subject = $subject;
        $this->attachment = $attachment;
    }

    public function build()
    {
        return $this->from(config('mail.from.address'), config('mail.from.name'))
        ->subject($this->subject)
        ->view('emails.email-template')
        ->with([
            'content' => $this->content,
            'subject' => $this->subject,
            'attachment' => $this->attachment,
        ]);
    }
}