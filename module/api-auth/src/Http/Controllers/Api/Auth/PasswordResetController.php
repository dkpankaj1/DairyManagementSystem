<?php

namespace Cortexitsolution\ApiAuth\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Cortexitsolution\ApiAuth\Http\Requests\NewPasswordRequest;
use Cortexitsolution\ApiAuth\Http\Traits\HttpResponses;
use Cortexitsolution\ApiAuth\Notifications\SendOtpNotification;
use Cortexitsolution\ApiAuth\Http\Requests\PasswordResetRequest;
use Cortexitsolution\ApiAuth\Otp;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\RateLimiter;



class PasswordResetController extends Controller
{
	use HttpResponses;
	private $otp;

	public function __construct()
	{
		$this->otp = new Otp();
	}

	public function sendPasswordResetOtpEmail(PasswordResetRequest $request)
	{

		$request->ensureIsNotRateLimited();
		RateLimiter::hit($request->throttleKey());

		$user = User::where("email", $request->only('email'))->first();
		try {

			$user->notify(
				new SendOtpNotification(
					$this->otp->generate(
						$user->email,
						env('OTP_LENGTH', 4),
						env('OTP_EXPIRATION', 5)
					)
				)
			);

			return $this->sendSuccess("OTP Generated", ["email" => $request->email]);
		} catch (\Exception $e) {
			return $this->sendError(trans('api-auth::profile.password.error'), ['error' => $e->getMessage()]);
		}

	}

	public function resetPassword(NewPasswordRequest $request)
	{
		$request->ensureIsNotRateLimited();

		$status = $this->otp->validate($request->email, $request->otp);

		if ($status->status) {
			$user = User::where('email', $request->only("email"))->first();
			$user->forceFill([
				'password' => Hash::make($request->password)
			])->setRememberToken(Str::random(60));

			$user->save();
			RateLimiter::clear($request->throttleKey());
			return $this->sendSuccess("password updated");
		}

		RateLimiter::hit($request->throttleKey());
		return $this->sendError($status->message);

	}



}