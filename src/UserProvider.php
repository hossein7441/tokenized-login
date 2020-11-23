<?php


namespace Hossein7441\TwoFactorAuth;


use Illuminate\Foundation\Auth\User;

class UserProvider
{

    /**
     * @param $email
     * @return \Imanghafoori\Helpers\Nullable
     */
    public function getUserByEmail($email)
    {
        return nullable(User::where('email', $email)->first());
    }

    public function isBanned($uid)
    {
        $user = User::find($uid) ?: new User();

        return $user->is_ban == 1;
    }
}
