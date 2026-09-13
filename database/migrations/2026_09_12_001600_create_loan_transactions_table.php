<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loan_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loan_id')->constrained()->cascadeOnDelete();
            $table->string('type'); // disbursement|top_up|interest_payment|closure
            $table->unsignedBigInteger('amount_paise');
            $table->unsignedBigInteger('principal_component_paise')->default(0);
            $table->unsignedBigInteger('interest_component_paise')->default(0);
            $table->unsignedBigInteger('balance_after_paise');
            $table->date('transaction_date');
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();

            $table->index(['loan_id', 'transaction_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loan_transactions');
    }
};
