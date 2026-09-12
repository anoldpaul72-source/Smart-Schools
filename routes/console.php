<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('grades:recalculate', function () {
    $marks = \App\Models\Mark::all();
    $updated = 0;
    foreach ($marks as $m) {
        [$g, $r] = \App\Models\Mark::calculateGrade((float)$m->marks);
        if ($m->grade !== $g || $m->remarks !== $r) {
            $m->grade = $g;
            $m->remarks = $r;
            $m->save();
            $updated++;
        }
    }
    $this->info("Total marks: {$marks->count()}, Updated: {$updated}");
})->purpose('Recalculate and update all student marks to match the grading scale (75-100 A, 60-74 B, 45-59 C, 30-44 D, 0-29 F)');

Artisan::command('grades:test', function () {
    $leader = new \App\Http\Controllers\LeaderController();
    $cases = [
        ['score' => 100,  'expectedG' => 'A', 'expectedR' => 'Excellent', 'expectedP' => 1],
        ['score' => 75,   'expectedG' => 'A', 'expectedR' => 'Excellent', 'expectedP' => 1],
        ['score' => 74.9, 'expectedG' => 'B', 'expectedR' => 'Very Good', 'expectedP' => 2],
        ['score' => 60,   'expectedG' => 'B', 'expectedR' => 'Very Good', 'expectedP' => 2],
        ['score' => 59.9, 'expectedG' => 'C', 'expectedR' => 'Good',      'expectedP' => 3],
        ['score' => 45,   'expectedG' => 'C', 'expectedR' => 'Good',      'expectedP' => 3],
        ['score' => 44.9, 'expectedG' => 'D', 'expectedR' => 'Pass',      'expectedP' => 4],
        ['score' => 30,   'expectedG' => 'D', 'expectedR' => 'Pass',      'expectedP' => 4],
        ['score' => 29.9, 'expectedG' => 'F', 'expectedR' => 'Fail',      'expectedP' => 5],
        ['score' => 0,    'expectedG' => 'F', 'expectedR' => 'Fail',      'expectedP' => 5],
    ];

    $allPassed = true;
    foreach ($cases as $c) {
        [$modelG, $modelR] = \App\Models\Mark::calculateGrade($c['score']);
        $leaderInfo = $leader->getGradeInfo($c['score']);

        if ($modelG !== $c['expectedG'] || $modelR !== $c['expectedR'] || $leaderInfo['G'] !== $c['expectedG'] || $leaderInfo['P'] !== $c['expectedP']) {
            $this->error("FAILED for score {$c['score']}: Model=({$modelG}, {$modelR}), Leader=({$leaderInfo['G']}, {$leaderInfo['P']})");
            $allPassed = false;
        } else {
            $this->line("PASS: Score {$c['score']} => Grade {$modelG} ({$modelR}), Points {$leaderInfo['P']}");
        }
    }

    if ($allPassed) {
        $this->info("SUCCESS: All 10 boundary tests passed perfectly!");
    }
})->purpose('Test grading boundaries');
