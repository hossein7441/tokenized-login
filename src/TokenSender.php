<?php


namespace Hossein7441\TwoFactorAuth;


use Illuminate\Support\Facades\Notification;

class TokenSender
{
    public function send($token, $user)
    {
        Notification::sendNow($user, new LoginTokenNotification($token));
    }
}
