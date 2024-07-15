<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

use Mail;

use App\Models\User;
use App\Models\UserOtp;
use App\Mail\RegisterReset;
use App\Mail\RegisterOtp;
use App\Mail\RegisterVerified;

use Carbon\Carbon;

class UserServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(UserServiceProvider::class, function ($app) {
            return new UserServiceProvider($app);
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    public function validator_forget(array $data)
    {
        return Validator::make($data, [
            'email' => 'required|email|max:255|exists:users,email'
        ]);
    }

    public function forget($data)
    {
        $validator = $this->validator_forget($data);
        if($validator->fails()){
            return [
                'success'=>false,
                'message'=> 'Email not found.'
                ];
        } else {
            $dados = $data;
            $user = User::where('email', $dados['email'])->first();
            $user->reset_password_code = Str::random(60);
            $user->save();

            $mailJob = (new RegisterReset($user))->onQueue('sistema');
            $m = Mail::to($user)
                ->bcc('sistema@soaba.com.br')
                ->later(1, $mailJob);

            return [
                'success'=>true
                ];
        }
    }

    public function generate_otp($data)
    {
        $validator = $this->validator_forget($data);
        if($validator->fails()){
            return [
                'success'=>false,
                'message'=> 'Email not found.'
                ];
        } else {
            $email = $data['email'];
            $user = User::where('email', $email)->first();
            $open_number_otp = UserOtp::generate_otp($email);

            $mailJob = (new RegisterOtp($user, $open_number_otp))->onQueue('sistema');
            $m = Mail::to($user)
                ->bcc('sistema@soaba.com.br')
                ->later(1, $mailJob);

            return [
                'success'=>true
                ];
        }
    }

    public function generate_otp_pre($data)
    {
        $email = $data['email'];
        $user = User::where('email', $email)->first();
        $open_number_otp = UserOtp::generate_otp($email);

        $mailJob = (new RegisterOtp($user, $open_number_otp))->onQueue('sistema');
        $m = Mail::to($user)
            ->bcc('sistema@soaba.com.br')
            ->later(1, $mailJob);

        return [
            'success'=>true
            ];
    }

    public function validator_otp(array $data)
    {
        return Validator::make($data, [
            'email' => 'required|email|exists:users,email',
            'number_otp' => 'required'
        ]);
    }

    public function verify_otp($data)
    {
        $validator = $this->validator_otp($data);
        if( $validator->passes() ){
            $email = $data['email'];
            $otp_number = $data['number_otp'];
            UserOtp::expires_otp();
            if( UserOtp::is_valid($email, $otp_number) ){

                $user = User::where('email', $email)->first();
                $user->markEmailAsVerified();

                $mailJob = (new RegisterVerified($user))->onQueue('sistema');
                    $m = Mail::to($user)
                        ->bcc('sistema@soaba.com.br')
                        ->later(1, $mailJob);
                return [
                    'success'=>true
                ];
            } else {
                return [
                    'success'=> false,
                    'message'=> 'Expired code'
                ];
            }
        }

        return [
            'success'=> false,
            'message'=> 'Email and Code are required.'
        ];
    }

    public function verify_otp_pre($data)
    {
        $email = $data['email'];
        $otp_number = $data['number_otp'];
        UserOtp::expires_otp();
        if( UserOtp::is_valid($email, $otp_number) ){
            // $user = User::where('email', $email)->first();
            // $user->markEmailAsVerified();

            // $mailJob = (new RegisterVerified($user))->onQueue('sistema');
            //     $m = Mail::to($user)
            //         ->bcc('sistema@soaba.com.br')
            //         ->later(1, $mailJob);
            return [
                'success'=>true
            ];
        } else {
            return [
                'success'=> false,
                'message'=> 'Expired code'
            ];
        }
    }
}
