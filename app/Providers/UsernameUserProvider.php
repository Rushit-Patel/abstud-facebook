<?php

namespace App\Providers;

use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Contracts\Auth\Authenticatable as UserContract;

class UsernameUserProvider extends EloquentUserProvider
{
    /**
     * Retrieve a user by the given credentials.
     */
    public function retrieveByCredentials(array $credentials)
    {
        if (empty($credentials) ||
           (count($credentials) === 1 &&
            array_key_exists('password', $credentials))) {
            return;
        }

        $query = $this->newModelQuery();

        foreach ($credentials as $key => $value) {
            if ($key === 'password') {
                continue;
            }

            if (is_array($value) || $value instanceof \Arrayable) {
                $query->whereIn($key, $value);
            } else {
                $query->where($key, $value);
            }
        }

        // Add active user check
        $user = $query->where('is_active', true)->first();

        return $user;
    }

    /**
     * Validate a user against the given credentials.
     */
    public function validateCredentials(UserContract $user, array $credentials)
    {
        // Additional check for active status
        if (isset($user->is_active) && !$user->is_active) {
            return false;
        }

        return parent::validateCredentials($user, $credentials);
    }
}