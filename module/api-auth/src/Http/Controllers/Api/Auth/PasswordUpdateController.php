<?php
namespace Cortexitsolution\ApiAuth\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Cortexitsolution\ApiAuth\Http\Requests\PasswordUpdateRequest;
use Cortexitsolution\ApiAuth\Http\Traits\HttpResponses;
use Illuminate\Support\Facades\Hash;

class PasswordUpdateController extends Controller
{
    use HttpResponses;
    public function update(PasswordUpdateRequest $request)
    {
        if(!$request->validateOldPassword()){
            return $this->sendError(trans('profile.password.invalid'));
        }
      
        try {
            $request->user()->update(['password' => Hash::make($request->password)]);
            return $this->sendSuccess(trans('api-auth::profile.password.success'),[],200);

        } catch (\Exception $e) {
            return $this->sendHttp(trans('api-auth::profile.password.error'),['error' =>$e->getMessage()]);
        }
    }
}