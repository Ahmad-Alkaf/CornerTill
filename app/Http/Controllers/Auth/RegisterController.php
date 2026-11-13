<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;
use App\Models\Business;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Auth\Events\Registered;
use App\Providers\RouteServiceProvider;
use App\Http\Requests\Auth\RegisterRequest;

class RegisterController extends Controller
{
    /**
     * Session key for the name and email from Google, GitHub, or X (OAuthRegister).
     * They are kept in the session, not in the URL.
     */
    public const OAUTH_SESSION_KEY = 'oauthRegister';

    /**
     * The name and email from the social sign-in provider, if they did not expire.
     *
     * @return array{name: ?string, email: ?string}|null
     */
    private function oauthInfo(): ?array
    {
        $info = session(self::OAUTH_SESSION_KEY);
        if (!is_array($info) || ($info['expires_at'] ?? 0) < now()->getTimestamp())
            return null;
        return ['name' => $info['name'] ?? null, 'email' => $info['email'] ?? null];
    }

    /**
     * Display the registration view.
     */
    public function create(): Response
    {
        return Inertia::render('Authenticating/Register/Register', [
            'oauth' => $this->oauthInfo(),
        ]);
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(RegisterRequest $request): RedirectResponse
    {
        $registerInfo = $request->validated();
        $user = new User($registerInfo);

        // The provider confirmed this email (OAuthRegister). The email is marked as
        // confirmed only when the form sends exactly that email.
        $oauthEmail = $this->oauthInfo()['email'] ?? null;
        $emailConfirmed = is_string($oauthEmail) && $oauthEmail !== ''
            && strcasecmp($oauthEmail, (string) $registerInfo['email']) === 0;

        DB::transaction(function () use ($registerInfo, $emailConfirmed, &$user) {
            $business = new Business($registerInfo['business']);
            $business->save();
            if ($emailConfirmed)
                $user->email_verified_at = now();
            $user->business_id = $business->id;
            $user->save();

            //Laravel should be exposed to internet for this function to work
            //Free beta (PAYMENTS_ENABLED=false): no Paddle customer and no trial.
            if (config('features.payments_enabled') && !app()->environment('testing'))
                $business->createAsCustomer([
                    'trial_ends_at' => now()->addDays(3),
                ]);
        });

        $request->session()->forget(self::OAUTH_SESSION_KEY);

        event(new Registered($user));

        Auth::login($user);

        if ( //registered by clicking a specific subscription plan
            config('features.payments_enabled') &&
            !empty($request->period) &&
            !empty($request->plan)
        )
            return redirect(route('subscription.subscribe', ['period' => $request->period, 'plan' => $request->plan]));

        return redirect(RouteServiceProvider::HOME)->with('message', __('Welcome :name', ['name' => $user->name]));
    }
}
