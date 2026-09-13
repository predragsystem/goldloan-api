<?php

namespace App\Services;

use App\Contracts\SmsGateway;
use Illuminate\Support\Facades\Log;

/**
 * Default SMS gateway used until a real provider (e.g. MSG91) is wired up.
 * Writes the message to the log instead of actually sending it, so OTP
 * flows are fully testable in local/dev before a provider is chosen.
 *
 * Swap the App\Contracts\SmsGateway binding in AppServiceProvider to a real
 * implementation (e.g. Msg91SmsGateway) when ready — nothing else changes.
 */
class LogSmsGateway implements SmsGateway
{
    public function send(string $phone, string $message): void
    {
        Log::info("[SMS to {$phone}] {$message}");
    }
}
