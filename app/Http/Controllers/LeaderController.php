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
        $availableExams = [
            'Weekly Test',
            'Monthly Test',
            'Midterm Test',
            'Terminal Examination',
            'Annual Examination',
            'Joint / Pre-Mock',
            'Regional Mock',
            'Pre-Necta',
            'NECTA',
        ];
        $selectedClass       = $request->input('class', 'Form 1');
        $selectedExam        = $request->input('exam_type', 'Weekly Test');
        $selectedCombination = $request->filled('combination') ? strtoupper(trim($request->input('combination'))) : null;
        $isALevel            = Student::isClassALevel($selectedClass);
        $activeGrades        = $isALevel ? ['A', 'B', 'C', 'D', 'E', 'S', 'F'] : ['A', 'B', 'C', 'D', 'F'];
        $allCombinations     = Student::COMBINATIONS;

        $combinationSubjectsMap = [
            'HKL' => ['History', 'Kiswahili', 'English', 'General Studies', 'Basic Applied Mathematics'],
            'HGK' => ['History', 'Geography', 'Kiswahili', 'General Studies', 'Basic Applied Mathematics'],
            'HGL' => ['History', 'Geography', 'English', 'General Studies', 'Basic Applied Mathematics'],
            'HGE' => ['History', 'Geography', 'Economics', 'General Studies', 'Basic Applied Mathematics'],
            'PCM' => ['Physics', 'Chemistry', 'Advanced Mathematics', 'General Studies'],
            'PCB' => ['Physics', 'Chemistry', 'Biology', 'General Studies', 'Basic Applied Mathematics'],
            'PGM' => ['Physics', 'Geography', 'Advanced Mathematics', 'General Studies'],
            'CBG' => ['Chemistry', 'Biology', 'Geography', 'General Studies', 'Basic Applied Mathematics'],
            'CBA' => ['Chemistry', 'Biology', 'Agriculture', 'General Studies', 'Basic Applied Mathematics'],
            'CBN' => ['Chemistry', 'Biology', 'Food and Human Nutrition', 'General Studies', 'Basic Applied Mathematics'],
            'PMC' => ['Physics', 'Advanced Mathematics', 'Computer Science', 'General Studies'],
            'EGM' => ['Economics', 'Geography', 'Advanced Mathematics', 'General Studies'],
            'ECA' => ['Economics', 'Commerce', 'Accountancy', 'General Studies', 'Basic Applied Mathematics'],
            'KLF' => ['Kiswahili', 'English', 'French', 'General Studies', 'Basic Applied Mathematics'],
            'KEC' => ['Kiswahili', 'Economics', 'Commerce', 'General Studies', 'Basic Applied Mathematics'],
        ];

        // Fetch students in this class (filtered by combination if A-Level & specified)
        $studentsQuery = Student::where('class_name', $selectedClass)
            ->where(function ($q) use ($schoolName) {
                if ($schoolName) {
                    $q->where('school_name', $schoolName);
                }
            });

        if ($isALevel && !empty($selectedCombination)) {
            $studentsQuery->where(function ($q) use ($selectedCombination) {
                $q->where('combination', $selectedCombination)
                  ->orWhere('class_name', 'like', "%{$selectedCombination}%");
            });
        }

        $students = $studentsQuery->orderBy('reg_number', 'asc')->get();
        $studentIds = $students->pluck('id');

        // Fetch marks for this exam/term and students
        $rawMarks = Mark::whereIn('student_id', $studentIds)
            ->where('term', $selectedExam)
            ->with('subject')
            ->get();
        $distinctSubjectIdsWithMarks = $rawMarks->pluck('subject_id')->unique()->toArray();

        if ($isALevel && !empty($selectedCombination) && isset($combinationSubjectsMap[$selectedCombination])) {
            $targetSubNames = $combinationSubjectsMap[$selectedCombination];
            $subjects = Subject::academic()
                ->where(function ($q) use ($targetSubNames, $distinctSubjectIdsWithMarks) {
                    $q->whereIn('id', $distinctSubjectIdsWithMarks);
                    foreach ($targetSubNames as $tsn) {
                        $q->orWhere('subject_name', 'like', "%{$tsn}%");
                    }
                })
                ->get();

            // Custom sort: Combination principal subjects first, subsidiary next, others last
            $subjects = $subjects->sortBy(function ($sub) use ($targetSubNames) {
                $name = strtolower($sub->subject_name);
                if (str_contains($name, 'general studies')) return 80;
                if (str_contains($name, 'basic applied')) return 85;
                foreach ($targetSubNames as $idx => $tsn) {
                    if (str_contains($name, strtolower($tsn))) {
                        return $idx;
                    }
                }
                return 99;
            })->values();
        } else {
            // All academic subjects
            $subjects = Subject::academic()->orderBy('subject_name')->get();
        }

        $marks = $rawMarks->whereIn('subject_id', $subjects->pluck('id'));
        $marksGrouped = $marks->groupBy('student_id');

        $genderTotals = ['F' => 0, 'M' => 0];
        $divCounters = [
            'F' => ['I' => 0, 'II' => 0, 'III' => 0, 'IV' => 0, '0' => 0],
            'M' => ['I' => 0, 'II' => 0, 'III' => 0, 'IV' => 0, '0' => 0],
        ];
        $gpaCounters = [
            'F' => array_fill_keys($activeGrades, 0),
            'M' => array_fill_keys($activeGrades, 0),
        ];

        // Subject statistics
        $subjectStats = [];
        foreach ($subjects as $sub) {
            $subjectStats[$sub->id] = [
                'id'          => $sub->id,
                'name'        => $sub->subject_name,
                'grades'      => [
                    'F' => array_fill_keys($activeGrades, 0),
                    'M' => array_fill_keys($activeGrades, 0),
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

                    $gInfo = $this->getGradeInfo($scoreVal, $isALevel);
                    if ($gInfo['P'] !== null) {
                        if ($isALevel) {
                            $subName = strtolower($mk->subject?->subject_name ?? '');
                            $isSubsidiary = str_contains($subName, 'general studies') || str_contains($subName, 'basic applied');
                            if (!$isSubsidiary) {
                                $pointsArray[] = $gInfo['P'];
                            }
                        } else {
                            $pointsArray[] = $gInfo['P'];
                        }
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
                'combination'  => $stud->effective_combination,
                'sex'          => $dbSex,
                'scores'       => $scores,
                'points_array' => $pointsArray,
                'total'        => $totalScore,
                'count'        => $count,
                'average'      => $count > 0 ? round($totalScore / $count, 2) : 0,
                'points'       => '-',
                'division'     => '-',
                'parent_phone' => $stud->effective_parent_phone,
            ];
        }

        $examMonthName = $latestExamDate ? date('F Y', strtotime($latestExamDate)) : 'NOT CONDUCTED YET';

        // Division & GPA calculations
        foreach ($studentsData as $id => $data) {
            $s = $data['sex'];
            if ($data['count'] > 0) {
                $avg = $data['average'];
                $avgGrade = $this->getGradeInfo($avg, $isALevel)['G'];
                if (isset($gpaCounters[$s][$avgGrade])) {
                    $gpaCounters[$s][$avgGrade]++;
                }

                // Best subjects for NECTA Points (Best 3 for A-Level, Best 7 for O-Level)
                $pts = $data['points_array'];
                sort($pts);
                $sliceCount = $isALevel ? 3 : 7;
                $bestPts = array_slice($pts, 0, $sliceCount);
                $totalPoints = array_sum($bestPts);
                $studentsData[$id]['points'] = $totalPoints;

                $div = $this->calculateDivision($totalPoints, $isALevel);
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

        // Order strictly by registration number ascending (e.g. S0762/0001 to S0762/0197)
        uasort($studentsData, function ($a, $b) {
            $regA = $a['reg_number'] ?? '';
            $regB = $b['reg_number'] ?? '';
            return strnatcasecmp($regA, $regB) ?: ($a['id'] <=> $b['id']);
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

            foreach ($activeGrades as $g) {
                $st['grades']['T'][$g] = ($st['grades']['F'][$g] ?? 0) + ($st['grades']['M'][$g] ?? 0);
            }

            $passGrades = $isALevel ? ['A', 'B', 'C', 'D', 'E', 'S'] : ['A', 'B', 'C', 'D'];
            $st['ad_pass']['F'] = 0;
            $st['ad_pass']['M'] = 0;
            foreach ($passGrades as $pg) {
                $st['ad_pass']['F'] += $st['grades']['F'][$pg] ?? 0;
                $st['ad_pass']['M'] += $st['grades']['M'][$pg] ?? 0;
            }
            $st['ad_pass']['T'] = $st['ad_pass']['F'] + $st['ad_pass']['M'];

            $st['ad_pct']['F'] = $st['sat']['F'] > 0 ? round(($st['ad_pass']['F'] / $st['sat']['F']) * 100, 1) : 0;
            $st['ad_pct']['M'] = $st['sat']['M'] > 0 ? round(($st['ad_pass']['M'] / $st['sat']['M']) * 100, 1) : 0;
            $st['ad_pct']['T'] = $st['sat']['T'] > 0 ? round(($st['ad_pass']['T'] / $st['sat']['T']) * 100, 1) : 0;

            $totalScoreAll = $st['total_score']['F'] + $st['total_score']['M'];
            $st['avg'] = $st['sat']['T'] > 0 ? round($totalScoreAll / $st['sat']['T']) : 0;

            if ($st['sat']['T'] > 0) {
                if ($isALevel) {
                    $gpaPoints = (($st['grades']['T']['A'] ?? 0) * 1) +
                                 (($st['grades']['T']['B'] ?? 0) * 2) +
                                 (($st['grades']['T']['C'] ?? 0) * 3) +
                                 (($st['grades']['T']['D'] ?? 0) * 4) +
                                 (($st['grades']['T']['E'] ?? 0) * 5) +
                                 (($st['grades']['T']['S'] ?? 0) * 6) +
                                 (($st['grades']['T']['F'] ?? 0) * 7);
                } else {
                    $gpaPoints = (($st['grades']['T']['A'] ?? 0) * 1) +
                                 (($st['grades']['T']['B'] ?? 0) * 2) +
                                 (($st['grades']['T']['C'] ?? 0) * 3) +
                                 (($st['grades']['T']['D'] ?? 0) * 4) +
                                 (($st['grades']['T']['F'] ?? 0) * 5);
                }
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

        $reportHeaderClass = $selectedClass . ($isALevel && !empty($selectedCombination) ? " ({$selectedCombination})" : "");

        return view('leader.dashboard', compact(
            'schoolName',
            'availableClasses',
            'availableExams',
            'selectedClass',
            'selectedCombination',
            'allCombinations',
            'reportHeaderClass',
            'isALevel',
            'activeGrades',
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

    public function getGradeInfo($score, $isALevel = false)
    {
        if ($score === null || $score === '') return ['G' => '-', 'C' => '#000', 'P' => null];
        
        if ($isALevel) {
            // A-Level: 80-100=A, 70-79=B, 60-69=C, 50-59=D, 40-49=E, 36-40=S, 0-35=F
            if ($score >= 80) return ['G' => 'A', 'C' => '#16a34a', 'P' => 1];
            if ($score >= 70) return ['G' => 'B', 'C' => '#2563eb', 'P' => 2];
            if ($score >= 60) return ['G' => 'C', 'C' => '#ca8a04', 'P' => 3];
            if ($score >= 50) return ['G' => 'D', 'C' => '#ea580c', 'P' => 4];
            if ($score >= 40) return ['G' => 'E', 'C' => '#f97316', 'P' => 5];
            if ($score >= 36) return ['G' => 'S', 'C' => '#7c3aed', 'P' => 6];
            return ['G' => 'F', 'C' => '#dc2626', 'P' => 7];
        }

        // O-Level: 75-100=A, 60-74=B, 45-59=C, 30-44=D, 0-29=F
        if ($score >= 75) return ['G' => 'A', 'C' => '#16a34a', 'P' => 1];
        if ($score >= 60) return ['G' => 'B', 'C' => '#2563eb', 'P' => 2];
        if ($score >= 45) return ['G' => 'C', 'C' => '#ca8a04', 'P' => 3];
        if ($score >= 30) return ['G' => 'D', 'C' => '#ea580c', 'P' => 4];
        return ['G' => 'F', 'C' => '#dc2626', 'P' => 5];
    }

    public function calculateDivision($points, $isALevel = false)
    {
        if ($points === null || $points === '' || $points === '-') return '-';

        if ($isALevel) {
            // NECTA ACSEE 3-subject combination division
            if ($points >= 20) return '0';
            if ($points >= 18) return 'IV';
            if ($points >= 13) return 'III';
            if ($points >= 10) return 'II';
            if ($points >= 3)  return 'I';
            return '-';
        }

        // NECTA CSEE 7-subject division
        if ($points >= 34) return '0';
        if ($points >= 26) return 'IV';
        if ($points >= 22) return 'III';
        if ($points >= 18) return 'II';
        if ($points >= 7)  return 'I';
        return '-';
    }
}
