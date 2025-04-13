<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\FeeCategory;
use App\Models\FeeType;
use App\Models\FeeAllocation;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Payment;
use App\Models\Student;
use App\Models\AcademicYear;
use App\Models\Program;

class FinanceController extends Controller
{
    /**
     * Display the finance dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function dashboard()
    {
        // Get basic stats for the dashboard
        $totalStudents = Student::count();
        $totalInvoices = Invoice::count();
        $totalRevenue = Payment::sum('amount');
        $pendingPayments = Invoice::where('status', 'pending')->sum('total_amount');
        
        // Get recent payments
        $recentPayments = Payment::with('student', 'invoice')
                                ->orderBy('created_at', 'desc')
                                ->take(10)
                                ->get();
        
        // Get payment statistics by month for the current year
        $paymentsByMonth = Payment::selectRaw('MONTH(created_at) as month, SUM(amount) as total')
                                ->whereYear('created_at', date('Y'))
                                ->groupBy('month')
                                ->orderBy('month')
                                ->get()
                                ->pluck('total', 'month')
                                ->toArray();
        
        return view('finance.dashboard', compact(
            'totalStudents',
            'totalInvoices',
            'totalRevenue',
            'pendingPayments',
            'recentPayments',
            'paymentsByMonth'
        ));
    }

    /**
     * Display a report of finance statistics.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function report(Request $request)
    {
        $academicYears = AcademicYear::orderBy('year', 'desc')->get();
        $programs = Program::orderBy('name')->get();
        
        $selectedYear = $request->input('academic_year_id', AcademicYear::where('is_current', true)->first()?->id);
        $selectedProgram = $request->input('program_id');
        $reportType = $request->input('report_type', 'summary');
        
        // Generate report data based on filters
        $query = Payment::query();
        
        if ($selectedYear) {
            $query->whereHas('invoice', function($q) use ($selectedYear) {
                $q->where('academic_year_id', $selectedYear);
            });
        }
        
        if ($selectedProgram) {
            $query->whereHas('student', function($q) use ($selectedProgram) {
                $q->where('program_id', $selectedProgram);
            });
        }
        
        $totalRevenue = $query->sum('amount');
        
        // Different report types
        if ($reportType === 'summary') {
            $reportData = $this->generateSummaryReport($selectedYear, $selectedProgram);
        } elseif ($reportType === 'student') {
            $reportData = $this->generateStudentReport($selectedYear, $selectedProgram);
        } elseif ($reportType === 'fee_type') {
            $reportData = $this->generateFeeTypeReport($selectedYear, $selectedProgram);
        } else {
            $reportData = [];
        }
        
        return view('finance.report', compact(
            'academicYears',
            'programs',
            'selectedYear',
            'selectedProgram',
            'reportType',
            'totalRevenue',
            'reportData'
        ));
    }
    
    /**
     * Generate a summary report.
     *
     * @param  int|null  $academicYearId
     * @param  int|null  $programId
     * @return array
     */
    private function generateSummaryReport($academicYearId, $programId)
    {
        // Summary report implementation
        $totalInvoiced = Invoice::query();
        $totalPaid = Payment::query();
        
        if ($academicYearId) {
            $totalInvoiced->where('academic_year_id', $academicYearId);
            $totalPaid->whereHas('invoice', function($q) use ($academicYearId) {
                $q->where('academic_year_id', $academicYearId);
            });
        }
        
        if ($programId) {
            $totalInvoiced->whereHas('student', function($q) use ($programId) {
                $q->where('program_id', $programId);
            });
            $totalPaid->whereHas('student', function($q) use ($programId) {
                $q->where('program_id', $programId);
            });
        }
        
        return [
            'invoiced' => $totalInvoiced->sum('total_amount'),
            'paid' => $totalPaid->sum('amount'),
            'pending' => $totalInvoiced->where('status', 'pending')->sum('total_amount'),
            'overdue' => $totalInvoiced->where('status', 'overdue')->sum('total_amount'),
        ];
    }
    
    /**
     * Generate a student-based report.
     *
     * @param  int|null  $academicYearId
     * @param  int|null  $programId
     * @return array
     */
    private function generateStudentReport($academicYearId, $programId)
    {
        $query = Student::query();
        
        if ($programId) {
            $query->where('program_id', $programId);
        }
        
        $students = $query->with(['invoices' => function($q) use ($academicYearId) {
            if ($academicYearId) {
                $q->where('academic_year_id', $academicYearId);
            }
        }, 'invoices.payments'])
        ->get()
        ->map(function($student) {
            $invoiced = $student->invoices->sum('total_amount');
            $paid = $student->invoices->flatMap->payments->sum('amount');
            
            return [
                'id' => $student->id,
                'name' => $student->name,
                'student_id' => $student->student_id,
                'invoiced' => $invoiced,
                'paid' => $paid,
                'balance' => $invoiced - $paid,
            ];
        });
        
        return $students;
    }
    
    /**
     * Generate a fee type-based report.
     *
     * @param  int|null  $academicYearId
     * @param  int|null  $programId
     * @return array
     */
    private function generateFeeTypeReport($academicYearId, $programId)
    {
        $query = FeeType::with(['invoiceItems' => function($q) use ($academicYearId, $programId) {
            $q->whereHas('invoice', function($iq) use ($academicYearId, $programId) {
                if ($academicYearId) {
                    $iq->where('academic_year_id', $academicYearId);
                }
                
                if ($programId) {
                    $iq->whereHas('student', function($sq) use ($programId) {
                        $sq->where('program_id', $programId);
                    });
                }
            });
        }]);
        
        $feeTypes = $query->get()->map(function($feeType) {
            $totalAmount = $feeType->invoiceItems->sum('amount');
            $count = $feeType->invoiceItems->count();
            
            return [
                'id' => $feeType->id,
                'name' => $feeType->name,
                'count' => $count,
                'total' => $totalAmount,
            ];
        });
        
        return $feeTypes;
    }
} 