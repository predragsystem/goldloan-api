<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->string('business_name');
            $table->string('owner_name');
            $table->string('phone')->unique();
            $table->string('email')->nullable()->unique();
            $table->string('status')->default('trial'); // trial|active|expired|suspended
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tenants');
    }
};
