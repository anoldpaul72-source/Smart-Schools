<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendance', function (Blueprint $table) {
            $table->integer('period_number')->nullable()->after('subject_id');
            $table->index(['student_id', 'date', 'period_number']);
        });
    }

    public function down(): void
    {
        Schema::table('attendance', function (Blueprint $table) {
            $table->dropIndex(['student_id', 'date', 'period_number']);
            $table->dropColumn('period_number');
        });
    }
};
