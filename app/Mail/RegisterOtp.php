<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

use App\Models\User;

class RegisterOtp extends Mailable
{
    use Queueable, SerializesModels;

    protected $user;
    protected $otp_code;

    public function __construct(User $user, $otp_code = null)
    {
        $this->user = $user;
        $this->otp_code = $otp_code;
    }

    public function build()
    {
        return $this->view('emails.newotp')
            ->subject("Beamble - Verification code")
            ->with([
                'otp_code' => $this->otp_code
            ]);
    }
}
