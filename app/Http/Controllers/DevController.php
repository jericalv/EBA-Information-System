<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

// DEV ONLY - DELETE THIS FILE BEFORE DEPLOYING
class DevController extends Controller
{
    public function showForm(): View
    {
        $users = User::orderByDesc('id')->get();

        return view('dev.test-accounts', compact('users'));
    }

    public function createAccount(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['required', 'in:admin,concessionaire,cashier'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
            'role' => $validated['role'],
        ]);

        return redirect()->route('dev.test-accounts')->with([
            'createdAccount' => [
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'password' => $validated['password'],
            ],
        ]);
    }
}
