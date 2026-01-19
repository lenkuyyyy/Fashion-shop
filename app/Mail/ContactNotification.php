<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function build()
    {
        return $this->subject('📬 Liên hệ mới từ website')
            ->replyTo($this->data['email'], $this->data['name']) // người nhận có thể reply
            ->view('emails.contact');
    }
}

