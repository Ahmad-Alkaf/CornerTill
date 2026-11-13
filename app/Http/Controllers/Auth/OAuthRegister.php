<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;

class OAuthRegister extends Controller
{
    /** The plan and period that the visitor chose before the provider's page. */
    private const PLAN_SESSION_KEY = 'oauthRegisterPlan';

    /**
     * True when the provider is known and its switch is on
     * (SOCIAL_GOOGLE_ENABLED, SOCIAL_GITHUB_ENABLED, SOCIAL_X_ENABLED).
     */
    private function isEnabled(string $provider): bool
    {
        return in_array($provider, ['github', 'x', 'google'], true)
            && config('features.social.' . $provider) === true;
    }

    public function redirect(string $provider)
    {
        if ($this->isEnabled($provider)) {
            if ($provider == 'x')
                $provider = 'twitter-oauth-2';
            // The plan and period wait in the session. The OAuth `state` parameter
            // belongs to Socialite: it checks it in callback() (CSRF protection).
            $plan = request()->query('plan');
            $period = request()->query('period');
            if (is_string($plan) && $plan !== '' && is_string($period) && $period !== '')
                session()->put(self::PLAN_SESSION_KEY, ['plan' => $plan, 'period' => $period]);
            else session()->forget(self::PLAN_SESSION_KEY);
            return Socialite::driver($provider)->redirect();
        }
        abort(404);
    }

    public function callback(string $provider)
    {
        if ($this->isEnabled($provider)) {
            if ($provider == 'x')
                $provider = 'twitter-oauth-2';

            // Not stateless(): Socialite compares the `state` of the provider's answer
            // with the session, so an attacker cannot make a victim finish the
            // attacker's sign-in (and register with the attacker's confirmed email).
            try {
                $userInfo = Socialite::driver($provider)->user();
            } catch (\Exception $e) {
                return redirect()->back()->with('error', __('Whoops, something went wrong!'));
            }

            // The name and email stay on the server, in the session (not in the URL:
            // a URL goes into server logs and the browser history).
            // RegisterController marks the email as confirmed only when the form
            // sends this same email.
            session()->put(RegisterController::OAUTH_SESSION_KEY, [
                'name' => $userInfo->name ?? $userInfo->nickname ?? null,
                'email' => $userInfo->email ?? null,
                'expires_at' => now()->addHour()->getTimestamp(),
            ]);

            // The plan and period (not personal data) go back into the URL.
            $query = session()->pull(self::PLAN_SESSION_KEY);

            return redirect()->route('register', is_array($query) ? $query : []);
        }

        abort(404);
    }
}
