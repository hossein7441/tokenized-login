<?php

namespace Hossein7441\TwoFactorAuth\Http\Responses;


use Illuminate\Http\Response;

class Responses
{
    public function blockedUser()
    {
        return response()->json(['error' => 'You are blocked!'], Response::HTTP_BAD_REQUEST);
    }

    public function emailNotValid()
    {
        return response()->json(['error' => 'Email is not valid!'], Response::HTTP_BAD_REQUEST);
    }

    public function youShouldBeGuest()
    {
        return response()->json(['error' => 'You should be guest'], Response::HTTP_BAD_REQUEST);
    }

    public function userNotFound()
    {
        return response()->json(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
    }

    public function tokenSent()
    {
        return response()->json(['message' => 'token was sent.']);
    }
    public function loggedIn()
    {
        return response()->json(['message' => 'You are logged in!']);
    }
    public function tokenNotFound()
    {
        return response()->json(['error' => 'token is not valid'], Response::HTTP_NOT_FOUND);
    }
}
