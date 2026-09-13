<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loan_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loan_id')->constrained()->cascadeOnDelete();
            $table->foreignId('jewellery_type_id')->constrained();
            $table->foreignId('jewellery_quality_id')->constrained('jewellery_qualities');
            $table->unsignedInteger('quantity')->default(1);
            $table->decimal('total_grams', 10, 4);
            $table->string('description')->nullable();
            $table->string('photo_url')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loan_items');
    }
};
