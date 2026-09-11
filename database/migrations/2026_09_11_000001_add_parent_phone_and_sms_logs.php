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
        // 1. Add parent_phone to students table if not exists
        if (!Schema::hasColumn('students', 'parent_phone')) {
            Schema::table('students', function (Blueprint $table) {
                $table->string('parent_phone')->nullable()->after('parent_id');
            });
        }

        // 2. Add phone to users table if not exists
        if (!Schema::hasColumn('users', 'phone')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('phone')->nullable()->after('email');
            });
        }

        // 3. Create sms_logs table
        if (!Schema::hasTable('sms_logs')) {
            Schema::create('sms_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('student_id')->nullable()->constrained('students')->nullOnDelete();
                $table->string('recipient_phone');
                $table->string('recipient_name')->nullable();
                $table->text('message');
                $table->string('status')->default('sent'); // sent, simulated, failed
                $table->string('response_code')->nullable();
                $table->text('error_message')->nullable();
                $table->foreignId('sent_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sms_logs');

        if (Schema::hasColumn('users', 'phone')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('phone');
            });
        }

        if (Schema::hasColumn('students', 'parent_phone')) {
            Schema::table('students', function (Blueprint $table) {
                $table->dropColumn('parent_phone');
            });
        }
    }
};
