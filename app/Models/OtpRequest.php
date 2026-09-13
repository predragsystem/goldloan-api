<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OtpRequest extends Model
{
    use HasFactory;

    protected $fillable = ['phone', 'otp_hash', 'purpose', 'expires_at', 'consumed_at', 'attempts'];

    protected function casts(): array
    {
        return ['expires_at' => 'datetime', 'consumed_at' => 'datetime'];
    }
}
