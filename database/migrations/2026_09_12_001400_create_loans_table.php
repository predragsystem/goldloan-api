<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('loan_no'); // tenant-scoped display number, assigned in app code
            $table->foreignId('customer_id')->constrained();
            $table->date('loan_date');
            $table->unsignedBigInteger('principal_paise');
            $table->decimal('interest_rate_percent', 5, 2);
            $table->string('status')->default('active'); // active|closed|overdue
            $table->unsignedBigInteger('outstanding_balance_paise')->default(0);
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();

            $table->unique(['tenant_id', 'loan_no']);
            $table->index(['tenant_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loans');
    }
};
