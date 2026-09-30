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
        Schema::table('schools', function (Blueprint $table) {
            if (!Schema::hasColumn('schools', 'periods_per_day')) {
                $table->integer('periods_per_day')->default(10)->after('phone');
            }
            if (!Schema::hasColumn('schools', 'breakfast_time')) {
                $table->string('breakfast_time')->default('11:20 - 11:40')->after('periods_per_day');
            }
            if (!Schema::hasColumn('schools', 'breakfast_after_period')) {
                $table->integer('breakfast_after_period')->default(5)->after('breakfast_time');
            }
            if (!Schema::hasColumn('schools', 'lunch_time')) {
                $table->string('lunch_time')->default('14:20 - 15:00')->after('breakfast_after_period');
            }
            if (!Schema::hasColumn('schools', 'lunch_after_period')) {
                $table->integer('lunch_after_period')->default(9)->after('lunch_time');
            }
            if (!Schema::hasColumn('schools', 'class_start_time')) {
                $table->string('class_start_time')->default('08:00')->after('lunch_after_period');
            }
            if (!Schema::hasColumn('schools', 'period_duration')) {
                $table->integer('period_duration')->default(40)->after('class_start_time');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('schools', function (Blueprint $table) {
            $cols = [
                'periods_per_day',
                'breakfast_time',
                'breakfast_after_period',
                'lunch_time',
                'lunch_after_period',
                'class_start_time',
                'period_duration',
            ];
            foreach ($cols as $col) {
                if (Schema::hasColumn('schools', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
