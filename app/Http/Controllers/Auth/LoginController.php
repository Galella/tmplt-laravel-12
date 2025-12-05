<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle a login request.
     */
    public function login(Request $request)
    {
        // Validate the login request
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Check if the user has too many failed login attempts
        if ($this->tooManyLoginAttempts($request)) {
            $this->sendLockoutResponse($request);
        }

        // Attempt to log the user in
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            // Regenerate the session to prevent session fixation
            $request->session()->regenerate();

            // Clear login attempts after successful login
            $this->clearLoginAttempts($request);

            // Redirect to intended page or dashboard
            return redirect()->intended('/dashboard');
        }

        // If login attempt fails
        $this->incrementLoginAttempts($request);

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->withInput($request->only('email'));
    }

    /**
     * Log the user out of the application.
     */
    public function logout(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('status', 'You have been logged out successfully.');
    }

    /**
     * Increment the login attempts for the user.
     */
    protected function incrementLoginAttempts(Request $request)
    {
        $key = $this->throttleKey($request);

        RateLimiter::hit($key);
    }

    /**
     * Determine if the user has too many failed login attempts.
     */
    protected function tooManyLoginAttempts(Request $request)
    {
        $key = $this->throttleKey($request);

        return RateLimiter::tooManyAttempts($key, 5); // 5 attempts
    }

    /**
     * Get the throttle key for the request.
     */
    protected function throttleKey(Request $request)
    {
        return md5(implode('|', [
            $request->ip(),
            $request->input('email')
        ]));
    }

    /**
     * Send the lockout response.
     */
    protected function sendLockoutResponse(Request $request)
    {
        $seconds = RateLimiter::availableIn($this->throttleKey($request));

        throw ValidationException::withMessages([
            'email' => [trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ])],
        ]);
    }

    /**
     * Clear the login attempts for the user.
     */
    protected function clearLoginAttempts(Request $request)
    {
        RateLimiter::clear($this->throttleKey($request));
    }
}