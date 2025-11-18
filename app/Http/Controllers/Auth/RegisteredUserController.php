<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', 'min:6'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'user',
            'active' => true,
        ]);

        // Log the user in and send an email verification link.
        Auth::login($user);

        // Send email verification notification (user implements MustVerifyEmail)
        if (method_exists($user, 'sendEmailVerificationNotification')) {
            try {
                $user->sendEmailVerificationNotification();
            } catch (\Throwable $e) {
                // If mail configuration is missing, log and continue so user can still see the verification prompt.
                logger()->error('Failed to send verification email: ' . $e->getMessage());
            }
        }

        // Redirect to the email verification prompt page
        return redirect()->route('verification.notice');
    }
}
