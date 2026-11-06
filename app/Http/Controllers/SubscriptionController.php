<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function plans()
    {
        return Inertia::render('Authenticated/Plans/index', ['plansMaxRes' => config('constants.planResources')]);
    }

    public function subscribe(Request $request)
    {
        $prices = [
            'Basic' => [
                'Monthly' => config('services.paddle.prices.basic.monthly'),
                'Annually' =>  config('services.paddle.prices.basic.annually')
            ], 'Enhanced' => [
                'Monthly' => config('services.paddle.prices.enhanced.monthly'),
                'Annually' => config('services.paddle.prices.enhanced.annually')
            ],
            'Advanced' => [
                'Monthly' => config('services.paddle.prices.advanced.monthly'),
                'Annually' => config('services.paddle.prices.advanced.annually')
            ],
        ];
        if (
            isset(($prices[$request->query('plan')][$request->query('period')]))
        ) {
            $plan = $prices[$request->query('plan')][$request->query('period')];
            $planOption = $request->user()->business
                ->subscribe($plan)
                ->returnTo(route('business.edit'))->options();
            $planOption['settings']['displayMode'] = 'inline';
            $planOption['settings']['theme'] = 'light';
            $planOption['settings']['frameTarget'] = 'checkout-container';
            $planOption['settings']['frameInitialHeight'] = '600';
            return Inertia::render('Authenticated/Subscribe/index', ['planOption' => $planOption, 'plansMaxRes' => config('constants.planResources')]);
        }
        return abort(404);
    }


    public function pause(Request $request)
    {
        $request->user()->business->subscription()->pause();

        return redirect()->back()->with('success', 'Successfully paused');
    }

    public function pauseNow(Request $request)
    {
        $request->user()->business->subscription()->pauseNow();
        return redirect()->back()->with('success', 'Successfully paused');
    }

    public function resume(Request $request)
    {
        if (($sub = $request->user()->business->subscription())->onPausedGracePeriod() || $sub->paused())
            $sub->resume();
        else abort(400, 'Your subscription has not paused in the first place to be resumed');
        return redirect()->back()->with('success', 'Successfully resumed');
    }

    public function updatePaymentMethod(Request $request)
    {
        return $request->user()->business
            ->subscription()
            ->redirectToUpdatePaymentMethod()->with('success', 'Successfully updated');
    }

    public function swapToAdvanced(Request $request, string $period)
    {
        if (isset($period)) {
            $period = strtolower($period);
            if ($period !== 'monthly' && $period !== 'annually')
                abort(400);
        } else abort(400);
        $advancedPrices = [config('services.paddle.prices.advanced.monthly'), config('services.paddle.prices.advanced.annually')];
        $request->user()->business
            ->subscription()
            ->swapAndInvoice($advancedPrices[$period === 'monthly' ? 0 : 1]);
        return redirect()->back()->with('success', 'Successfully upgraded');
    }

    public function swapToEnhanced(Request $request, string $period)
    {

        if (isset($period)) {
            $period = strtolower($period);
            if ($period !== 'monthly' && $period !== 'annually')
                abort(400);
        } else abort(400);
        $enhancedPrices = [config('services.paddle.prices.enhanced.monthly'), config('services.paddle.prices.enhanced.annually')];
        $request->user()->business
            ->subscription()
            ->swapAndInvoice($enhancedPrices[$period === 'monthly' ? 0 : 1]);
        return redirect()->back()->with('success', 'Successfully updated');
    }

    public function swapToBasic(Request $request, string $period)
    {
        if (isset($period)) {
            $period = strtolower($period);
            if ($period !== 'monthly' && $period !== 'annually')
                abort(400);
        } else abort(400);
        $basicPrices    = [config('services.paddle.prices.basic.monthly'), config('services.paddle.prices.basic.annually')];
        $request->user()->business
            ->subscription()
            ->swapAndInvoice($basicPrices[$period === 'monthly' ? 0 : 1]);
        return redirect()->back()->with('success', 'Successfully downgraded');
    }
}
