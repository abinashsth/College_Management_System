<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\FeeType;
use App\Models\FeeAllocation;
use App\Models\Student;
use App\Models\AcademicYear;
use App\Models\Program;
use App\Models\Classes;
use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the invoices.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Invoice::with(['student', 'academicYear']);
        
        // Apply filters
        if ($request->filled('student_id')) {
            $query->where('student_id', $request->student_id);
        }
        
        if ($request->filled('academic_year_id')) {
            $query->where('academic_year_id', $request->academic_year_id);
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('due_date')) {
            $query->whereDate('due_date', $request->due_date);
        }
        
        $invoices = $query->orderBy('created_at', 'desc')->paginate(15);
        
        // Get filter options
        $students = Student::orderByName()->get();
        $academicYears = AcademicYear::orderBy('year', 'desc')->get();
        $statuses = [
            'pending' => 'Pending',
            'paid' => 'Paid',
            'partial' => 'Partially Paid',
            'overdue' => 'Overdue',
            'cancelled' => 'Cancelled'
        ];
        
        return view('finance.invoices.index', compact('invoices', 'students', 'academicYears', 'statuses'));
    }

    /**
     * Show the form for creating a new invoice.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $students = Student::orderByName()->get();
        $academicYears = AcademicYear::orderBy('year', 'desc')->get();
        $feeTypes = FeeType::where('is_active', true)->orderBy('name')->get();
        
        return view('finance.invoices.create', compact('students', 'academicYears', 'feeTypes'));
    }

    /**
     * Store a newly created invoice in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'invoice_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:invoice_date',
            'notes' => 'nullable|string',
            'fee_types' => 'required|array',
            'fee_types.*.fee_type_id' => 'required|exists:fee_types,id',
            'fee_types.*.amount' => 'required|numeric|min:0',
            'fee_types.*.description' => 'nullable|string',
        ]);
        
        try {
            DB::beginTransaction();
            
            // Calculate total amount
            $totalAmount = 0;
            foreach ($validated['fee_types'] as $feeType) {
                $totalAmount += $feeType['amount'];
            }
            
            // Create invoice
            $invoice = Invoice::create([
                'student_id' => $validated['student_id'],
                'academic_year_id' => $validated['academic_year_id'],
                'invoice_number' => $this->generateInvoiceNumber(),
                'total_amount' => $totalAmount,
                'paid_amount' => 0,
                'balance_amount' => $totalAmount,
                'status' => 'pending',
                'invoice_date' => $validated['invoice_date'],
                'due_date' => $validated['due_date'],
                'notes' => $validated['notes'],
                'created_by' => Auth::id(),
            ]);
            
            // Create invoice items
            foreach ($validated['fee_types'] as $feeTypeData) {
                $feeType = FeeType::findOrFail($feeTypeData['fee_type_id']);
                
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'fee_type_id' => $feeType->id,
                    'amount' => $feeTypeData['amount'],
                    'description' => $feeTypeData['description'] ?? $feeType->name,
                ]);
            }
            
            DB::commit();
            
            return redirect()->route('invoices.show', $invoice)
                ->with('success', 'Invoice created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to create invoice: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified invoice.
     *
     * @param  \App\Models\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function show(Invoice $invoice)
    {
        $invoice->load(['student', 'academicYear', 'invoiceItems.feeType', 'payments']);
        return view('finance.invoices.show', compact('invoice'));
    }

    /**
     * Show the form for editing the specified invoice.
     *
     * @param  \App\Models\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function edit(Invoice $invoice)
    {
        // Don't allow editing invoices that have payments
        if ($invoice->payments()->count() > 0) {
            return redirect()->route('invoices.show', $invoice)
                ->with('error', 'Cannot edit an invoice that already has payments.');
        }
        
        $invoice->load(['student', 'academicYear', 'invoiceItems.feeType']);
        
        $students = Student::orderByName()->get();
        $academicYears = AcademicYear::orderBy('year', 'desc')->get();
        $feeTypes = FeeType::where('is_active', true)->orderBy('name')->get();
        
        return view('finance.invoices.edit', compact('invoice', 'students', 'academicYears', 'feeTypes'));
    }

    /**
     * Update the specified invoice in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Invoice $invoice)
    {
        // Don't allow editing invoices that have payments
        if ($invoice->payments()->count() > 0) {
            return redirect()->route('invoices.show', $invoice)
                ->with('error', 'Cannot edit an invoice that already has payments.');
        }
        
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'invoice_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:invoice_date',
            'notes' => 'nullable|string',
            'fee_types' => 'required|array',
            'fee_types.*.fee_type_id' => 'required|exists:fee_types,id',
            'fee_types.*.amount' => 'required|numeric|min:0',
            'fee_types.*.description' => 'nullable|string',
            'fee_types.*.id' => 'nullable|exists:invoice_items,id',
        ]);
        
        try {
            DB::beginTransaction();
            
            // Calculate total amount
            $totalAmount = 0;
            foreach ($validated['fee_types'] as $feeType) {
                $totalAmount += $feeType['amount'];
            }
            
            // Update invoice
            $invoice->update([
                'student_id' => $validated['student_id'],
                'academic_year_id' => $validated['academic_year_id'],
                'total_amount' => $totalAmount,
                'balance_amount' => $totalAmount, // No payments yet
                'invoice_date' => $validated['invoice_date'],
                'due_date' => $validated['due_date'],
                'notes' => $validated['notes'],
                'updated_by' => Auth::id(),
            ]);
            
            // Delete existing invoice items
            $invoice->invoiceItems()->delete();
            
            // Create new invoice items
            foreach ($validated['fee_types'] as $feeTypeData) {
                $feeType = FeeType::findOrFail($feeTypeData['fee_type_id']);
                
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'fee_type_id' => $feeType->id,
                    'amount' => $feeTypeData['amount'],
                    'description' => $feeTypeData['description'] ?? $feeType->name,
                ]);
            }
            
            DB::commit();
            
            return redirect()->route('invoices.show', $invoice)
                ->with('success', 'Invoice updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to update invoice: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified invoice from storage.
     *
     * @param  \App\Models\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function destroy(Invoice $invoice)
    {
        // Don't allow deleting invoices that have payments
        if ($invoice->payments()->count() > 0) {
            return redirect()->route('invoices.index')
                ->with('error', 'Cannot delete an invoice that has payments.');
        }
        
        try {
            DB::beginTransaction();
            
            // Delete invoice items
            $invoice->invoiceItems()->delete();
            
            // Delete invoice
            $invoice->delete();
            
            DB::commit();
            
            return redirect()->route('invoices.index')
                ->with('success', 'Invoice deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to delete invoice: ' . $e->getMessage());
        }
    }
    
    /**
     * Generate invoice in bulk.
     *
     * @return \Illuminate\Http\Response
     */
    public function generateBulk()
    {
        $academicYears = AcademicYear::orderBy('year', 'desc')->get();
        $programs = Program::orderBy('name')->get();
        $classes = Classes::orderBy('name')->get();
        $sections = Section::orderBy('name')->get();
        $feeTypes = FeeType::where('is_active', true)->orderBy('name')->get();
        
        return view('finance.invoices.generate-bulk', compact(
            'academicYears',
            'programs',
            'classes',
            'sections',
            'feeTypes'
        ));
    }
    
    /**
     * Process bulk invoice generation.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function processBulk(Request $request)
    {
        $validated = $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'target_type' => 'required|in:program,class,section',
            'program_id' => 'required_if:target_type,program|exists:programs,id',
            'class_id' => 'required_if:target_type,class|exists:classes,id',
            'section_id' => 'required_if:target_type,section|exists:sections,id',
            'fee_type_ids' => 'required|array',
            'fee_type_ids.*' => 'exists:fee_types,id',
            'invoice_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:invoice_date',
            'description' => 'nullable|string',
        ]);
        
        try {
            DB::beginTransaction();
            
            // Get students based on target type
            $studentsQuery = Student::query();
            
            if ($validated['target_type'] === 'program') {
                $studentsQuery->where('program_id', $validated['program_id']);
            } elseif ($validated['target_type'] === 'class') {
                // Assuming students are linked to classes
                $studentsQuery->whereHas('classes', function($query) use ($validated) {
                    $query->where('classes.id', $validated['class_id']);
                });
            } elseif ($validated['target_type'] === 'section') {
                // Assuming students are linked to sections
                $studentsQuery->whereHas('section', function($query) use ($validated) {
                    $query->where('sections.id', $validated['section_id']);
                });
            }
            
            $students = $studentsQuery->get();
            
            // Get fee types
            $feeTypes = FeeType::whereIn('id', $validated['fee_type_ids'])->get();
            
            $invoicesCreated = 0;
            
            foreach ($students as $student) {
                // Calculate total amount for this student
                $totalAmount = 0;
                $invoiceItems = [];
                
                foreach ($feeTypes as $feeType) {
                    // Check if there's a specific allocation for this fee type for this student/class/program
                    $amount = $this->getFeeAmount($feeType, $student, $validated['academic_year_id']);
                    $totalAmount += $amount;
                    
                    $invoiceItems[] = [
                        'fee_type_id' => $feeType->id,
                        'amount' => $amount,
                        'description' => $feeType->name,
                    ];
                }
                
                // Create invoice
                $invoice = Invoice::create([
                    'student_id' => $student->id,
                    'academic_year_id' => $validated['academic_year_id'],
                    'invoice_number' => $this->generateInvoiceNumber(),
                    'total_amount' => $totalAmount,
                    'paid_amount' => 0,
                    'balance_amount' => $totalAmount,
                    'status' => 'pending',
                    'invoice_date' => $validated['invoice_date'],
                    'due_date' => $validated['due_date'],
                    'notes' => $validated['description'] ?? 'Bulk generated invoice',
                    'created_by' => Auth::id(),
                ]);
                
                // Create invoice items
                foreach ($invoiceItems as $item) {
                    InvoiceItem::create([
                        'invoice_id' => $invoice->id,
                        'fee_type_id' => $item['fee_type_id'],
                        'amount' => $item['amount'],
                        'description' => $item['description'],
                    ]);
                }
                
                $invoicesCreated++;
            }
            
            DB::commit();
            
            return redirect()->route('invoices.index')
                ->with('success', "Successfully generated {$invoicesCreated} invoices.");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to generate invoices: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    /**
     * Print the specified invoice.
     *
     * @param  \App\Models\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function print(Invoice $invoice)
    {
        $invoice->load(['student', 'academicYear', 'invoiceItems.feeType', 'payments']);
        return view('finance.invoices.print', compact('invoice'));
    }
    
    /**
     * Generate a new invoice number.
     *
     * @return string
     */
    private function generateInvoiceNumber()
    {
        $prefix = 'INV-' . date('Y');
        $lastInvoice = Invoice::where('invoice_number', 'like', $prefix . '%')
                            ->orderBy('id', 'desc')
                            ->first();
        
        if ($lastInvoice) {
            $lastNumber = (int) substr($lastInvoice->invoice_number, strlen($prefix) + 1);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        
        return $prefix . '-' . str_pad($newNumber, 5, '0', STR_PAD_LEFT);
    }
    
    /**
     * Get the fee amount for a specific fee type and student.
     *
     * @param  \App\Models\FeeType  $feeType
     * @param  \App\Models\Student  $student
     * @param  int  $academicYearId
     * @return float
     */
    private function getFeeAmount($feeType, $student, $academicYearId)
    {
        // First try to find student-specific allocation
        $allocation = FeeAllocation::where('fee_type_id', $feeType->id)
                                 ->where('academic_year_id', $academicYearId)
                                 ->where('applicable_to', 'student')
                                 ->where('applicable_id', $student->id)
                                 ->where('is_active', true)
                                 ->first();
        
        if ($allocation) {
            return $allocation->amount ?? $feeType->amount;
        }
        
        // Try to find section-specific allocation
        if ($student->section_id) {
            $allocation = FeeAllocation::where('fee_type_id', $feeType->id)
                                     ->where('academic_year_id', $academicYearId)
                                     ->where('applicable_to', 'section')
                                     ->where('applicable_id', $student->section_id)
                                     ->where('is_active', true)
                                     ->first();
            
            if ($allocation) {
                return $allocation->amount ?? $feeType->amount;
            }
        }
        
        // Try to find class-specific allocation
        if ($student->class_id) {
            $allocation = FeeAllocation::where('fee_type_id', $feeType->id)
                                     ->where('academic_year_id', $academicYearId)
                                     ->where('applicable_to', 'class')
                                     ->where('applicable_id', $student->class_id)
                                     ->where('is_active', true)
                                     ->first();
            
            if ($allocation) {
                return $allocation->amount ?? $feeType->amount;
            }
        }
        
        // Try to find program-specific allocation
        if ($student->program_id) {
            $allocation = FeeAllocation::where('fee_type_id', $feeType->id)
                                     ->where('academic_year_id', $academicYearId)
                                     ->where('applicable_to', 'program')
                                     ->where('applicable_id', $student->program_id)
                                     ->where('is_active', true)
                                     ->first();
            
            if ($allocation) {
                return $allocation->amount ?? $feeType->amount;
            }
        }
        
        // Default to the fee type's amount
        return $feeType->amount;
    }
} 