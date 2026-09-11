<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\FeeStructure;
use App\Models\StudentPayment;
use App\Models\Student;
use App\Models\SchoolIncome;

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

        $activeTab   = $request->input('tab', 'fees');
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
                ->orderBy('reg_number', 'asc')
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

        // --- School Projects & Non-Fee Revenues ---
        $projectYear     = $request->input('project_year', date('Y'));
        $projectCategory = $request->input('project_category');

        $projectQuery = SchoolIncome::where('school_name', $schoolName)
            ->where('academic_year', $projectYear);

        if (!empty($projectCategory)) {
            $projectQuery->where('category', $projectCategory);
        }

        $projectIncomes = $projectQuery->orderBy('payment_date', 'desc')->latest()->paginate(20, ['*'], 'projects_page');

        // Summary Aggregates for the active project year
        $totalProjectRevenue = (float) SchoolIncome::where('school_name', $schoolName)
            ->where('academic_year', $projectYear)
            ->sum('amount');

        $farmRevenue = (float) SchoolIncome::where('school_name', $schoolName)
            ->where('academic_year', $projectYear)
            ->where('category', 'Mauzo ya Mazao')
            ->sum('amount');

        $vendorRevenue = (float) SchoolIncome::where('school_name', $schoolName)
            ->where('academic_year', $projectYear)
            ->where('category', 'Ushuru wa Mama Ntilie')
            ->sum('amount');

        $frameRevenue = (float) SchoolIncome::where('school_name', $schoolName)
            ->where('academic_year', $projectYear)
            ->where('category', 'Kodi za Fremu')
            ->sum('amount');

        $otherRevenue = (float) SchoolIncome::where('school_name', $schoolName)
            ->where('academic_year', $projectYear)
            ->whereNotIn('category', ['Mauzo ya Mazao', 'Ushuru wa Mama Ntilie', 'Kodi za Fremu'])
            ->sum('amount');

        $categories = SchoolIncome::CATEGORIES;

        return view('accountant.fees', compact(
            'schoolName',
            'classes',
            'activeTab',
            'filterClass',
            'filterYear',
            'targetFeeRequired',
            'ledger',
            'projectYear',
            'projectCategory',
            'projectIncomes',
            'totalProjectRevenue',
            'farmRevenue',
            'vendorRevenue',
            'frameRevenue',
            'otherRevenue',
            'categories'
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
                ->orderBy('reg_number', 'asc')
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
            'tab'          => 'fees',
            'filter_class' => Student::find($request->student_id)?->class_name,
            'filter_year'  => $academicYear,
        ])->with('success', "✔️ Payment of " . number_format($request->amount_paid, 2) . " TZS recorded successfully for receipt #$receiptNo!");
    }

    public function storeProjectIncome(Request $request)
    {
        $request->validate([
            'category'       => 'required|string',
            'source_title'   => 'required|string|max:255',
            'amount'         => 'required|numeric|min:1',
            'payment_date'   => 'required|date',
            'payer_name'     => 'nullable|string|max:255',
            'receipt_number' => 'nullable|string|max:100',
            'payment_method' => 'required|string',
            'academic_year'  => 'nullable|string',
            'notes'          => 'nullable|string',
        ]);

        $user = Auth::user();
        $schoolName = $user->school_name ?: 'Kome Secondary School';
        $year = $request->academic_year ?: date('Y', strtotime($request->payment_date));

        SchoolIncome::create([
            'school_name'    => $schoolName,
            'category'       => $request->category,
            'source_title'   => trim($request->source_title),
            'payer_name'     => $request->payer_name ? trim($request->payer_name) : null,
            'amount'         => $request->amount,
            'payment_date'   => $request->payment_date,
            'receipt_number' => $request->receipt_number ? trim($request->receipt_number) : null,
            'payment_method' => $request->payment_method,
            'academic_year'  => $year,
            'notes'          => $request->notes ? trim($request->notes) : null,
            'recorded_by'    => Auth::id(),
        ]);

        return redirect()->route('accountant.fees', [
            'tab'          => 'projects',
            'project_year' => $year
        ])->with('success', "✔️ Mapato ya mradi '" . trim($request->source_title) . "' (" . number_format($request->amount, 2) . " TZS) yamerekodiwa kikamilifu!");
    }

    public function deleteProjectIncome($id)
    {
        $income = SchoolIncome::findOrFail($id);
        $title = $income->source_title;
        $amount = $income->amount;
        $year = $income->academic_year;
        $income->delete();

        return redirect()->route('accountant.fees', [
            'tab'          => 'projects',
            'project_year' => $year
        ])->with('success', "✔️ Rekodi ya mapato '$title' (" . number_format($amount, 2) . " TZS) imefutwa kikamilifu.");
    }

    public function printProjectRevenueReport(Request $request)
    {
        $user = Auth::user();
        $schoolName = $user->school_name ?: 'Kome Secondary School';
        $projectYear = $request->input('year', date('Y'));
        $category = $request->input('category');

        $query = SchoolIncome::where('school_name', $schoolName)
            ->where('academic_year', $projectYear);

        if (!empty($category)) {
            $query->where('category', $category);
        }

        $incomes = $query->orderBy('payment_date', 'asc')->get();
        $totalAmount = $incomes->sum('amount');

        return view('accountant.project_revenue_print', compact(
            'schoolName',
            'projectYear',
            'category',
            'incomes',
            'totalAmount'
        ));
    }
}
