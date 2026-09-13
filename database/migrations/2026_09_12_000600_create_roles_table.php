<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            // null tenant_id = global default role (owner/cashier), seeded once and reused;
            // a tenant can later be given its own custom role with tenant_id set.
            $table->foreignId('tenant_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('name'); // owner, cashier, ...
            $table->string('slug');
            $table->boolean('is_default')->default(false);
            $table->timestamps();

            $table->unique(['tenant_id', 'slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
