<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\OtpRequestFormRequest;
use App\Http\Requests\OtpVerifyRequest;
use App\Http\Requests\SignupRequest;
use App\Models\Role;
use App\Models\Tenant;
use App\Models\User;
use App\Services\OtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function __construct(private OtpService $otp) {}

    /**
     * Self-serve tenant signup. Creates the tenant + an Owner user, then
     * sends an OTP to confirm the phone number before any token is issued.
     * No token is returned here on purpose — call /auth/otp/verify next.
     */
    public function signup(SignupRequest $request)
    {
        $tenant = DB::transaction(function () use ($request) {
            $tenant = Tenant::create([
                'business_name' => $request->business_name,
                'owner_name' => $request->owner_name,
                'phone' => $request->phone,
                'email' => $request->email,
                'status' => 'trial', // becomes "active" once billing/webhook confirms first payment
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

            return $tenant;
        });

        $this->otp->send($request->phone, 'signup');

        return response()->json([
            'message' => 'Account created. Enter the OTP sent to your phone to verify and continue to billing.',
            'tenant_id' => $tenant->id,
        ], 201);
    }

    public function requestOtp(OtpRequestFormRequest $request)
    {
        if ($request->purpose === 'login' && ! User::where('phone', $request->phone)->exists()) {
            // Don't reveal whether a phone is registered.
            return response()->json(['message' => 'If that number is registered, an OTP has been sent.']);
        }

        $this->otp->send($request->phone, $request->purpose);

        return response()->json(['message' => 'OTP sent.']);
    }

    public function verifyOtp(OtpVerifyRequest $request)
    {
        if (! $this->otp->verify($request->phone, $request->purpose, $request->otp)) {
            return response()->json(['message' => 'Invalid or expired OTP.'], 422);
        }

        $user = User::where('phone', $request->phone)->firstOrFail();

        return $this->issueTokenResponse($user);
    }

    public function login(LoginRequest $request)
    {
        $user = User::where('phone', $request->identifier)
            ->orWhere('email', $request->identifier)
            ->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials.'], 422);
        }

        if ($user->status !== 'active') {
            return response()->json(['message' => 'This account has been disabled.'], 403);
        }

        return $this->issueTokenResponse($user);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out.']);
    }

    private function issueTokenResponse(User $user)
    {
        $token = $user->createToken('mobile')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'role' => $user->role?->slug,
            ],
            'tenant' => [
                'id' => $user->tenant->id,
                'business_name' => $user->tenant->business_name,
                'status' => $user->tenant->status,
                'subscription_active' => $user->tenant->isSubscriptionActive(),
            ],
        ]);
    }
}
