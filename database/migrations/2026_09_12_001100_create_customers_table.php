<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('gender')->nullable();
            $table->string('phone');
            $table->string('address')->nullable();
            $table->string('aadhar_no')->nullable();
            $table->string('photo_url')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'phone']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
