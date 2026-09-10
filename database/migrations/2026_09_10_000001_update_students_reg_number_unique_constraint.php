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
        Schema::table('students', function (Blueprint $table) {
            // Drop single-column unique constraint so different classes can share registration numbers
            $table->dropUnique('students_reg_number_unique');

            // Add compound unique constraint per school, class, and reg_number
            $table->unique(['school_name', 'class_name', 'reg_number'], 'students_school_class_reg_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropUnique('students_school_class_reg_unique');
            $table->unique('reg_number', 'students_reg_number_unique');
        });
    }
};
