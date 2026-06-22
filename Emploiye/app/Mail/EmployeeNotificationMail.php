<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EmployeeNotificationMail extends Mailable
{
    use SerializesModels;

    public $title;
    public $messageText;

    public function __construct($title, $messageText)
    {
        $this->title = $title;
        $this->messageText = $messageText;
    }

    public function build()
    {
        return $this->subject($this->title)
                    ->view('emails.notification');
    }
}