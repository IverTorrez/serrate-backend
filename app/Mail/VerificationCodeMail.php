<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

class VerificationCodeMail extends Mailable
{
    public $verificationCode;

    public function __construct($verificationCode)
    {
        $this->verificationCode = $verificationCode;
    }

    public function build()
    {
        return $this->subject('Código de verificación')
            ->html('<h1>Tu código de verificación es: ' . $this->verificationCode . '</h1><p>Este código expirará en 15 minutos.</p>');
    }
}
