<?php
namespace Hossein7441\TwoFactorAuth;

use Hossein7441\TwoFactorAuth\Http\ResponderFacade;
use Hossein7441\TwoFactorAuth\Http\Responses\Responses;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Hossein7441\TwoFactorAuth\Authenticator\SessionAuth;
use Hossein7441\TwoFactorAuth\Facades\AuthFacade;
use Hossein7441\TwoFactorAuth\Facades\TokenGeneratorFacade;
use Hossein7441\TwoFactorAuth\Facades\TokenSenderFacade;
use Hossein7441\TwoFactorAuth\Facades\TokenStoreFacade;
use Hossein7441\TwoFactorAuth\Facades\UserProviderFacade;
use Hossein7441\TwoFactorAuth\TokenGenerator\FakeTokenGenerator;
use Hossein7441\TwoFactorAuth\TokenGenerator\TokenGenerator;
use Hossein7441\TwoFactorAuth\TokenStore\FakeTokenStore;
use Hossein7441\TwoFactorAuth\TokenStore\TokenStore;

class TwoFactorAuthServiceProvider extends ServiceProvider
{

    private $namespace = 'Hossein7441\TwoFactorAuth\Http\Controllers';

    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/config/two_factor_auth_config.php', 'two_factor_config');

        AuthFacade::shouldProxyTo(SessionAuth::class);
        UserProviderFacade::shouldProxyTo(UserProvider::class);

        if(app()->runningUnitTests()){
            $tokenGenerator = FakeTokenGenerator::class;
            $tokenStore = FakeTokenStore::class;
            $tokenSender = FakeTokenSender::class;
        }else{
            $tokenGenerator = TokenGenerator::class;
            $tokenStore = TokenStore::class;
            $tokenSender = TokenSender::class;

        }
        ResponderFacade::shouldProxyTo(Responses::class);
        TokenGeneratorFacade::shouldProxyTo($tokenGenerator);
        TokenStoreFacade::shouldProxyTo($tokenStore);
        TokenSenderFacade::shouldProxyTo($tokenSender);


    }

    public function boot()
    {
        if(! $this->app->routesAreCached()){
            $this->defineRoutes();
        }
    }

    protected function defineRoutes()
    {
        Route::prefix('api')
            ->middleware('api')
            ->namespace($this->namespace)
            ->group(__DIR__ . '/routes.php');
    }
}
