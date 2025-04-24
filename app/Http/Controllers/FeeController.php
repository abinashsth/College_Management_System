<?php

namespace App\Http\Controllers;

use App\Models\Fee;
use App\Models\Student;
use App\Models\FeeStructure;
use App\Models\AcademicSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FeeController extends Controller
{
    public function index()
    {
        $fees = Fee::with(['student', 'academicSession'])->latest()->paginate(10);
        return view('fees.index', compact('fees'));
    }

    public function create()
    {
        $students = Student::all();
        $feeStructures = FeeStructure::all();
        $academicSessions = AcademicSession::all();
        return view('fees.create', compact('students', 'feeStructures', 'academicSessions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'fee_structure_id' => 'required|exists:fee_structures,id',
            'academic_session_id' => 'required|exists:academic_sessions,id',
            'amount' => 'required|numeric|min:0',
            'due_date' => 'required|date',
            'remarks' => 'nullable|string'
        ]);

        $fee = Fee::create($validated);
        return redirect()->route('fees.index')->with('success', 'Fee record created successfully');
    }

    public function show(Fee $fee)
    {
        $fee->load(['student', 'academicSession', 'feeStructure']);
        return view('fees.show', compact('fee'));
    }

    public function edit(Fee $fee)
    {
        $students = Student::all();
        $feeStructures = FeeStructure::all();
        $academicSessions = AcademicSession::all();
        return view('fees.edit', compact('fee', 'students', 'feeStructures', 'academicSessions'));
    }

    public function update(Request $request, Fee $fee)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'fee_structure_id' => 'required|exists:fee_structures,id',
            'academic_session_id' => 'required|exists:academic_sessions,id',
            'amount' => 'required|numeric|min:0',
            'due_date' => 'required|date',
            'remarks' => 'nullable|string'
        ]);

        $fee->update($validated);
        return redirect()->route('fees.index')->with('success', 'Fee record updated successfully');
    }

    public function destroy(Fee $fee)
    {
        $fee->delete();
        return redirect()->route('fees.index')->with('success', 'Fee record deleted successfully');
    }

    public function recordPayment(Request $request, Fee $fee)
    {
        $validated = $request->validate([
            'paid_amount' => 'required|numeric|min:0|max:' . ($fee->amount - $fee->paid_amount),
            'payment_method' => 'required|string',
            'transaction_id' => 'required|string',
            'payment_date' => 'required|date',
            'remarks' => 'nullable|string'
        ]);

        DB::transaction(function () use ($fee, $validated) {
            $fee->paid_amount += $validated['paid_amount'];
            $fee->remaining_amount = $fee->amount - $fee->paid_amount;
            $fee->payment_method = $validated['payment_method'];
            $fee->transaction_id = $validated['transaction_id'];
            $fee->payment_date = $validated['payment_date'];
            $fee->remarks = $validated['remarks'];
            
            if ($fee->paid_amount >= $fee->amount) {
                $fee->status = 'paid';
            } elseif ($fee->paid_amount > 0) {
                $fee->status = 'partially_paid';
            }
            
            $fee->save();
        });

        return redirect()->route('fees.show', $fee)->with('success', 'Payment recorded successfully');
    }

    public function history()
    {
        $payments = Fee::where('status', '!=', 'pending')
            ->with(['student', 'academicSession'])
            ->latest('payment_date')
            ->paginate(15);
            
        return view('fees.history', compact('payments'));
    }

    public function generateInvoice(Fee $fee)
    {
        $fee->load(['student', 'academicSession', 'feeStructure']);
        return view('fees.invoice', compact('fee'));
    }

    public function downloadReceipt(Fee $fee)
    {
        $fee->load(['student', 'academicSession', 'feeStructure']);
        return view('fees.receipt', compact('fee'));
    }
}