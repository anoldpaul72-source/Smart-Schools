<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('school_incomes', function (Blueprint $table) {
            $table->id();
            $table->string('school_name');
            $table->string('category'); // Farm/Produce, Food Vendors, Frame Rent, Hall/Grounds, Other
            $table->string('source_title'); // Description of the project/source
            $table->string('payer_name')->nullable();
            $table->decimal('amount', 12, 2)->default(0.00);
            $table->date('payment_date');
            $table->string('receipt_number')->nullable();
            $table->string('payment_method')->default('Cash'); // Cash, Bank, Mobile Money
            $table->string('academic_year');
            $table->text('notes')->nullable();
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['school_name', 'academic_year']);
            $table->index('category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_incomes');
    }
};
