<?php

namespace App\Http\Controllers\Auth;

use App\Mail\PendingRegistrationMail;
use App\Http\Controllers\Controller;
use App\Models\PartnershipApplication;
use App\Models\PendingRegistration;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class PendingRegistrationController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'suffix' => ['nullable', 'string', 'max:32'],
            'account_type' => ['required', 'in:student,concessionaire'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone_number' => ['nullable', 'regex:/^[0-9+\s]{7,20}$/', 'max:32'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        // Keep the chosen account type consistent with the email-based role logic:
        // a @cvsu.edu.ph address is always a student, anything else is a concessionaire.
        $emailIsStudent = str_ends_with(strtolower($validated['email']), '@cvsu.edu.ph');

        if ($validated['account_type'] === 'student' && ! $emailIsStudent) {
            return back()
                ->withInput($request->except('password', 'password_confirmation'))
                ->withErrors([
                    'email' => 'Students must register with their CvSU email address (ending in @cvsu.edu.ph).',
                ]);
        }

        if ($validated['account_type'] === 'concessionaire' && $emailIsStudent) {
            return back()
                ->withInput($request->except('password', 'password_confirmation'))
                ->withErrors([
                    'email' => 'A @cvsu.edu.ph address is reserved for students. Please use a different email to register as a concessionaire.',
                ]);
        }

        $pending = PendingRegistration::where('email', $validated['email'])->first();

        if ($pending && ! $pending->isExpired()) {
            return back()
                ->withInput($request->except('password', 'password_confirmation'))
                ->withErrors([
                    'email' => 'A confirmation email was already sent to this address. Please check your inbox or wait for it to expire before trying again.',
                ]);
        }

        do {
            $token = Str::random(64);
        } while (PendingRegistration::where('token', $token)->exists());

        $pending = PendingRegistration::updateOrCreate(
            ['email' => $validated['email']],
            [
                'token' => $token,
                'first_name' => trim($validated['first_name']),
                'middle_name' => isset($validated['middle_name']) ? trim($validated['middle_name']) : null,
                'last_name' => trim($validated['last_name']),
                'suffix' => isset($validated['suffix']) ? trim($validated['suffix']) : null,
                'phone_number' => isset($validated['phone_number']) ? trim($validated['phone_number']) : null,
                'password' => bcrypt($validated['password']),
                'expires_at' => now()->addHours(24),
            ]
        );

        Mail::to($pending->email)->send(new PendingRegistrationMail($pending));

        return back()->with('success', 'We sent a confirmation link to your email. Click it within 24 hours to complete your registration.');
    }

    public function confirm(Request $request, string $token)
    {
        $pending = PendingRegistration::where('token', $token)->first();

        if (! $pending) {
            return redirect('/register')->with('error', 'This confirmation link is invalid.');
        }

        if ($pending->isExpired()) {
            $pending->delete();

            return redirect('/register')->with('error', 'This confirmation link has expired. Please register again.');
        }

        if (User::where('email', $pending->email)->exists()) {
            $pending->delete();

            return redirect('/login')->with('status', 'Your account already exists. Please log in.');
        }

        $firstName = trim($pending->first_name);
        $middleName = $pending->middle_name ? trim($pending->middle_name) : null;
        $lastName = trim($pending->last_name);
        $suffix = $pending->suffix ? trim($pending->suffix) : null;
        $phoneNumber = $pending->phone_number ? trim($pending->phone_number) : null;
        $fullName = trim(preg_replace('/\s+/', ' ', implode(' ', array_filter([$firstName, $middleName, $lastName, $suffix]))));
        $isStudent = str_ends_with($pending->email, '@cvsu.edu.ph');

        $userId = User::query()->insertGetId([
            'name' => $fullName,
            'first_name' => $firstName,
            'middle_name' => $middleName,
            'last_name' => $lastName,
            'suffix' => $suffix,
            'email' => $pending->email,
            'phone_number' => $phoneNumber,
            'password' => $pending->password,
            'role' => $isStudent ? 'student' : 'concessionaire',
            'is_approved' => $isStudent,
            'is_active_concessionaire' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $user = User::findOrFail($userId);

        if (! $isStudent) {
            PartnershipApplication::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'first_name' => $firstName,
                    'middle_name' => $middleName,
                    'last_name' => $lastName,
                    'email' => $user->email,
                    'business_name' => $user->business_name ?? '',
                    'phone_number' => $phoneNumber,
                    'business_proposal' => null,
                    'phone' => $phoneNumber,
                    'proposal' => '',
                    'status' => 'pending',
                    'wizard_status' => 'loi_pending',
                ]
            );
        }

        $pending->delete();

        return redirect('/login')
            ->with('status', 'account-created')
            ->with('message', 'Your account has been created successfully. You can now log in.');
    }
}
