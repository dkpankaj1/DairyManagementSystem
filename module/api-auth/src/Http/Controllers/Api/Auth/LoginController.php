<?php

namespace Cortexitsolution\ApiAuth\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Cortexitsolution\ApiAuth\Events\UserLoginEvent;
use Cortexitsolution\ApiAuth\Http\Requests\LoginRequest;
use Cortexitsolution\ApiAuth\Http\Traits\HttpResponses;
use Cortexitsolution\ApiAuth\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    use HttpResponses;
    public function login(LoginRequest $request)
    {
        $request->authenticate();

        if (Auth::user()->currentAccessToken()) {
            Auth::user()->currentAccessToken()->delete();
        }

        $data = [
            'user' => new UserResource(Auth::user()),
            'token' => Auth::user()->createToken($request->throttleKey())->plainTextToken,
        ];

        event(new UserLoginEvent(auth()->user(), $request->ip()));

        return $this->sendSuccess("login success", $data, 200);
    }
    public function logout(Request $request)
    {
        // $request->user()->currentAccessToken()->delete();
        $request->user()->tokens()->delete();
        return $this->sendSuccess("logout success");
    }
}

?>