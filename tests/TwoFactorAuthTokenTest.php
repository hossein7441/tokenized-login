<?php


namespace Hossein7441\TwoFactorAuth;

use Hossein7441\TwoFactorAuth\Facades\AuthFacade;
use Hossein7441\TwoFactorAuth\Facades\TokenGeneratorFacade;
use Hossein7441\TwoFactorAuth\Facades\TokenSenderFacade;
use Hossein7441\TwoFactorAuth\Facades\TokenStoreFacade;
use Hossein7441\TwoFactorAuth\Facades\UserProviderFacade;
use Hossein7441\TwoFactorAuth\Http\ResponderFacade;
use Hossein7441\TwoFactorAuth\TokenSender;
use Illuminate\Foundation\Auth\User;

class TwoFactorAuthTokenTest  extends TestCase
{

    public function test_good_every_things()
    {
        User::unguard();
        UserProviderFacade::shouldReceive('isBanned')
            ->once()
            ->with(1)
            ->andReturn(false);

        $user = new User(['id'=> 1, 'email'=> 'iman@gmail.com']);
        UserProviderFacade::shouldReceive('getUserByEmail')
            ->once()
            ->with('hossein@gmail.com')
            ->andReturn(nullable($user));

        TokenGeneratorFacade::shouldReceive('generateToken')
            ->once()
            ->withNoArgs()
            ->andReturn('125sadaf');

        TokenStoreFacade::shouldReceive('saveToken')
            ->once()
            ->with('125sadaf', $user->id);

        TokenSenderFacade::shouldReceive('send')->once()
            ->with('125sadaf', $user);

        ResponderFacade::shouldReceive('tokenSent')->once();

        $this->get('api/two-factor-auth/request-token?email=hossein@gmail.com');
    }


    public function test_user_is_banned()
    {
        User::unguard();

        UserProviderFacade::shouldReceive('isBanned')
            ->once()
            ->with(1)
            ->andReturn(true);

        $user = new User(['id'=> 1, 'email'=> 'hossein@gmail.com']);
        UserProviderFacade::shouldReceive('getUserByEmail')
            ->once()
            ->with('hossein@gmail.com')
            ->andReturn(nullable($user));

        TokenGeneratorFacade::shouldReceive('generateToken')->never();

        TokenStoreFacade::shouldReceive('saveToken')->never();

        TokenSenderFacade::shouldReceive('send')->never();

        $response = $this->get('api/two-factor-auth/request-token?email=hossein@gmail.com');
        $response->assertJson(['error' => 'You are blocked!']);
        $response->assertStatus(400);
    }

    public function test_email_not_found()
    {
        UserProviderFacade::shouldReceive('isBanned')->never();

        UserProviderFacade::shouldReceive('getUserByEmail')
            ->once()
            ->with('hossein@gmail.com')
            ->andReturn(nullable(null));

        TokenGeneratorFacade::shouldReceive('generateToken')->never();
        TokenStoreFacade::shouldReceive('saveToken')->never();
        TokenSenderFacade::shouldReceive('send')->never();
        ResponderFacade::shouldReceive('userNotFound')->once();

        $this->get('api/two-factor-auth/request-token?email=hossein@gmail.com');

    }

    public function test_email_not_valid()
    {
        UserProviderFacade::shouldReceive('getUserByEmail')->never();
        UserProviderFacade::shouldReceive('isBanned')->never();
        TokenGeneratorFacade::shouldReceive('generateToken')->never();
        TokenStoreFacade::shouldReceive('saveToken')->never();
        TokenSenderFacade::shouldReceive('send')->never();
        ResponderFacade::shouldReceive('emailNotValid')->once()->andReturn(response('email not valid'));

        $respo = $this->get('api/two-factor-auth/request-token?email=hossein_gmail.com');
        $respo->assertSee('email not valid');

    }

    public function test_user_is_guest()
    {
        AuthFacade::shouldReceive('check')->andReturn(true)->once();
        UserProviderFacade::shouldReceive('getUserByEmail')->never();
        UserProviderFacade::shouldReceive('isBanned')->never();
        TokenGeneratorFacade::shouldReceive('generateToken')->never();
        TokenStoreFacade::shouldReceive('saveToken')->never();
        TokenSenderFacade::shouldReceive('send')->never();
        ResponderFacade::shouldReceive('youShouldBeGuest')->once()->andReturn(response('guest'));

        $respo = $this->get('api/two-factor-auth/request-token?email=hossein@gmail.com');
        $respo->assertSee('guest');

    }

}
