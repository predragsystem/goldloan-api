<?php

namespace App\Contracts;

interface SmsGateway
{
    /**
     * Send a plain-text SMS to an Indian phone number.
     */
    public function send(string $phone, string $message): void;
}
