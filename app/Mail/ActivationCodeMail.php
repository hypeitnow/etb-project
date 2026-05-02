<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

class ActivationCodeMail extends Mailable
{
    public string $code;

    public function __construct(string $code)
    {
        $this->code = $code;
    }

    public function build()
    {
        return $this->subject('Kod aktywacyjny ETB')
            ->text('emails.activation-code', [
                'code' => $this->code,
            ]);
    }
}
