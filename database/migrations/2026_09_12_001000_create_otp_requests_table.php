<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('otp_requests', function (Blueprint $table) {
            $table->id();
            $table->string('phone');
            $table->string('otp_hash');
            $table->string('purpose'); // login|signup
            $table->timestamp('expires_at');
            $table->timestamp('consumed_at')->nullable();
            $table->unsignedTinyInteger('attempts')->default(0);
            $table->timestamps();

            $table->index(['phone', 'purpose']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('otp_requests');
    }
};
