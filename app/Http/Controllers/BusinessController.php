<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;
use App\Models\Business;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Filesystem\FilesystemAdapter;
use App\Http\Requests\Business\UpdateBusinessRequest;

class BusinessController extends Controller
{
    /**
     * Display the user's business form.
     */
    public function edit(Request $request): Response
    {
        $business = Business::with([
            'users' => function ($query) {
                $query->withTrashed();
            }
        ])->find($request->user()->business_id);
        Gate::authorize('edit', $business);

        // Free beta (PAYMENTS_ENABLED=false): no Paddle plans, only the beta limits.
        if (!config('features.payments_enabled')) {
            return Inertia::render('Authenticated/Business/Edit', [
                'business' => $business,
                'plansMaxRes' => config('constants.planResources'),
                'subscriptionLinks' => [
                    'basic' => null,
                    'enhanced' => null,
                    'advanced' => null,
                    'subscribedTo' => 'Beta',
                    'state' => null,
                    'onTrial' => null,
                    'progress' => $business->progress(),
                    'lastPayment' => null,
                    'nextPayment' => null,
                    'gracePeriodExpiresAt' => null,
                ],
            ]);
        }

        // dd(
        //     'Subscribed:',
        //     $business->subscribed(),
        //     'Subscription:',
        //     $business->subscription(),
        //     'Transactions:',
        //     $business->transactions,
        //     'On Trail:',
        //     $business->onTrial(),
        //     'Subscribed OR on Trial:',
        //     $business->subscribedOrOnTrial(),
        //     'Recurring (Active sub and not trail nor grace period):',
        //     $business->subscription()?->recurring(),
        //     'Canceled (Was active sub but has canceled now):',
        //     $business->subscription()?->canceled(),
        //     'Grace period (Canceled but still active until expires):',
        //     $business->subscription()?->onGracePeriod(),
        //     'Past Due (payment failed, customer should update payment method):',
        //     $business->subscription()?->pastDue(),
        //     'Subscribed to Advance:',
        //     $business->subscription()?->hasProduct(config('services.paddle.products.advanced')),
        //     'Subscribed to Enhanced:',
        //     $business->subscription()?->hasProduct(config('services.paddle.products.enhanced')),
        //     'Subscribed to Basic:',
        //     $business->subscription()?->hasProduct(config('services.paddle.products.basic')),
        // );
        // dd('Next billing cycle:', $business->subscription()->nextPayment());
        // dd('paused:', $business->subscription()->paused());
        // dd('pause grace period:',$business->subscription()->onPausedGracePeriod());


        $state = null;
        $subscribedTo = null;
        $lastPayment = null;
        $gracePeriodExpiresAt = null;
        $nextPayment = null;
        if ($sub = $business->subscription()) {
            $lastPayment = $sub->lastPayment();
            $nextPayment = $sub->nextPayment();

            if ($sub->onGracePeriod() || $sub->onPausedGracePeriod()) {
                $gracePeriodExpiresAt = $sub->ends_at;
                $state = 'Grace Period';
            } else if ($sub->canceled())
                $state = 'Canceled';
            else if ($sub->recurring()) {
                $state = 'Recurring';
            } else if ($sub->pastDue())
                $state = 'Past Due';
            else if ($sub->paused())
                $state = 'Paused';

            if ($sub->hasProduct(config('services.paddle.products.advanced')))
                $subscribedTo = 'Advanced';
            else if ($sub->hasProduct(config('services.paddle.products.enhanced')))
                $subscribedTo = 'Enhanced';
            else if ($sub->hasProduct(config('services.paddle.products.basic')))
                $subscribedTo = 'Basic';
        } else if ($business->onTrial()) {
            $subscribedTo = 'Trial';
        }

        //                                                monthly,                          annually
        $basicPrices    = $subscribedTo === 'Basic'    ? [null, null] : [config('services.paddle.prices.basic.monthly'), config('services.paddle.prices.basic.annually')];
        $enhancedPrices = $subscribedTo === 'Enhanced' ? [null, null] : [config('services.paddle.prices.enhanced.monthly'), config('services.paddle.prices.enhanced.annually')];
        $advancedPrices = $subscribedTo === 'Advanced' ? [null, null] : [config('services.paddle.prices.advanced.monthly'), config('services.paddle.prices.advanced.annually')];

        $prices = [
            ...$basicPrices,
            ...$enhancedPrices,
            ...$advancedPrices,
        ];
        $plans = [];
        foreach ($prices as $price) {
            if (isset($price))
                $plans[] = $business
                    ->subscribe($price)
                    ->returnTo(route('business.edit'));
            else $plans[] = null;
        }
        $planOptions = [];
        foreach ($plans as $plan)
            $planOptions[] = $plan?->options();

        foreach ($planOptions as &$option) {
            if (isset($option)) {
                $option['settings']['displayMode'] = 'overlay';
                $option['settings']['theme'] = 'light';
            }
        }

        return Inertia::render('Authenticated/Business/Edit', [
            'business' => $business,
            'plansMaxRes' => config('constants.planResources'),
            'subscriptionLinks' => [
                'basic' => $subscribedTo === 'Basic' ? null : [
                    'monthly' => $planOptions[0],
                    'annually' => $planOptions[1],
                ],
                'enhanced' => $subscribedTo === 'Enhanced' ? null : [
                    'monthly' => $planOptions[2],
                    'annually' => $planOptions[3],
                ],
                'advanced' => $subscribedTo === 'Advanced' ? null : [
                    'monthly' => $planOptions[4],
                    'annually' => $planOptions[5],
                ],
                'subscribedTo' => $subscribedTo,
                'state' => $state,
                'onTrial' => $subscribedTo === 'Trial' ? $business->customer->trial_ends_at : null,
                'progress' => $business->progress(strtolower($subscribedTo)),
                'lastPayment' => $lastPayment,
                'nextPayment' => $nextPayment,
                'gracePeriodExpiresAt' => $gracePeriodExpiresAt,
            ]
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBusinessRequest $request, Business $business)
    {
        Gate::authorize('update', $business);

        $updatedBusiness = $request->validated();
        // The logo changes only when the request uploads a new file (logoFile) or
        // explicitly removes it (logo: null). The other forms of the business page
        // (Location & Contact, Financial) do not send `logo`: they keep the logo.
        // A logo name sent by the client is never stored, so a request cannot
        // point the logo to (and later delete) another file.
        $removeLogo = array_key_exists('logo', $updatedBusiness) && $updatedBusiness['logo'] === null;
        unset($updatedBusiness['logo'], $updatedBusiness['logoFile']);
        if ($request->hasFile('logoFile')) {
            $updatedBusiness['logo'] = $this->storeFile($request);
        } else if ($removeLogo) {
            $updatedBusiness['logo'] = null;
        }

        $oldLogo = $business->logo;
        $business->update($updatedBusiness);

        // Delete the old file only after the new value is saved.
        if (isset($oldLogo) && $oldLogo !== $business->logo) {
            $this->deleteFile($oldLogo);
        }

        return redirect()->back()->with('success', __('Successfully updated'));
    }

    /**
     * Soft-delete the business and its accounts. Nothing else is deleted now (also
     * not the logo file), so a mistaken delete can be restored for
     * config('constants.deletedBusinessRetentionDays') days:
     * `php artisan business:restore {id}`. After that, the daily job
     * `business:purge-deleted` deletes all data of the business permanently.
     * No account of a deleted business can sign in (ActiveBusinessUserProvider).
     */
    public function destroy(Request $request, Business $business)
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);
        Gate::authorize('destroy', $business);

        DB::transaction(function () use ($business) {
            // One time for the business and its accounts: business:restore restores
            // the accounts with this time, not the accounts that the Owner removed before.
            $deletedAt = now();
            // The policy leaves only the Owner, but delete every active account of the business.
            User::where('business_id', $business->id)->update(['deleted_at' => $deletedAt]);
            $business->deleted_at = $deletedAt;
            $business->save();
        });

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/')->with('success', __('Successfully deleted'));
    }


    private function deleteFile(string $filePath)
    {
        if (config('filesystems.default') == 's3')
            return Storage::disk('businesses-logo')->delete(basename($filePath));
        return Storage::disk('businesses-logo')->delete($filePath);
    }

    /**Store a file in the correspond filesystem and return the path/name. If file stored in the cloud and name if locally  */
    private function storeFile(Request $request): string
    {
        if (config('filesystems.default') == 's3')
            return $this->businessesStorage()->url($request->logoFile->store('', 'businesses-logo'));
        return $request->logoFile->store('', 'businesses-logo');
    }

    //to ignore an error of intelephense extension.
    private function businessesStorage(): FilesystemAdapter
    {
        return Storage::disk('businesses-logo');
    }
}
