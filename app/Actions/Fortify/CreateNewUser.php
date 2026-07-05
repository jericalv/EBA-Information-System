<?php

namespace App\Actions\Fortify;

use App\Concerns\PasswordValidationRules;
use App\Concerns\ProfileValidationRules;
use App\Mail\WelcomeMail;
use App\Models\PartnershipApplication;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules, ProfileValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => $this->emailRules(),
            'password' => $this->passwordRules(),
        ])->validate();

        $firstName = trim((string) ($input['first_name'] ?? ''));
        $lastName = trim((string) ($input['last_name'] ?? ''));
        $fullName = trim($firstName . ' ' . $lastName);

        $user = User::create([
            'name' => $fullName,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $input['email'],
            'password' => $input['password'],
            'role' => str_ends_with($input['email'], '@cvsu.edu.ph') ? 'student' : 'concessionaire',
            'is_approved' => str_ends_with($input['email'], '@cvsu.edu.ph') ? true : false,
            'is_active_concessionaire' => false,
        ]);

        if (!str_ends_with($input['email'], '@cvsu.edu.ph')) {
            PartnershipApplication::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'first_name' => $firstName,
                    'middle_name' => null,
                    'last_name' => $lastName,
                    'email' => $user->email,
                    'business_name' => $user->business_name ?? '',
                    'phone_number' => null,
                    'business_proposal' => null,
                    'phone' => null,
                    'proposal' => '',
                    'status' => 'pending',
                ]
            );
        }

        try {
            Mail::to($user->email)->send(new WelcomeMail($user));
        } catch (\Exception $e) {
            Log::warning('Welcome mail failed: ' . $e->getMessage(), [
                'action' => self::class,
                'user_id' => $user->id,
                'email' => $user->email,
            ]);
        }

        return $user;
    }
}
