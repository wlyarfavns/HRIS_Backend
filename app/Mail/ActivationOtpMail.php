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

    /**
     * Create a new message instance.
     */
    public function __construct($otpCode)
    {
        $this->otpCode = $otpCode;
    }

    /**
     * Get the message envelope (Subjek Email).
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Kode Verifikasi Aktivasi Akun HRIS System',
        );
    }

    /**
     * Get the message content definition (Tampilan HTML).
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.activation_otp',
        );
    }
}