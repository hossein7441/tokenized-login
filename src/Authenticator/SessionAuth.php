<?php


namespace Hossein7441\TwoFactorAuth\Authenticator;


use Illuminate\Support\Facades\Auth;

class SessionAuth
{
    public function check()
    {
        return Auth::check();
    }

    public function loginById($uid)
    {
        auth()->loginUsingId($uid);
    }
}
