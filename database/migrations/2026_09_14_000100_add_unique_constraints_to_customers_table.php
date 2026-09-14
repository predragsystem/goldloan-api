<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            // Same phone/aadhar can exist across different tenants (they're
            // different businesses' customers) — the uniqueness is per-tenant.
            // MySQL/MariaDB allow multiple NULLs in a unique index, so a
            // missing aadhar_no on several customers is not a conflict.
            $table->unique(['tenant_id', 'phone']);
            $table->unique(['tenant_id', 'aadhar_no']);
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropUnique(['tenant_id', 'phone']);
            $table->dropUnique(['tenant_id', 'aadhar_no']);
        });
    }
};
