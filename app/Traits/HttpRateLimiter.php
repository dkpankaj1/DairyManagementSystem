<?PHP

namespace App\Traits;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

trait HttpRateLimiter
{
    use HttpResponses;

    public function ensureIsNotRateLimited()
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        return $this->sendError("too many attempts",['email' => trans('auth.throttle',['seconds' => $seconds, 'minutes' => ceil($seconds / 60)])],429);
    }


    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->input('email')).'|'.$this->ip());
    }

}