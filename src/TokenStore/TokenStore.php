<?php


namespace Hossein7441\TwoFactorAuth\TokenStore;


class TokenStore
{
    public function saveToken($token, $userId): void
    {
        $ttl = config('two_factor_config.token_ttl');
        cache()->set($token . '_2factor_auth', $userId, $ttl);
    }

    public function getUidByToken($token)
    {
        $uid = cache()->pull($token . '_2factor_auth');
        return nullable($uid);
    }
}
