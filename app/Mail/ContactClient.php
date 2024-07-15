<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

use App\Models\Webview\Contacts;

class ContactClient extends Mailable
{
    use Queueable, SerializesModels;

    protected $user;

    public function __construct(Contacts $user)
    {
        $this->user = $user;
    }
    public function envelope()
    {
        return new Envelope(
            subject: 'Beamble - Sign-Up Confirmation',
        );
    }
    public function content()
    {
        return new Content(
            view: 'emails.web-client',
        );
    }
}
