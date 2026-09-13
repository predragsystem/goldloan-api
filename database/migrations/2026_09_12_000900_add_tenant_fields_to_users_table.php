<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('tenant_id')->nullable()->after('id')->constrained()->cascadeOnDelete();
            $table->foreignId('role_id')->nullable()->after('tenant_id')->constrained();
            $table->string('phone')->nullable()->unique()->after('email');
            $table->string('status')->default('active')->after('phone'); // active|disabled
            $table->string('email')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('tenant_id');
            $table->dropConstrainedForeignId('role_id');
            $table->dropColumn(['phone', 'status']);
        });
    }
};
