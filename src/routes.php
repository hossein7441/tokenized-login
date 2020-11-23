<?php

use Illuminate\Support\Facades\Route;

Route::get('/two-factor-auth/request-token', 'TokenSenderController@issueToken')
->name('2factor.requestToken');
Route::get('/two-factor-auth/login', 'TokenSenderController@loginWithToken')
->name('2factor.login');

if(app()->environment('local')){
    Route::get('/test/token-notif', function (){
        \App\User::unguard();
        $data = ['id' => 1, 'email'=> 'nasiry.hossein@gmail.com'];
        $user = new \App\User($data);
        \TwoFactorAuth\Facades\TokenSenderFacade::send('123456', $user);
    });

    Route::get('test/token-storage', function (){
        config()->set('two_factor_config.token_ttl', 3);
        sleep(1);
        \TwoFactorAuth\Facades\TokenStoreFacade::saveToken('1q2w3e', 1);
        $uid = \TwoFactorAuth\Facades\TokenStoreFacade::getUidByToken('1q2w3e')->getOr(null);

        if($uid !== 1){
            dd('some problem with read token');
        }

        $uid = \TwoFactorAuth\Facades\TokenStoreFacade::getUidByToken('1q2w3e')->getOr(null);;

        if(!is_null($uid)){
            dd('some problem with reading token');
        }

        dd('every thing is ok!');

        config()->set('two_factor_config.token_ttl', 1);
        sleep(1.1);
        \TwoFactorAuth\Facades\TokenStoreFacade::saveToken('1q2w3e', 1);
        $uid = \TwoFactorAuth\Facades\TokenStoreFacade::getUidByToken('1q2w3e')->getOr(null);;

        if(!is_null($uid)){
            dd('some problem with reading token');
        }

        dd('every thing is ok!');

    });
}

