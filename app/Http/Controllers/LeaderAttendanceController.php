<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Attendance;
use App\Models\Timetable;

class LeaderAttendanceController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $schoolName = $user->school_name ?: 'KOME SECONDARY SCHOOL';

        // Available classes
        $defaultClasses = [
            'Form 1', 'Form 2', 'Form 3', 'Form 4', 'Form 5', 'Form 6',
            'Standard 1', 'Standard 2', 'Standard 3', 'Standard 4', 'Standard 5', 'Standard 6', 'Standard 7'
        ];
        $dbClasses = Student::distinct()->whereNotNull('class_name')->pluck('class_name')->toArray();
        $availableClasses = array_values(array_unique(array_filter(array_merge($defaultClasses, $dbClasses))));

        // Available subjects
        $subjects = Subject::orderBy('subject_name')->get();

        // Report type: daily, weekly, monthly, annual
        $reportType = $request->input('report_type', 'daily');
        if (!in_array($reportType, ['daily', 'weekly', 'monthly', 'annual'])) {
            $reportType = 'daily';
        }

        // Active filters
        $selectedClass   = $request->input('class_name', $availableClasses[0] ?? 'Form 1');
        $selectedSubject = $request->input('subject_id', 'all'); // 'all' or numeric ID
        $selectedDate    = $request->input('date', date('Y-m-d'));
        $selectedWeek    = $request->input('week_date', date('Y-m-d'));
        $selectedMonth   = (int)$request->input('month', date('n'));
        $selectedYear    = (int)$request->input('year', date('Y'));

        // Query students in this class
        $studentsQuery = Student::where('class_name', $selectedClass);
        if ($user->school_name) {
            $studentsQuery->where('school_name', $user->school_name);
        }
        $students = $studentsQuery->orderBy('student_name', 'asc')->get();
        $studentIds = $students->pluck('id');
        $totalStudents = $students->count();

        // Selected subject model if specific
        $currentSubject = null;
        if (!empty($selectedSubject) && $selectedSubject !== 'all') {
            $currentSubject = Subject::find($selectedSubject);
        }

        // Data containers depending on report type
        $dailyData = [];
        $weeklyData = [];
        $monthlyData = [];
        $annualData = [];
        $summaryStats = [
            'total_students' => $totalStudents,
            'present_count'  => 0,
            'absent_count'   => 0,
            'late_count'     => 0,
            'rate_percent'   => 0,
            'total_sessions' => 0,
        ];

        // 1. DAILY REPORT
        if ($reportType === 'daily') {
            $attQuery = Attendance::with(['subject', 'recorder'])
                ->whereIn('student_id', $studentIds)
                ->where('date', $selectedDate);

            if (!empty($selectedSubject) && $selectedSubject !== 'all') {
                $attQuery->where('subject_id', $selectedSubject);
            }

            $attendances = $attQuery->get();

            // Group by student_id
            $attByStudent = $attendances->groupBy('student_id');

            $presentCount = 0;
            $absentCount = 0;
            $lateCount = 0;

            foreach ($students as $student) {
                $stRecords = $attByStudent->get($student->id, collect());
                
                // If specific subject selected, get that record; else general or latest
                $status = 'Unrecorded';
                $recordedTime = null;
                $recordedBy = null;
                $periodNum = null;
                $subName = $currentSubject ? $currentSubject->subject_name : null;

                if ($stRecords->isNotEmpty()) {
                    // Pick the record
                    $rec = $stRecords->first();
                    $status = $rec->status;
                    $recordedBy = $rec->recorder ? ($rec->recorder->name ?: $rec->recorder->username) : null;
                    $periodNum = $rec->period_number;
                    if (!$subName && $rec->subject) {
                        $subName = $rec->subject->subject_name;
                    }
                }

                if ($status === 'Present') $presentCount++;
                elseif ($status === 'Absent') $absentCount++;
                elseif ($status === 'Late' || $status === 'Permission') $lateCount++;

                $dailyData[] = [
                    'student'       => $student,
                    'status'        => $status,
                    'subject_name'  => $subName ?: __('General Attendance'),
                    'period_number' => $periodNum,
                    'recorded_by'   => $recordedBy,
                ];
            }

            $recordedTotal = $presentCount + $absentCount + $lateCount;
            $rate = $recordedTotal > 0 ? round(($presentCount / $recordedTotal) * 100, 1) : 0;

            $summaryStats = [
                'total_students' => $totalStudents,
                'present_count'  => $presentCount,
                'absent_count'   => $absentCount,
                'late_count'     => $lateCount,
                'unrecorded'     => max(0, $totalStudents - $recordedTotal),
                'rate_percent'   => $rate,
            ];
        }

        // 2. WEEKLY REPORT
        elseif ($reportType === 'weekly') {
            $weekCarbon = Carbon::parse($selectedWeek);
            $startOfWeek = $weekCarbon->copy()->startOfWeek(Carbon::MONDAY);
            $endOfWeek   = $weekCarbon->copy()->endOfWeek(Carbon::FRIDAY); // Mon to Fri school days

            $weekDays = [];
            $currentDay = $startOfWeek->copy();
            while ($currentDay <= $endOfWeek) {
                $weekDays[] = [
                    'date'        => $currentDay->format('Y-m-d'),
                    'day_name'    => $currentDay->format('D'),
                    'day_number'  => $currentDay->format('d/m'),
                ];
                $currentDay->addDay();
            }

            $weekDates = array_column($weekDays, 'date');

            $attQuery = Attendance::whereIn('student_id', $studentIds)
                ->whereIn('date', $weekDates);

            if (!empty($selectedSubject) && $selectedSubject !== 'all') {
                $attQuery->where('subject_id', $selectedSubject);
            }

            $attendances = $attQuery->get();

            // Structure by student and date
            $attByStudentDate = [];
            foreach ($attendances as $att) {
                $attByStudentDate[$att->student_id][$att->date] = $att->status;
            }

            $totalPossibleSessions = 0;
            $totalPresent = 0;
            $totalAbsent = 0;

            foreach ($students as $student) {
                $dayStatuses = [];
                $stPresent = 0;
                $stAbsent = 0;
                $stTotal = 0;

                foreach ($weekDays as $dayInfo) {
                    $d = $dayInfo['date'];
                    $st = $attByStudentDate[$student->id][$d] ?? '-';
                    $dayStatuses[$d] = $st;

                    if ($st === 'Present') {
                        $stPresent++;
                        $stTotal++;
                    } elseif ($st === 'Absent') {
                        $stAbsent++;
                        $stTotal++;
                    } elseif ($st === 'Late' || $st === 'Permission') {
                        $stTotal++;
                    }
                }

                $stRate = $stTotal > 0 ? round(($stPresent / $stTotal) * 100, 1) : null;

                $totalPossibleSessions += $stTotal;
                $totalPresent += $stPresent;
                $totalAbsent += $stAbsent;

                $weeklyData[] = [
                    'student'       => $student,
                    'days'          => $dayStatuses,
                    'present_count' => $stPresent,
                    'absent_count'  => $stAbsent,
                    'rate_percent'  => $stRate,
                ];
            }

            $weekRate = $totalPossibleSessions > 0 ? round(($totalPresent / $totalPossibleSessions) * 100, 1) : 0;
            $summaryStats = [
                'total_students' => $totalStudents,
                'total_sessions' => $totalPossibleSessions,
                'present_count'  => $totalPresent,
                'absent_count'   => $totalAbsent,
                'rate_percent'   => $weekRate,
                'week_label'     => $startOfWeek->format('d M') . ' – ' . $endOfWeek->format('d M, Y'),
            ];

            $weeklyData = [
                'days'     => $weekDays,
                'students' => $weeklyData,
            ];
        }

        // 3. MONTHLY REPORT
        elseif ($reportType === 'monthly') {
            $monthCarbon = Carbon::createFromDate($selectedYear, $selectedMonth, 1);
            $startOfMonth = $monthCarbon->copy()->startOfMonth()->format('Y-m-d');
            $endOfMonth   = $monthCarbon->copy()->endOfMonth()->format('Y-m-d');

            $attQuery = Attendance::whereIn('student_id', $studentIds)
                ->whereBetween('date', [$startOfMonth, $endOfMonth]);

            if (!empty($selectedSubject) && $selectedSubject !== 'all') {
                $attQuery->where('subject_id', $selectedSubject);
            }

            $attendances = $attQuery->get();

            // Total distinct school days in this month where attendance was recorded
            $recordedDaysCount = $attendances->pluck('date')->unique()->count();

            // Aggregate by student
            $attByStudent = $attendances->groupBy('student_id');

            $totalSessions = 0;
            $totalPresent = 0;
            $totalAbsent = 0;
            $totalLate = 0;

            foreach ($students as $student) {
                $stRecords = $attByStudent->get($student->id, collect());
                $stPresent = $stRecords->where('status', 'Present')->count();
                $stAbsent  = $stRecords->where('status', 'Absent')->count();
                $stLate    = $stRecords->whereIn('status', ['Late', 'Permission'])->count();
                $stTotal   = $stRecords->count();

                $stRate = $stTotal > 0 ? round(($stPresent / $stTotal) * 100, 1) : 0;

                $totalSessions += $stTotal;
                $totalPresent += $stPresent;
                $totalAbsent += $stAbsent;
                $totalLate += $stLate;

                $monthlyData[] = [
                    'student'       => $student,
                    'total_days'    => $stTotal,
                    'present_count' => $stPresent,
                    'absent_count'  => $stAbsent,
                    'late_count'    => $stLate,
                    'rate_percent'  => $stRate,
                ];
            }

            // Sort students by attendance rate descending
            usort($monthlyData, function ($a, $b) {
                return $b['rate_percent'] <=> $a['rate_percent'];
            });

            $overallRate = $totalSessions > 0 ? round(($totalPresent / $totalSessions) * 100, 1) : 0;
            $summaryStats = [
                'total_students' => $totalStudents,
                'recorded_days'  => $recordedDaysCount,
                'total_sessions' => $totalSessions,
                'present_count'  => $totalPresent,
                'absent_count'   => $totalAbsent,
                'late_count'     => $totalLate,
                'rate_percent'   => $overallRate,
                'month_name'     => $monthCarbon->format('F Y'),
            ];
        }

        // 4. ANNUAL REPORT
        elseif ($reportType === 'annual') {
            $startOfYear = "{$selectedYear}-01-01";
            $endOfYear   = "{$selectedYear}-12-31";

            $attQuery = Attendance::whereIn('student_id', $studentIds)
                ->whereBetween('date', [$startOfYear, $endOfYear]);

            if (!empty($selectedSubject) && $selectedSubject !== 'all') {
                $attQuery->where('subject_id', $selectedSubject);
            }

            $attendances = $attQuery->get();

            // Group by month
            $months = [
                1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr',
                5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Aug',
                9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dec'
            ];

            // Student month breakdown
            $studentYearMap = [];
            foreach ($attendances as $att) {
                $m = (int)date('n', strtotime($att->date));
                if (!isset($studentYearMap[$att->student_id][$m])) {
                    $studentYearMap[$att->student_id][$m] = ['present' => 0, 'total' => 0];
                }
                $studentYearMap[$att->student_id][$m]['total']++;
                if ($att->status === 'Present') {
                    $studentYearMap[$att->student_id][$m]['present']++;
                }
            }

            $grandTotalSessions = 0;
            $grandTotalPresent = 0;

            foreach ($students as $student) {
                $monthRates = [];
                $stYearPresent = 0;
                $stYearTotal = 0;

                foreach ($months as $mNum => $mLabel) {
                    $mStats = $studentYearMap[$student->id][$mNum] ?? ['present' => 0, 'total' => 0];
                    $mRate = $mStats['total'] > 0 ? round(($mStats['present'] / $mStats['total']) * 100, 1) : null;
                    $monthRates[$mNum] = [
                        'rate'    => $mRate,
                        'present' => $mStats['present'],
                        'total'   => $mStats['total'],
                    ];
                    $stYearPresent += $mStats['present'];
                    $stYearTotal   += $mStats['total'];
                }

                $stAnnualRate = $stYearTotal > 0 ? round(($stYearPresent / $stYearTotal) * 100, 1) : 0;
                $grandTotalSessions += $stYearTotal;
                $grandTotalPresent  += $stYearPresent;

                $annualData[] = [
                    'student'       => $student,
                    'months'        => $monthRates,
                    'year_present'  => $stYearPresent,
                    'year_total'    => $stYearTotal,
                    'annual_rate'   => $stAnnualRate,
                ];
            }

            // Sort by annual rate descending
            usort($annualData, function ($a, $b) {
                return $b['annual_rate'] <=> $a['annual_rate'];
            });

            $yearRate = $grandTotalSessions > 0 ? round(($grandTotalPresent / $grandTotalSessions) * 100, 1) : 0;
            $summaryStats = [
                'total_students' => $totalStudents,
                'total_sessions' => $grandTotalSessions,
                'present_count'  => $grandTotalPresent,
                'absent_count'   => max(0, $grandTotalSessions - $grandTotalPresent),
                'rate_percent'   => $yearRate,
                'year'           => $selectedYear,
            ];

            $annualData = [
                'months'   => $months,
                'students' => $annualData,
            ];
        }

        return view('leader.attendance', compact(
            'schoolName',
            'availableClasses',
            'subjects',
            'reportType',
            'selectedClass',
            'selectedSubject',
            'currentSubject',
            'selectedDate',
            'selectedWeek',
            'selectedMonth',
            'selectedYear',
            'summaryStats',
            'dailyData',
            'weeklyData',
            'monthlyData',
            'annualData'
        ));
    }
}
