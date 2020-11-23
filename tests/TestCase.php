<?php


namespace Hossein7441\TwoFactorAuth;


use Hossein7441\TwoFactorAuth\TwoFactorAuthServiceProvider;

abstract class TestCase extends \Orchestra\Testbench\TestCase
{
    protected function getPackageProviders($app)
    {
        return [TwoFactorAuthServiceProvider::class];
    }

}