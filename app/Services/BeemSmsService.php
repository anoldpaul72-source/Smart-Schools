<?php

namespace App\Services;

use App\Models\Student;
use App\Models\Mark;
use App\Models\SmsLog;
use App\Models\Attendance;
use App\Models\FeeStructure;
use App\Models\StudentPayment;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BeemSmsService
{
    protected ?string $apiKey;
    protected ?string $secretKey;
    protected string $senderName;
    protected bool $simulate;

    public function __construct()
    {
        $this->apiKey     = config('services.beem.api_key') ?: env('BEEM_API_KEY');
        $this->secretKey  = config('services.beem.secret_key') ?: env('BEEM_SECRET_KEY');
        $this->senderName = config('services.beem.sender_name') ?: env('BEEM_SENDER_NAME', 'SMARTSCHOOL');
        $this->simulate   = (bool)(config('services.beem.simulate') ?: env('BEEM_SIMULATE', false));
    }

    /**
     * Format any phone number into standard international format for Beem Africa (e.g. 255712345678).
     */
    public function formatPhoneNumber(?string $phone): ?string
    {
        if (!$phone) {
            return null;
        }

        // Remove non-numeric characters except leading plus
        $cleaned = preg_replace('/[^0-9]/', '', $phone);

        if (str_starts_with($cleaned, '0') && strlen($cleaned) === 10) {
            return '255' . substr($cleaned, 1);
        }

        if (str_starts_with($cleaned, '255') && strlen($cleaned) === 12) {
            return $cleaned;
        }

        if (str_starts_with($cleaned, '7') && strlen($cleaned) === 9) {
            return '255' . $cleaned;
        }

        if (str_starts_with($cleaned, '6') && strlen($cleaned) === 9) {
            return '255' . $cleaned;
        }

        return $cleaned;
    }

    /**
     * Send an SMS message using Beem Africa or local simulation mode.
     */
    public function sendSms(string $phone, string $message, ?Student $student = null, ?int $sentBy = null): array
    {
        $destAddr = $this->formatPhoneNumber($phone);

        if (!$destAddr || strlen($destAddr) < 10) {
            return [
                'success' => false,
                'message' => 'Namba ya simu si sahihi (' . $phone . ').',
                'status'  => 'failed',
            ];
        }

        // If credentials are not set or simulation is active, log as simulated
        if ($this->simulate || empty($this->apiKey) || empty($this->secretKey)) {
            $log = SmsLog::create([
                'student_id'      => $student?->id,
                'recipient_phone' => $destAddr,
                'recipient_name'  => $student?->student_name,
                'message'         => $message,
                'status'          => 'simulated',
                'response_code'   => 'SIMULATED_200',
                'error_message'   => 'Iliwekwa kwenye majaribio (Weka BEEM_API_KEY na BEEM_SECRET_KEY kwenye .env kutuma live).',
                'sent_by'         => $sentBy,
            ]);

            Log::info("Simulated SMS to {$destAddr}: {$message}");

            return [
                'success'   => true,
                'simulated' => true,
                'message'   => 'Ujumbe umehifadhiwa (Simulated mode). Weka BEEM_API_KEY kwenye .env kutuma moja kwa moja.',
                'log_id'    => $log->id,
            ];
        }

        try {
            $response = Http::withHeaders([
                'Content-Type'  => 'application/json',
                'Authorization' => 'Basic ' . base64_encode($this->apiKey . ':' . $this->secretKey),
            ])->timeout(15)->post('https://apisms.beem.africa/v1/send', [
                'source_addr'   => $this->senderName,
                'schedule_time' => '',
                'encoding'      => '0',
                'message'       => $message,
                'recipients'    => [
                    [
                        'recipient_id' => 1,
                        'dest_addr'    => $destAddr,
                    ],
                ],
            ]);

            $body = $response->json();
            $statusCode = $response->status();

            if ($response->successful() && isset($body['successful']) && $body['successful'] === true) {
                $log = SmsLog::create([
                    'student_id'      => $student?->id,
                    'recipient_phone' => $destAddr,
                    'recipient_name'  => $student?->student_name,
                    'message'         => $message,
                    'status'          => 'sent',
                    'response_code'   => (string)$statusCode,
                    'error_message'   => null,
                    'sent_by'         => $sentBy,
                ]);

                return [
                    'success'   => true,
                    'simulated' => false,
                    'message'   => 'SMS imetumwa kikamilifu kwenda ' . $destAddr,
                    'log_id'    => $log->id,
                ];
            } else {
                $errMsg = $body['message'] ?? $response->body();
                $log = SmsLog::create([
                    'student_id'      => $student?->id,
                    'recipient_phone' => $destAddr,
                    'recipient_name'  => $student?->student_name,
                    'message'         => $message,
                    'status'          => 'failed',
                    'response_code'   => (string)$statusCode,
                    'error_message'   => $errMsg,
                    'sent_by'         => $sentBy,
                ]);

                Log::error("Beem SMS failed to {$destAddr}: " . $errMsg);

                return [
                    'success' => false,
                    'message' => 'Hitilafu ya Beem API: ' . $errMsg,
                    'log_id'  => $log->id,
                ];
            }
        } catch (\Exception $e) {
            $log = SmsLog::create([
                'student_id'      => $student?->id,
                'recipient_phone' => $destAddr,
                'recipient_name'  => $student?->student_name,
                'message'         => $message,
                'status'          => 'failed',
                'response_code'   => 'EXCEPTION',
                'error_message'   => $e->getMessage(),
                'sent_by'         => $sentBy,
            ]);

            Log::error("Beem SMS Exception: " . $e->getMessage());

            return [
                'success' => false,
                'message' => 'Hitilafu ya mtandao: ' . $e->getMessage(),
                'log_id'  => $log->id,
            ];
        }
    }

    /**
    /**
     * Format standard subject name into a clean, compact abbreviation for SMS reporting.
     */
    public static function formatSubjectShortName(string $name): string
    {
        $n = strtolower(trim($name));
        if (str_contains($n, 'basic applied') || $n === 'bam') return 'BAM';
        if (str_contains($n, 'general studies') || $n === 'gs') return 'GS';
        if (str_contains($n, 'advanced math')) return 'Adv.Math';
        if (str_contains($n, 'basic math') || str_contains($n, 'mathematics') || $n === 'maths') return 'Math';
        if (str_contains($n, 'kiswahili')) return 'Kisw';
        if (str_contains($n, 'english')) return 'Eng';
        if (str_contains($n, 'physics')) return 'Phy';
        if (str_contains($n, 'chemistry')) return 'Chem';
        if (str_contains($n, 'biology')) return 'Bio';
        if (str_contains($n, 'history')) return 'Hist';
        if (str_contains($n, 'geography')) return 'Geo';
        if (str_contains($n, 'civics')) return 'Civ';
        if (str_contains($n, 'computer') || str_contains($n, 'ics') || str_contains($n, 'it')) return 'CS';
        if (str_contains($n, 'book')) return 'B.Keep';
        if (str_contains($n, 'commerce')) return 'Comm';
        if (str_contains($n, 'business')) return 'Bus';
        if (str_contains($n, 'agriculture')) return 'Agri';
        if (str_contains($n, 'economics')) return 'Econ';
        if (str_contains($n, 'account')) return 'Acct';
        if (str_contains($n, 'nutrition')) return 'Nutr';
        if (str_contains($n, 'french')) return 'Fre';
        if (str_contains($n, 'religion') || str_contains($n, 'dini')) return 'Rel';
        return substr($name, 0, 4);
    }

    /**
     * Build the text message for a student's report.
     * Supports both English ('en') and Swahili ('sw') dynamically.
     * Shows all registered curriculum subjects for the student's class.
     */
    public function buildStudentReportText(Student $student, string $term, ?string $locale = null): string
    {
        $schoolName = $student->school_name ?: 'SMART SCHOOL';
        $locale = $locale ?: app()->getLocale() ?: 'sw';
        $isEn = ($locale === 'en');

        // 1. Fetch registered academic curriculum subjects for this student's class
        $registeredSubjects = \App\Models\Subject::getRegisteredAcademicSubjectsForClass(
            $student->class_name,
            $student->effective_combination
        );

        // 2. Fetch recorded marks for this student and term (academic subjects only)
        $marks = Mark::where('student_id', $student->id)
            ->where(function ($q) use ($term) {
                $q->where('term', $term)
                  ->orWhere('term', str_replace([' Examination', ' Test'], '', $term))
                  ->orWhere('term', 'like', '%' . trim(explode(' ', $term)[0]) . '%');
            })
            ->whereHas('subject', function ($q) {
                $q->whereNotIn('subject_name', \App\Models\Subject::NON_ACADEMIC_ACTIVITIES);
            })
            ->with('subject')
            ->get();

        $marksBySubId = $marks->keyBy('subject_id');
        $isALevel = $student->isALevel();

        $subjectLines = [];

        if ($registeredSubjects->isNotEmpty()) {
            foreach ($registeredSubjects as $sub) {
                $shortSub = self::formatSubjectShortName($sub->subject_name);
                $m = $marksBySubId->get($sub->id);
                if ($m && $m->marks !== null && $m->marks !== '') {
                    $subGrade = Mark::calculateGrade((float)$m->marks, $isALevel)[0];
                    $subjectLines[] = "{$shortSub}: " . round($m->marks) . "({$subGrade})";
                } else {
                    $subjectLines[] = "{$shortSub}: -";
                }
            }
        } else {
            foreach ($marks as $m) {
                $subName = $m->subject ? $m->subject->subject_name : 'Subject';
                $shortSub = self::formatSubjectShortName($subName);
                $subGrade = Mark::calculateGrade((float)$m->marks, $isALevel)[0];
                $subjectLines[] = "{$shortSub}: " . round($m->marks) . "({$subGrade})";
            }
        }

        $avg = $marks->avg('marks');
        $overallGrade = $avg !== null ? Mark::calculateGrade((float)$avg, $isALevel)[0] : 'N/A';
        $avgFormatted = $avg !== null ? number_format($avg, 1) . '%' : 'N/A';

        // 3. Attendance rate
        $studentAttendances = Attendance::where('student_id', $student->id)->get();
        $totalAtt = $studentAttendances->count();
        $presentAtt = $studentAttendances->where('status', 'Present')->count();
        $attRate = $totalAtt > 0 ? round(($presentAtt / $totalAtt) * 100) . '%' : '100%';

        // 4. Fee status
        $academicYear = date('Y');
        $feeStructure = FeeStructure::where('class_name', $student->class_name)
            ->where('academic_year', $academicYear)
            ->first();
        $totalFees = $feeStructure ? (float)$feeStructure->total_amount : 0.00;
        $paidAmount = (float)StudentPayment::where('student_id', $student->id)
            ->where('academic_year', $academicYear)
            ->sum('amount_paid');
        $remainingBalance = max(0, $totalFees - $paidAmount);
        
        if ($isEn) {
            $feeText = $remainingBalance > 0 ? number_format($remainingBalance) . " TZS" : "Completed";
            $subjectsString = !empty($subjectLines) ? implode(", ", $subjectLines) : "No marks recorded";
            $levelStr = $isALevel ? " (A-Level)" : " (O-Level)";
            
            $text = "PARENT OF " . strtoupper($student->student_name) . " ({$student->class_name}{$levelStr})\n";
            $text .= "Report: {$term} - {$schoolName}\n";
            $text .= "Results: {$subjectsString}\n";
            $text .= "Average: {$avgFormatted} (Grade: {$overallGrade})\n";
            $text .= "Attendance: {$attRate} | Outstanding Fees: {$feeText}\n";
            $text .= "Good job and congratulations.";
        } else {
            $feeText = $remainingBalance > 0 ? number_format($remainingBalance) . " TZS" : "Imekamilika";
            $subjectsString = !empty($subjectLines) ? implode(", ", $subjectLines) : "Bado hayajaingizwa";
            $levelStr = $isALevel ? " (A-Level)" : " (O-Level)";
            
            $text = "MZAZI WA " . strtoupper($student->student_name) . " ({$student->class_name}{$levelStr})\n";
            $text .= "Ripoti: {$term} - {$schoolName}\n";
            $text .= "Matokeo: {$subjectsString}\n";
            $text .= "Wastani: {$avgFormatted} (Daraja: {$overallGrade})\n";
            $text .= "Mahudhurio: {$attRate} | Ada Inayodaiwa: {$feeText}\n";
            $text .= "Kazi nzuri na hongera.";
        }

        return $text;
    }
}
