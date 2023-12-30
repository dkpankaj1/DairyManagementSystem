<?php
namespace Cortexitsolution\ApiAuth\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Cortexitsolution\ApiAuth\Http\Requests\ProfileUpdateRequest;
use Cortexitsolution\ApiAuth\Http\Resources\UserResource;
use Cortexitsolution\ApiAuth\Http\Traits\HttpResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    use HttpResponses;
    public function profile(Request $request)
    {
        return $this->sendSuccess(
            "user profile",
            [
                "user" => $request->user()
            ],
            200
        );
    }

    public function update(ProfileUpdateRequest $request)
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        try {

            $request->user()->save();
            return $this->sendSuccess('profile update success.!',[ 'user' => new UserResource(Auth::user())],200);

        } catch (\Exception $e) {

            $this->sendhttpResponseException('profile update failed.',['error' =>$e->getMessage()],400);

        }
    }
}