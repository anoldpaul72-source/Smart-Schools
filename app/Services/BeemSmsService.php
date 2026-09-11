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
     * Build the text message for a student's report.
     */
    public function buildStudentReportText(Student $student, string $term): string
    {
        $schoolName = $student->school_name ?: 'SMART SCHOOL';

        // 1. Fetch marks for this student and term
        $marks = Mark::where('student_id', $student->id)
            ->where(function ($q) use ($term) {
                $q->where('term', $term)
                  ->orWhere('term', str_replace([' Examination', ' Test'], '', $term))
                  ->orWhere('term', 'like', '%' . trim(explode(' ', $term)[0]) . '%');
            })
            ->with('subject')
            ->get();

        $subjectLines = [];
        foreach ($marks as $m) {
            $subName = $m->subject ? $m->subject->subject_name : 'Somo';
            // Shorten common subject names if needed
            $shortSub = str_ireplace(
                ['Basic Mathematics', 'Information & Computer Studies', 'Civics & Moral', 'English Language', 'Kiswahili Language'],
                ['Maths', 'ICS', 'Civics', 'English', 'Kiswahili'],
                $subName
            );
            $subjectLines[] = "{$shortSub}: " . round($m->marks) . "({$m->grade})";
        }

        $avg = $marks->avg('marks');
        $overallGrade = $avg ? Mark::calculateGrade($avg)[0] : 'N/A';
        $avgFormatted = $avg ? number_format($avg, 1) . '%' : 'N/A';

        // 2. Attendance rate
        $studentAttendances = Attendance::where('student_id', $student->id)->get();
        $totalAtt = $studentAttendances->count();
        $presentAtt = $studentAttendances->where('status', 'Present')->count();
        $attRate = $totalAtt > 0 ? round(($presentAtt / $totalAtt) * 100) . '%' : '100%';

        // 3. Fee status
        $academicYear = date('Y');
        $feeStructure = FeeStructure::where('class_name', $student->class_name)
            ->where('academic_year', $academicYear)
            ->first();
        $totalFees = $feeStructure ? (float)$feeStructure->total_amount : 0.00;
        $paidAmount = (float)StudentPayment::where('student_id', $student->id)
            ->where('academic_year', $academicYear)
            ->sum('amount_paid');
        $remainingBalance = max(0, $totalFees - $paidAmount);
        $feeText = $remainingBalance > 0 ? number_format($remainingBalance) . " TZS" : "Imekamilika";

        $subjectsString = !empty($subjectLines)
            ? implode(", ", $subjectLines)
            : "Bado hayajaingizwa";

        // Construct clean, compact SMS
        $text = "MZAZI WA " . strtoupper($student->student_name) . " ({$student->class_name})\n";
        $text .= "Ripoti: {$term} - {$schoolName}\n";
        $text .= "Matokeo: {$subjectsString}\n";
        $text .= "Wastani: {$avgFormatted} (Daraja: {$overallGrade})\n";
        $text .= "Mahudhurio: {$attRate} | Ada Inayodaiwa: {$feeText}\n";
        $text .= "Kazi nzuri na hongera.";

        return $text;
    }
}
