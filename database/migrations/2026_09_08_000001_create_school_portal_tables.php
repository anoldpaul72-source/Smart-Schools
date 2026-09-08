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
        // 1. Schools
        Schema::create('schools', function (Blueprint $table) {
            $table->id();
            $table->string('school_name')->unique();
            $table->string('address')->nullable();
            $table->string('phone')->nullable();
            $table->timestamps();
        });

        // 2. Subjects
        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->string('subject_name');
            $table->timestamps();
        });

        // 3. Students
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('reg_number')->unique();
            $table->string('student_name');
            $table->string('class_name');
            $table->string('sex', 10)->default('M');
            $table->string('school_name');
            $table->foreignId('parent_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // 4. Teacher Assignments
        Schema::create('teacher_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained('subjects')->cascadeOnDelete();
            $table->string('class_name');
            $table->string('school_name')->nullable();
            $table->timestamps();
        });

        // 5. Marks
        Schema::create('marks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained('subjects')->cascadeOnDelete();
            $table->string('term');
            $table->date('exam_date');
            $table->decimal('marks', 5, 2);
            $table->string('grade', 5)->nullable();
            $table->string('remarks')->nullable();
            $table->timestamps();
        });

        // 6. Attendance
        Schema::create('attendance', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->date('date');
            $table->string('status')->default('Present'); // Present, Absent, Late
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // 7. Timetables
        Schema::create('timetables', function (Blueprint $table) {
            $table->id();
            $table->string('school_name');
            $table->string('class_name');
            $table->string('day_of_week');
            $table->integer('period_number');
            $table->foreignId('subject_id')->constrained('subjects')->cascadeOnDelete();
            $table->foreignId('teacher_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });

        // 8. Fee Structures
        Schema::create('fee_structures', function (Blueprint $table) {
            $table->id();
            $table->string('school_name');
            $table->string('class_name');
            $table->string('academic_year');
            $table->decimal('total_amount', 12, 2)->default(0.00);
            $table->timestamps();
        });

        // 9. Student Payments
        Schema::create('student_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->string('academic_year');
            $table->decimal('amount_paid', 12, 2)->default(0.00);
            $table->date('payment_date');
            $table->string('receipt_number')->nullable();
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_payments');
        Schema::dropIfExists('fee_structures');
        Schema::dropIfExists('timetables');
        Schema::dropIfExists('attendance');
        Schema::dropIfExists('marks');
        Schema::dropIfExists('teacher_assignments');
        Schema::dropIfExists('students');
        Schema::dropIfExists('subjects');
        Schema::dropIfExists('schools');
    }
};
