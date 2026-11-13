<?php

namespace App\Http\Requests\Auth;

use App\Models\Business;
use App\Models\User;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        if (! Auth::attempt($this->only('email', 'password'), $this->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey());

            $this->failIfBusinessIsDeleted();

            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }
        
        RateLimiter::clear($this->throttleKey());
    }

    /**
     * An account of a deleted business cannot sign in (ActiveBusinessUserProvider).
     * When the password is correct, tell why and how to restore the business.
     * With a wrong password, the normal message shows: it tells nothing about the business.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    private function failIfBusinessIsDeleted(): void
    {
        $user = User::withTrashed()->where('email', $this->input('email'))->first();
        if (! $user || ! Hash::check((string) $this->input('password'), $user->password))
            return;

        $business = Business::withTrashed()->find($user->business_id);
        if ($business?->trashed()) {
            throw ValidationException::withMessages([
                'email' => $business->deletedMessage(),
            ]);
        }
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->input('email')).'|'.$this->ip());
    }
}
