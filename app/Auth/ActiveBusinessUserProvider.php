<?php

namespace App\Auth;

use Illuminate\Auth\EloquentUserProvider;

/**
 * The user provider of the `web` guard. It finds only the accounts whose business
 * is not deleted. So no account of a deleted business (Owner or staff) can sign in,
 * keep an existing session, use a "remember me" cookie, or reset its password.
 * Deleted accounts are already excluded by the SoftDeletes scope of User.
 */
class ActiveBusinessUserProvider extends EloquentUserProvider
{
    protected function newModelQuery($model = null)
    {
        // `business` uses the SoftDeletes scope of Business: only a business that is not deleted.
        return parent::newModelQuery($model)->whereHas('business');
    }
}
