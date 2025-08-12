<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TeamAccountCreatedMail extends Mailable
{
    use Queueable, SerializesModels;

    private $content;
    public $subject;
    private $attachment;

    public function __construct($subject,$content,$attachment)
    {
        $this->content = $content;
        $this->subject = $subject;
        $this->attachment = $attachment;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $email = $this->from(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME', config('app.name')))
                    ->subject($this->subject)
                    ->view('emails.team-account-created')
                    ->with([
                        'content' => $this->content,
                        'subject' => $this->subject
                    ]);

        if(isset($this->attachment) && $this->attachment !== "")
        {
            $email->attach($this->attachment);
        }
        
        return $email;
    }
}
