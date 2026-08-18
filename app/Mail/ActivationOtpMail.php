<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ActivationOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public $otpCode;


    public function __construct($otpCode)
    {
        $this->otpCode = $otpCode;
    }


    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Kode Verifikasi Aktivasi Akun HRIS System',
        );
    }


    public function content(): Content
    {
        return new Content(
            view: 'emails.activation_otp',
        );
    }
}