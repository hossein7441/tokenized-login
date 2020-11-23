<?php


namespace Hossein7441\TwoFactorAuth\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use phpDocumentor\Reflection\Types\Nullable;
use Hossein7441\TwoFactorAuth\Facades\AuthFacade;
use Hossein7441\TwoFactorAuth\Facades\TokenGeneratorFacade;
use Hossein7441\TwoFactorAuth\Facades\TokenSenderFacade;
use Hossein7441\TwoFactorAuth\Facades\TokenStoreFacade;
use Hossein7441\TwoFactorAuth\Facades\UserProviderFacade;
use Hossein7441\TwoFactorAuth\Http\ResponderFacade;

class TokenSenderController extends Controller
{
    public function loginWithToken()
    {
        $token = request('token');
        $uid = TokenStoreFacade::getUidByToken($token)->getOrSend([
            ResponderFacade::class, 'tokenNotFound'
        ]);

        AuthFacade::loginById($uid);

        return ResponderFacade::loggedIn();
    }

    public function issueToken()
    {
        $email = request('email');
        // validate email
        $this->validateEmailIsValid();

        // validate user is guest
        $this->checkUserIsGuest();

        // throttle the route

        // find user row in DB or Fail
        /**
         * @var \Imanghafoori\Helpers\Nullable $user
         */
        $user = UserProviderFacade::getUserByEmail($email)->getOrSend([ResponderFacade::class, 'userNotFound']);

        // 1. stop block users
        if(UserProviderFacade::isBanned($user->id)){
            return ResponderFacade::blockedUser();
        }
        // 2. generate token
        $token = TokenGeneratorFacade::generateToken();

        // 3. save token
        TokenStoreFacade::saveToken($token, $user->id);

        // 4. send token
        TokenSenderFacade::send($token, $user);

        // 5. send Response
        return ResponderFacade::tokenSent();
    }

    protected function validateEmailIsValid()
    {
        $v = Validator::make(request()->all(), ['email' => 'required|email']);
        if ($v->fails()) {
            ResponderFacade::emailNotValid()->throwResponse();
        }
    }

    protected function checkUserIsGuest()
    {
        if (AuthFacade::check()) {
            ResponderFacade::youShouldBeGuest()->throwResponse();
        }
    }

}
