<?php

namespace App\Http\Controllers\Client\Auth\Mail;

use Illuminate\Mail\Mailable;

class SendVerificationCode extends Mailable
{
    public $code;

    public function __construct($code)
    {
        $this->code = $code;
    }

    public function build()
    {
        return $this->subject('Mã xác minh tài khoản')
                    ->view('emails.verification-code');
    }
}
