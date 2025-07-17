<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Show the login form
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle login with security measures
     */
    public function login(Request $request)
    {
        // Rate limiting
        $key = 'login.' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            Transaction::log(
                Transaction::TYPE_FAILED_LOGIN,
                'Rate limit exceeded',
                "Too many login attempts from IP: {$request->ip()}",
                ['ip' => $request->ip(), 'remaining_seconds' => $seconds],
                null,
                null,
                Transaction::SEVERITY_WARNING
            );
            return back()->withErrors(['email' => "Too many login attempts. Try again in {$seconds} seconds."]);
        }

        // Input validation with sanitization
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'max:255'],
        ]);

        if ($validator->fails()) {
            RateLimiter::hit($key);
            Transaction::logFailedLogin($request->email);
            return back()->withErrors($validator)->withInput($request->only('email'));
        }

        $credentials = [
            'email' => filter_var($request->email, FILTER_SANITIZE_EMAIL),
            'password' => $request->password,
            'is_active' => true
        ];

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();
            RateLimiter::clear($key);
            
            $user = Auth::user();
            $user->last_login_at = now();
            $user->save();

            // Log successful login
            Transaction::logLogin($user);

            return redirect()->intended('/dashboard');
        }

        // Failed login attempt
        RateLimiter::hit($key);
        Transaction::logFailedLogin($request->email);

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records or account is inactive.',
        ])->withInput($request->only('email'));
    }

    /**
     * Show registration form
     */
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    /**
     * Handle registration with validation
     */
    public function register(Request $request)
    {
        // Rate limiting for registration
        $key = 'register.' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, 3)) {
            $seconds = RateLimiter::availableIn($key);
            return back()->withErrors(['email' => "Too many registration attempts. Try again in {$seconds} seconds."]);
        }

        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s]+$/'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed', 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]/'],
            'role' => ['required', 'in:renter,owner,broker'],
            'phone' => ['nullable', 'string', 'max:20', 'regex:/^[\+]?[1-9][\d]{0,15}$/'],
            'address' => ['nullable', 'string', 'max:1000'],
        ], [
            'password.regex' => 'Password must contain at least one uppercase, lowercase, digit, and special character.',
            'name.regex' => 'Name must contain only letters and spaces.',
            'phone.regex' => 'Phone number format is invalid.',
        ]);

        if ($validator->fails()) {
            RateLimiter::hit($key);
            return back()->withErrors($validator)->withInput($request->except('password', 'password_confirmation'));
        }

        // Sanitize input data
        $userData = [
            'name' => strip_tags(trim($request->name)),
            'email' => filter_var($request->email, FILTER_SANITIZE_EMAIL),
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'phone' => $request->phone ? preg_replace('/[^+\d]/', '', $request->phone) : null,
            'address' => $request->address ? strip_tags(trim($request->address)) : null,
            'is_active' => true,
        ];

        $user = User::create($userData);

        RateLimiter::clear($key);

        // Log registration
        Transaction::log(
            Transaction::TYPE_LOGIN,
            'User registered',
            "New user registered: {$user->name} ({$user->email}) as {$user->role}",
            ['user_id' => $user->id, 'role' => $user->role],
            $user
        );

        Auth::login($user);

        return redirect('/dashboard')->with('success', 'Registration successful! Welcome to ChoziPay.');
    }

    /**
     * Handle logout
     */
    public function logout(Request $request)
    {
        $user = Auth::user();
        
        Transaction::log(
            Transaction::TYPE_LOGOUT,
            'User logged out',
            "User {$user->name} logged out",
            ['user_id' => $user->id],
            $user
        );

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    /**
     * Show dashboard
     */
    public function dashboard()
    {
        $user = Auth::user();
        
        $data = [
            'user' => $user,
            'recentTransactions' => Transaction::byUser($user->id)->recent()->limit(10)->get(),
        ];

        switch ($user->role) {
            case User::ROLE_BROKER:
                $data['choziCode'] = $user->getActiveChoziCode();
                $data['totalCommissions'] = $user->brokerCommissions()->completed()->sum('broker_commission');
                break;
            case User::ROLE_OWNER:
                $data['receivedPayments'] = $user->receivedPayments()->completed()->sum('net_amount');
                $data['pendingPayments'] = $user->receivedPayments()->pending()->count();
                break;
            case User::ROLE_RENTER:
                $data['sentPayments'] = $user->sentPayments()->completed()->sum('amount');
                $data['pendingPayments'] = $user->sentPayments()->pending()->count();
                break;
        }

        return view('dashboard', $data);
    }

    /**
     * Change password with security validation
     */
    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => ['required'],
            'password' => ['required', 'string', 'min:8', 'confirmed', 'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]/'],
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        Transaction::log(
            Transaction::TYPE_PASSWORD_CHANGE,
            'Password changed',
            "User {$user->name} changed password",
            ['user_id' => $user->id],
            $user
        );

        return back()->with('success', 'Password changed successfully.');
    }
}
