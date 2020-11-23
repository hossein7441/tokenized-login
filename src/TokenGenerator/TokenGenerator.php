<?php


namespace Hossein7441\TwoFactorAuth\TokenGenerator;


class TokenGenerator
{

    public function generateToken()
    {
        return random_int(100000, 100000 - 1);
    }
}
