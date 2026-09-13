<?php

namespace App\Services;

use App\Contracts\SmsGateway;
use App\Models\OtpRequest;
use Illuminate\Support\Facades\Hash;

class OtpService
{
    private const LENGTH = 6;

    private const TTL_MINUTES = 5;

    private const MAX_ATTEMPTS = 5;

    public function __construct(private SmsGateway $sms) {}

    /**
     * Generate and send an OTP for the given phone + purpose ("signup"|"login").
     */
    public function send(string $phone, string $purpose): void
    {
        $otp = (string) random_int(100000, 999999);

        OtpRequest::create([
            'phone' => $phone,
            'otp_hash' => Hash::make($otp),
            'purpose' => $purpose,
            'expires_at' => now()->addMinutes(self::TTL_MINUTES),
        ]);

        $this->sms->send($phone, "Your GoldLoan verification code is {$otp}. Valid for ".self::TTL_MINUTES.' minutes.');
    }

    /**
     * Verify an OTP. Returns true/false; increments attempts on every call
     * so a request can be locked out after MAX_ATTEMPTS wrong guesses.
     */
    public function verify(string $phone, string $purpose, string $otp): bool
    {
        $request = OtpRequest::where('phone', $phone)
            ->where('purpose', $purpose)
            ->whereNull('consumed_at')
            ->where('expires_at', '>', now())
            ->latest('id')
            ->first();

        if (! $request || $request->attempts >= self::MAX_ATTEMPTS) {
            return false;
        }

        $request->increment('attempts');

        if (! Hash::check($otp, $request->otp_hash)) {
            return false;
        }

        $request->update(['consumed_at' => now()]);

        return true;
    }
}
