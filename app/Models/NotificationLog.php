<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationLog extends Model
{
    use HasFactory;

    protected $table = 'notification_log';

    protected $fillable = ['tenant_id', 'channel', 'type', 'payload', 'status', 'sent_at'];

    protected function casts(): array
    {
        return ['payload' => 'array', 'sent_at' => 'datetime'];
    }
}
