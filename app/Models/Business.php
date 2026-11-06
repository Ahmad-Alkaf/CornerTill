<?php

namespace App\Models;

use App\Models\User;
use Laravel\Paddle\Billable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Business extends Model
{
    use HasFactory, HasUlids, SoftDeletes, Billable;

    public function onTrial()
    {
        return isset($this->customer) && isset($this->customer->trial_ends_at) && $this->customer->trial_ends_at->gt(now());
    }

    public function subscribedOrOnTrial()
    {
        // Free beta: every business has full access within the beta limits.
        if (!config('features.payments_enabled'))
            return true;
        return $this->onTrial() || ($this->subscribed() && (
            ($sub = $this->subscription())->recurring() || $sub->onGracePeriod() || $sub->onPausedGracePeriod()));
    }

    /**
     * Billable calls "$this->email" under the hood so we need to pass the owner's email, because Business model dose not have email property.
     */
    public function __get($key)
    {
        if ($key === 'email') {
            $user = $this->users()->where('role', 'Owner')->oldest('created_at')->first();
            return $user->email;
        }
        return $this->getAttribute($key);
    }

    /**
     * @param $subscribedTo is 'basic'|'enhanced'|'advanced'
     */
    public function progress(string $subscribedTo = null): null|array
    {
        // Free beta: the beta limits apply to every business, whatever plan is asked for.
        if (!config('features.payments_enabled'))
            return $this->progressFor(config('constants.betaResources'));

        //return null if subscribedTo not valid plan OR ( business is not subscribed AND trial expired )
        if (
            !(isset($subscribedTo) &&
                ($subscribedTo === 'advanced' || $subscribedTo === 'basic' || $subscribedTo === 'enhanced'))
        ) {
            if ($sub = $this->subscription()) {
                if ($sub->hasProduct(config('services.paddle.products.advanced')))
                    $subscribedTo = 'advanced';
                else if ($sub->hasProduct(config('services.paddle.products.enhanced')))
                    $subscribedTo = 'enhanced';
                else if ($sub->hasProduct(config('services.paddle.products.basic')))
                    $subscribedTo = 'basic';
            } else if ($this->onTrial()) { //trial considered a Basic Plan in terms of progress limit.
                $subscribedTo = 'basic';
            } else return null;
        }

        return $this->progressFor(config('constants.planResources.' . $subscribedTo));
    }

    /**
     * @param array{accounts:int,products:int,bills:int} $max the resource limits
     */
    private function progressFor(array $max): array
    {
        $accounts = $this->users()->count();
        $products = $this->products()->count();
        $bills    = $this->bills()->whereBetween('updated_at', [now()->subMonth(), now()])->count();

        return [
            'accounts' => [
                'reached' => $accounts,
                'max' => $max['accounts']
            ],
            'products' => [
                'reached' => $products,
                'max' => $max['products']
            ],
            'bills' => [
                'reached' => $bills,
                'max' => $max['bills']
            ],
        ];
    }

    /**
     * Message for HTTP 403 when a business reached a resource limit.
     * @param $resource is 'accounts'|'products'|'bills'
     */
    public static function limitReachedMessage(string $resource): string
    {
        if (!config('features.payments_enabled'))
            return __('You reached the beta limit. Contact support@kaflabs.com for more.');
        return __('You have reached the maximum number of :resource that you can create with your current plan. To create more :resource, you can upgrade your plan, delete some existing :resource, or contact our support team for assistance (support@kaflabs.com).', ['resource' => __($resource)]);
    }

    /**
     * Get progress status, or abort with 403 and (you have to subscribe) message
     */
    public function progressOrFail(string $subscribedTo = null): array
    {
        if (($p = $this->progress($subscribedTo)) != null) {
            return $p;
        }
        abort(403, __('You need to have an active subscription to perform this action!'));
    }

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'business';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'logo',
        'country',
        'city',
        'address',
        'currency',
        'phone',
        'countryCallingCode',
        'taxPercent',
        'taxIdentificationNumber',
    ];


    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'deleted_at',
    ];


    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'taxPercent' => 'float'
    ];


    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function bills(): HasMany
    {
        return $this->hasMany(Bill::class);
    }
}
