<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('grades:recalculate', function () {
    $marks = \App\Models\Mark::with('student')->get();
    $updated = 0;
    foreach ($marks as $m) {
        [$g, $r] = \App\Models\Mark::calculateGrade((float)$m->marks, $m->student);
        if ($m->grade !== $g || $m->remarks !== $r) {
            $m->grade = $g;
            $m->remarks = $r;
            $m->save();
            $updated++;
        }
    }
    $this->info("Total marks: {$marks->count()}, Updated: {$updated}");
})->purpose('Recalculate and update all student marks to match the grading scale (O-Level vs A-Level)');

Artisan::command('grades:test', function () {
    $leader = new \App\Http\Controllers\LeaderController();
    $casesOLevel = [
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

    $casesALevel = [
        ['score' => 100,  'expectedG' => 'A', 'expectedR' => 'Excellent',    'expectedP' => 1],
        ['score' => 80,   'expectedG' => 'A', 'expectedR' => 'Excellent',    'expectedP' => 1],
        ['score' => 79.9, 'expectedG' => 'B', 'expectedR' => 'Very Good',    'expectedP' => 2],
        ['score' => 70,   'expectedG' => 'B', 'expectedR' => 'Very Good',    'expectedP' => 2],
        ['score' => 69.9, 'expectedG' => 'C', 'expectedR' => 'Good',         'expectedP' => 3],
        ['score' => 60,   'expectedG' => 'C', 'expectedR' => 'Good',         'expectedP' => 3],
        ['score' => 59.9, 'expectedG' => 'D', 'expectedR' => 'Satisfactory', 'expectedP' => 4],
        ['score' => 50,   'expectedG' => 'D', 'expectedR' => 'Satisfactory', 'expectedP' => 4],
        ['score' => 49.9, 'expectedG' => 'E', 'expectedR' => 'Pass',         'expectedP' => 5],
        ['score' => 40,   'expectedG' => 'E', 'expectedR' => 'Pass',         'expectedP' => 5],
        ['score' => 39.9, 'expectedG' => 'S', 'expectedR' => 'Subsidiary',   'expectedP' => 6],
        ['score' => 36,   'expectedG' => 'S', 'expectedR' => 'Subsidiary',   'expectedP' => 6],
        ['score' => 35.9, 'expectedG' => 'F', 'expectedR' => 'Fail',         'expectedP' => 7],
        ['score' => 0,    'expectedG' => 'F', 'expectedR' => 'Fail',         'expectedP' => 7],
    ];

    $allPassed = true;
    $this->info("--- Testing O-Level Scale ---");
    foreach ($casesOLevel as $c) {
        [$modelG, $modelR] = \App\Models\Mark::calculateGrade($c['score'], false);
        $leaderInfo = $leader->getGradeInfo($c['score'], false);

        if ($modelG !== $c['expectedG'] || $modelR !== $c['expectedR'] || $leaderInfo['G'] !== $c['expectedG'] || $leaderInfo['P'] !== $c['expectedP']) {
            $this->error("O-Level FAILED for score {$c['score']}: Model=({$modelG}, {$modelR}), Leader=({$leaderInfo['G']}, {$leaderInfo['P']})");
            $allPassed = false;
        } else {
            $this->line("PASS O-Level: Score {$c['score']} => Grade {$modelG} ({$modelR}), Points {$leaderInfo['P']}");
        }
    }

    $this->info("--- Testing A-Level Scale ---");
    foreach ($casesALevel as $c) {
        [$modelG, $modelR] = \App\Models\Mark::calculateGrade($c['score'], true);
        $leaderInfo = $leader->getGradeInfo($c['score'], true);

        if ($modelG !== $c['expectedG'] || $modelR !== $c['expectedR'] || $leaderInfo['G'] !== $c['expectedG'] || $leaderInfo['P'] !== $c['expectedP']) {
            $this->error("A-Level FAILED for score {$c['score']}: Model=({$modelG}, {$modelR}), Leader=({$leaderInfo['G']}, {$leaderInfo['P']})");
            $allPassed = false;
        } else {
            $this->line("PASS A-Level: Score {$c['score']} => Grade {$modelG} ({$modelR}), Points {$leaderInfo['P']}");
        }
    }

    if ($allPassed) {
        $this->info("SUCCESS: All boundary tests (O-Level & A-Level) passed perfectly!");
    }
})->purpose('Test grading boundaries for O-Level and A-Level');
