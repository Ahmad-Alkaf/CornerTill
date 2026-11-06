<?php

namespace App\Policies;

use App\Models\Business;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class BusinessPolicy
{

    public function edit(User $user, Business $business)
    {
        return $user->business_id == $business->id && $user->role == 'Owner';
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Business $business): bool
    {
        return $user->business_id == $business->id && $user->role == 'Owner';
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function destroy(User $user, Business $business): bool
    {
        if ($user->business_id == $business->id && $user->role == 'Owner') {
            $usersCount = $user->business->users()->count();
            if ($usersCount > 1) {
                throw ValidationException::withMessages(['serverError' => __('You have to delete all accounts within your business. There are :count accounts remaining.', ['count' => $usersCount - 1])]);
            }
            return true;
        }
        return false;
    }
}
