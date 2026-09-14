<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\OtpRequestFormRequest;
use App\Http\Requests\OtpVerifyRequest;
use App\Http\Requests\SignupRequest;
use App\Models\Role;
use App\Models\Tenant;
use App\Models\User;
use App\Services\OtpService;
use App\Support\TenantDefaults;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * Session-based (web guard) equivalent of Api\AuthController. Deliberately
 * reuses the same underlying models/services (OtpService, TenantDefaults)
 * rather than making an HTTP call to our own JSON API — same business
 * logic, different delivery (session cookie here, Bearer token there).
 */
class AuthController extends Controller
{
    public function __construct(private OtpService $otp) {}

    public function showSignup()
    {
        return view('auth.signup');
    }

    public function signup(SignupRequest $request)
    {
        $tenant = DB::transaction(function () use ($request) {
            $tenant = Tenant::create([
                'business_name' => $request->business_name,
                'owner_name' => $request->owner_name,
                'phone' => $request->phone,
                'email' => $request->email,
                'status' => 'trial',
            ]);

            $ownerRole = Role::whereNull('tenant_id')->where('slug', 'owner')->firstOrFail();

            User::create([
                'tenant_id' => $tenant->id,
                'role_id' => $ownerRole->id,
                'name' => $request->owner_name,
                'phone' => $request->phone,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'status' => 'active',
            ]);

            TenantDefaults::seed($tenant);

            return $tenant;
        });

        $this->otp->send($request->phone, 'signup');

        return redirect()->route('otp.show', ['phone' => $request->phone, 'purpose' => 'signup']);
    }

    public function showOtp(string $phone, string $purpose)
    {
        return view('auth.otp-verify', compact('phone', 'purpose'));
    }

    public function requestOtp(OtpRequestFormRequest $request)
    {
        if ($request->purpose === 'login' && ! User::where('phone', $request->phone)->exists()) {
            return back()->withErrors(['phone' => 'No account found with that phone number.']);
        }

        $this->otp->send($request->phone, $request->purpose);

        return redirect()->route('otp.show', ['phone' => $request->phone, 'purpose' => $request->purpose]);
    }

    public function verifyOtp(OtpVerifyRequest $request)
    {
        if (! $this->otp->verify($request->phone, $request->purpose, $request->otp)) {
            return back()->withErrors(['otp' => 'Invalid or expired code.']);
        }

        $user = User::where('phone', $request->phone)->firstOrFail();
        Auth::login($user, remember: true);

        return redirect()->route('dashboard');
    }

    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(LoginRequest $request)
    {
        $user = User::where('phone', $request->identifier)
            ->orWhere('email', $request->identifier)
            ->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return back()->withErrors(['identifier' => 'Those credentials don\'t match an account.']);
        }

        if ($user->status !== 'active') {
            return back()->withErrors(['identifier' => 'This account has been disabled.']);
        }

        Auth::login($user, remember: true);

        return redirect()->route('dashboard');
    }

    public function logout()
    {
        Auth::logout();

        return redirect()->route('login');
    }
}
