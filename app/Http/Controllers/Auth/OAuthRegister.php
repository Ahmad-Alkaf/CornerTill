<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;

class OAuthRegister extends Controller
{
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
            if (($plan = request()->query('plan')) && ($period = request()->query('period')) && !empty($plan) && !empty($period))
                $with = ['state' => 'plan=' . $plan . '&period=' . $period];
            else $with = [];
            // $query = request
            return Socialite::driver($provider)->with($with)->redirect();
        }
        abort(404);
    }

    public function callback(string $provider)
    {
        if ($this->isEnabled($provider)) {
            if ($provider == 'x')
                $provider = 'twitter-oauth-2';

            try {
                $userInfo = Socialite::driver($provider)->stateless()->user();
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

            // The plan and period (not personal data) stay in the URL.
            $query = [];
            parse_str((string) request()->input('state'), $result);
            //empty() treats null and empty string as the same falsy value
            if (!empty($result['plan']) && !empty($result['period']) && is_string($result['plan']) && is_string($result['period'])) {
                $query['plan'] = $result['plan'];
                $query['period'] = $result['period'];
            }

            return redirect()->route('register', $query);
        }

        abort(404);
    }
}
