<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Student;
use App\Models\Mark;
use App\Models\Subject;

class LeaderController extends Controller
{
    public function dashboard(Request $request)
    {
        $leader = Auth::user();
        $schoolName = $leader->school_name ?: 'KOME SECONDARY SCHOOL';

        $availableClasses = [
            'Form 1', 'Form 2', 'Form 3', 'Form 4', 'Form 5', 'Form 6',
            'Standard 1', 'Standard 2', 'Standard 3', 'Standard 4', 'Standard 5', 'Standard 6', 'Standard 7'
        ];
        $selectedClass    = $request->input('class', 'Form 1');
        $selectedExam     = $request->input('exam_type', 'Weekly Test');

        $subjects = Subject::orderBy('subject_name')->get();

        // Fetch students in this class
        $students = Student::where(function ($q) use ($selectedClass) {
                $q->where('class_name', $selectedClass);
            })
            ->where(function ($q) use ($schoolName) {
                if ($schoolName) {
                    $q->where('school_name', $schoolName);
                }
            })
            ->orderBy('reg_number', 'asc')
            ->get();

        // Fetch marks for this exam/term and class
        $studentIds = $students->pluck('id');
        $marks = Mark::whereIn('student_id', $studentIds)
            ->where('term', $selectedExam)
            ->get();

        $marksGrouped = $marks->groupBy('student_id');

        $genderTotals = ['F' => 0, 'M' => 0];
        $divCounters = [
            'F' => ['I' => 0, 'II' => 0, 'III' => 0, 'IV' => 0, '0' => 0],
            'M' => ['I' => 0, 'II' => 0, 'III' => 0, 'IV' => 0, '0' => 0],
        ];
        $gpaCounters = [
            'F' => ['A' => 0, 'B' => 0, 'C' => 0, 'D' => 0, 'F' => 0],
            'M' => ['A' => 0, 'B' => 0, 'C' => 0, 'D' => 0, 'F' => 0],
        ];

        // Subject statistics
        $subjectStats = [];
        foreach ($subjects as $sub) {
            $subjectStats[$sub->id] = [
                'id'          => $sub->id,
                'name'        => $sub->subject_name,
                'grades'      => [
                    'F' => ['A' => 0, 'B' => 0, 'C' => 0, 'D' => 0, 'F' => 0],
                    'M' => ['A' => 0, 'B' => 0, 'C' => 0, 'D' => 0, 'F' => 0],
                ],
                'reg'         => ['F' => 0, 'M' => 0],
                'sat'         => ['F' => 0, 'M' => 0],
                'total_score' => ['F' => 0, 'M' => 0],
            ];
        }

        $latestExamDate = null;
        $studentsData   = [];

        foreach ($students as $stud) {
            $dbSex = strtoupper(trim($stud->sex)) === 'M' ? 'M' : 'F';
            $genderTotals[$dbSex]++;

            $studMarks = $marksGrouped->get($stud->id, collect());

            $scores      = [];
            $pointsArray = [];
            $totalScore  = 0;
            $count       = 0;

            foreach ($studMarks as $mk) {
                if ($mk->marks !== null && $mk->marks !== '') {
                    $scoreVal = (float)$mk->marks;
                    $scores[$mk->subject_id] = $scoreVal;
                    $totalScore += $scoreVal;
                    $count++;

                    $gInfo = $this->getGradeInfo($scoreVal);
                    if ($gInfo['P'] !== null) {
                        $pointsArray[] = $gInfo['P'];
                    }

                    if (isset($subjectStats[$mk->subject_id])) {
                        $subjectStats[$mk->subject_id]['sat'][$dbSex]++;
                        $subjectStats[$mk->subject_id]['total_score'][$dbSex] += $scoreVal;
                        if (isset($gInfo['G']) && isset($subjectStats[$mk->subject_id]['grades'][$dbSex][$gInfo['G']])) {
                            $subjectStats[$mk->subject_id]['grades'][$dbSex][$gInfo['G']]++;
                        }
                    }

                    if ($mk->exam_date) {
                        $latestExamDate = $mk->exam_date;
                    }
                }
            }

            $studentsData[$stud->id] = [
                'id'           => $stud->id,
                'reg_number'   => $stud->reg_number,
                'student_name' => $stud->student_name,
                'sex'          => $dbSex,
                'scores'       => $scores,
                'points_array' => $pointsArray,
                'total'        => $totalScore,
                'count'        => $count,
                'average'      => $count > 0 ? round($totalScore / $count, 2) : 0,
                'points'       => '-',
                'division'     => '-',
            ];
        }

        $examMonthName = $latestExamDate ? date('F Y', strtotime($latestExamDate)) : 'NOT CONDUCTED YET';

        // Division & GPA calculations
        foreach ($studentsData as $id => $data) {
            $s = $data['sex'];
            if ($data['count'] > 0) {
                $avg = $data['average'];
                $avgGrade = $this->getGradeInfo($avg)['G'];
                if (isset($gpaCounters[$s][$avgGrade])) {
                    $gpaCounters[$s][$avgGrade]++;
                }

                // Best 7 subjects for NECTA Points
                $pts = $data['points_array'];
                sort($pts);
                $best7 = array_slice($pts, 0, 7);
                $totalPoints = array_sum($best7);
                $studentsData[$id]['points'] = $totalPoints;

                $div = $this->calculateDivision($totalPoints);
                if (isset($divCounters[$s][$div])) {
                    $divCounters[$s][$div]++;
                }
                $studentsData[$id]['division'] = $div;
            }
        }

        // Rank Students by Average Score
        uasort($studentsData, function ($a, $b) {
            return $b['average'] <=> $a['average'];
        });

        $rank = 1;
        $prevAvg = null;
        $c = 0;
        foreach ($studentsData as $id => $d) {
            $c++;
            if ($prevAvg !== null && $d['average'] < $prevAvg) {
                $rank = $c;
            }
            $studentsData[$id]['rank'] = $d['count'] > 0 ? $rank : '-';
            $prevAvg = $d['average'];
        }

        // Reset to alphabetical order
        uasort($studentsData, function ($a, $b) {
            return strcasecmp($a['student_name'], $b['student_name']);
        });

        // Subject Breakdown Stats Processing
        foreach ($subjectStats as $subId => &$st) {
            $st['reg']['F'] = $genderTotals['F'];
            $st['reg']['M'] = $genderTotals['M'];
            $st['reg']['T'] = $st['reg']['F'] + $st['reg']['M'];

            $st['sat']['T'] = $st['sat']['F'] + $st['sat']['M'];
            $st['abs']['F'] = $st['reg']['F'] - $st['sat']['F'];
            $st['abs']['M'] = $st['reg']['M'] - $st['sat']['M'];
            $st['abs']['T'] = $st['reg']['T'] - $st['sat']['T'];

            foreach (['A', 'B', 'C', 'D', 'F'] as $g) {
                $st['grades']['T'][$g] = $st['grades']['F'][$g] + $st['grades']['M'][$g];
            }

            $st['ad_pass']['F'] = $st['grades']['F']['A'] + $st['grades']['F']['B'] + $st['grades']['F']['C'] + $st['grades']['F']['D'];
            $st['ad_pass']['M'] = $st['grades']['M']['A'] + $st['grades']['M']['B'] + $st['grades']['M']['C'] + $st['grades']['M']['D'];
            $st['ad_pass']['T'] = $st['ad_pass']['F'] + $st['ad_pass']['M'];

            $st['ad_pct']['F'] = $st['sat']['F'] > 0 ? round(($st['ad_pass']['F'] / $st['sat']['F']) * 100, 1) : 0;
            $st['ad_pct']['M'] = $st['sat']['M'] > 0 ? round(($st['ad_pass']['M'] / $st['sat']['M']) * 100, 1) : 0;
            $st['ad_pct']['T'] = $st['sat']['T'] > 0 ? round(($st['ad_pass']['T'] / $st['sat']['T']) * 100, 1) : 0;

            $totalScoreAll = $st['total_score']['F'] + $st['total_score']['M'];
            $st['avg'] = $st['sat']['T'] > 0 ? round($totalScoreAll / $st['sat']['T']) : 0;

            if ($st['sat']['T'] > 0) {
                $gpaPoints = ($st['grades']['T']['A'] * 1) +
                             ($st['grades']['T']['B'] * 2) +
                             ($st['grades']['T']['C'] * 3) +
                             ($st['grades']['T']['D'] * 4) +
                             ($st['grades']['T']['F'] * 5);
                $st['gpa'] = round($gpaPoints / $st['sat']['T'], 4);
            } else {
                $st['gpa'] = 0;
            }
        }
        unset($st);

        // Sort Subjects by GPA Ascending
        uasort($subjectStats, function ($a, $b) {
            if ($a['gpa'] == 0) return 1;
            if ($b['gpa'] == 0) return -1;
            return $a['gpa'] <=> $b['gpa'];
        });

        $psn = 1;
        foreach ($subjectStats as $subId => &$st) {
            $st['psn'] = $st['sat']['T'] > 0 ? $psn++ : '-';
        }
        unset($st);

        $totalDivI   = $divCounters['F']['I'] + $divCounters['M']['I'];
        $totalDivII  = $divCounters['F']['II'] + $divCounters['M']['II'];
        $totalDivIII = $divCounters['F']['III'] + $divCounters['M']['III'];
        $totalDivIV  = $divCounters['F']['IV'] + $divCounters['M']['IV'];
        $totalDiv0   = $divCounters['F']['0'] + $divCounters['M']['0'];
        $maxStudents = max(count($studentsData), 1);

        return view('leader.dashboard', compact(
            'schoolName',
            'availableClasses',
            'selectedClass',
            'selectedExam',
            'examMonthName',
            'subjects',
            'studentsData',
            'genderTotals',
            'divCounters',
            'gpaCounters',
            'totalDivI',
            'totalDivII',
            'totalDivIII',
            'totalDivIV',
            'totalDiv0',
            'maxStudents',
            'subjectStats'
        ));
    }

    public function getGradeInfo($score)
    {
        if ($score === null || $score === '') return ['G' => '-', 'C' => '#000', 'P' => null];
        if ($score >= 75) return ['G' => 'A', 'C' => '#16a34a', 'P' => 1];
        if ($score >= 65) return ['G' => 'B', 'C' => '#2563eb', 'P' => 2];
        if ($score >= 45) return ['G' => 'C', 'C' => '#ca8a04', 'P' => 3];
        if ($score >= 30) return ['G' => 'D', 'C' => '#ea580c', 'P' => 4];
        return ['G' => 'F', 'C' => '#dc2626', 'P' => 5];
    }

    public function calculateDivision($points)
    {
        if ($points === null || $points === '' || $points === '-') return '-';
        if ($points >= 34) return '0';
        if ($points >= 26) return 'IV';
        if ($points >= 22) return 'III';
        if ($points >= 18) return 'II';
        if ($points >= 7)  return 'I';
        return '-';
    }
}
