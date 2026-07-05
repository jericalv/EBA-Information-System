<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class RejectRegistration implements CreatesNewUsers
{
    /**
     * Prevent direct Fortify registration; accounts are created via email confirmation.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        throw ValidationException::withMessages([
            'email' => 'Please submit the registration form and confirm your email to complete account creation.',
        ]);
    }
}
