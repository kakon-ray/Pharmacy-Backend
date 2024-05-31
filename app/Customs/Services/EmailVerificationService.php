<?php

namespace app\Customs\Services;

use App\Models\EmailVerification;
use App\Notifications\EmailVerificationNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;


class EmailVerificationService
{

    public function sendVerificationLink(object $user):void
    {
        Notification::send($user, new EmailVerificationNotification($this->generateVerificationLink($user->email)));
    }

    public function generateVerificationLink(string $email): string
    {
        $checkIfTokenExists = EmailVerification::where('email',$email)->first();

        if($checkIfTokenExists) {
            $checkIfTokenExists->delete();
        }

        $token = Str::uuid();
        $url = config('app.url'). "/admin/emailverified?token=".$token . "&email=".$email;

        $saveToken = EmailVerification::create([
            'email' => $email,
            'token' => $token,
            'expired_at' => now()->addMinutes(10)
        ]);

        if($saveToken){
            return $url;
        }
    }


}