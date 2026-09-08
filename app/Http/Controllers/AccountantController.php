<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\FeeStructure;
use App\Models\StudentPayment;
use App\Models\Student;

class AccountantController extends Controller
{
    public function fees(Request $request)
    {
        $user = Auth::user();
        $schoolName = $user->school_name ?: 'Kome Secondary School';

        $classes = [
            'Form 1', 'Form 2', 'Form 3', 'Form 4', 'Form 5', 'Form 6',
            'Standard 1', 'Standard 2', 'Standard 3', 'Standard 4', 'Standard 5', 'Standard 6', 'Standard 7'
        ];

        $filterClass = $request->input('filter_class');
        $filterYear  = $request->input('filter_year', date('Y'));

        $ledger = [];
        $targetFeeRequired = 0;

        if (!empty($filterClass)) {
            // Find mandatory fee structure
            $feeStructure = FeeStructure::where('school_name', $schoolName)
                ->where('class_name', $filterClass)
                ->where('academic_year', $filterYear)
                ->first();

            $targetFeeRequired = $feeStructure ? $feeStructure->total_amount : 0;

            // Fetch students in this class
            $students = Student::where('school_name', $schoolName)
                ->where('class_name', $filterClass)
                ->orderBy('student_name')
                ->get();

            $studentIds = $students->pluck('id');
            $payments = StudentPayment::whereIn('student_id', $studentIds)
                ->where('academic_year', $filterYear)
                ->get()
                ->groupBy('student_id');

            foreach ($students as $stud) {
                $totalPaid = $payments->get($stud->id, collect())->sum('amount_paid');
                $balance = $targetFeeRequired - $totalPaid;
                if ($balance < 0) $balance = 0;

                $status = ($balance <= 0 && $targetFeeRequired > 0) ? 'Cleared' : 'Owing';

                $ledger[] = [
                    'student_id'   => $stud->id,
                    'student_name' => $stud->student_name,
                    'reg_number'   => $stud->reg_number,
                    'total_paid'   => $totalPaid,
                    'balance'      => $balance,
                    'status'       => $status,
                ];
            }
        }

        return view('accountant.fees', compact(
            'schoolName',
            'classes',
            'filterClass',
            'filterYear',
            'targetFeeRequired',
            'ledger'
        ));
    }

    public function storeFeeStructure(Request $request)
    {
        $request->validate([
            'config_class'  => 'required|string',
            'academic_year' => 'required',
            'total_amount'  => 'required|numeric|min:0',
        ]);

        $user = Auth::user();
        $schoolName = $user->school_name ?: 'Kome Secondary School';

        FeeStructure::updateOrCreate(
            [
                'school_name'   => $schoolName,
                'class_name'    => $request->config_class,
                'academic_year' => $request->academic_year,
            ],
            [
                'total_amount' => $request->total_amount,
            ]
        );

        return back()->with('success', "✔️ Fee structure for {$request->config_class} ({$request->academic_year}) updated to " . number_format($request->total_amount, 2) . " TZS");
    }

    public function showCollectPayment(Request $request)
    {
        $user = Auth::user();
        $schoolName = $user->school_name ?: 'Kome Secondary School';
        $classes = [
            'Form 1', 'Form 2', 'Form 3', 'Form 4', 'Form 5', 'Form 6',
            'Standard 1', 'Standard 2', 'Standard 3', 'Standard 4', 'Standard 5', 'Standard 6', 'Standard 7'
        ];

        $selectedClass = $request->input('class_name');
        $students = collect();

        if ($selectedClass) {
            $students = Student::where('school_name', $schoolName)
                ->where('class_name', $selectedClass)
                ->orderBy('student_name')
                ->get();
        }

        return view('accountant.collect_payment', compact('schoolName', 'classes', 'selectedClass', 'students'));
    }

    public function recordPayment(Request $request)
    {
        $request->validate([
            'student_id'   => 'required|exists:students,id',
            'amount_paid'  => 'required|numeric|min:1',
            'receipt_no'   => 'required|string',
            'payment_date' => 'required|date',
        ]);

        $receiptNo = trim($request->receipt_no);
        $academicYear = date('Y', strtotime($request->payment_date));

        // Check duplicate receipt
        $existing = StudentPayment::where('receipt_number', $receiptNo)->first();
        if ($existing) {
            return back()->withInput()->with('error', "❌ Receipt Number '$receiptNo' has already been registered!");
        }

        StudentPayment::create([
            'student_id'     => $request->student_id,
            'amount_paid'    => $request->amount_paid,
            'payment_date'   => $request->payment_date,
            'receipt_number' => $receiptNo,
            'academic_year'  => $academicYear,
            'recorded_by'    => Auth::id(),
        ]);

        return redirect()->route('accountant.fees', [
            'filter_class' => Student::find($request->student_id)?->class_name,
            'filter_year'  => $academicYear,
        ])->with('success', "✔️ Payment of " . number_format($request->amount_paid, 2) . " TZS recorded successfully for receipt #$receiptNo!");
    }
}
