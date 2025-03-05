<?php

namespace App\Http\Controllers;

use App\Models\Ledger;
use App\Models\Student;
use App\Models\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LedgerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $ledgers = Ledger::with(['student', 'session'])->orderBy('id', 'desc')->paginate(10);
        return view('ledgers.index', compact('ledgers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $students = Student::all();
        $sessions = Session::where('status', 'active')->get();
        
        return view('ledgers.create', compact('students', 'sessions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'student_id' => 'required|exists:students,id',
            'session_id' => 'required|exists:sessions,id',
            'fee_type' => 'required|string|max:50',
            'amount' => 'required|numeric|min:0',
            'paid_amount' => 'required|numeric|min:0',
            'due_amount' => 'required|numeric|min:0',
            'payment_date' => 'required|date',
            'payment_method' => 'required|string|max:50',
            'transaction_id' => 'nullable|string|max:100',
            'remarks' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        Ledger::create([
            'student_id' => $request->student_id,
            'session_id' => $request->session_id,
            'fee_type' => $request->fee_type,
            'amount' => $request->amount,
            'paid_amount' => $request->paid_amount,
            'due_amount' => $request->due_amount,
            'payment_date' => $request->payment_date,
            'payment_method' => $request->payment_method,
            'transaction_id' => $request->transaction_id,
            'remarks' => $request->remarks,
        ]);

        return redirect()->route('ledgers.index')
            ->with('success', 'Ledger entry created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Ledger $ledger)
    {
        $ledger->load(['student', 'session']);
        return view('ledgers.show', compact('ledger'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Ledger $ledger)
    {
        $students = Student::all();
        $sessions = Session::all();
        
        return view('ledgers.edit', compact('ledger', 'students', 'sessions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Ledger $ledger)
    {
        $validator = Validator::make($request->all(), [
            'student_id' => 'required|exists:students,id',
            'session_id' => 'required|exists:sessions,id',
            'fee_type' => 'required|string|max:50',
            'amount' => 'required|numeric|min:0',
            'paid_amount' => 'required|numeric|min:0',
            'due_amount' => 'required|numeric|min:0',
            'payment_date' => 'required|date',
            'payment_method' => 'required|string|max:50',
            'transaction_id' => 'nullable|string|max:100',
            'remarks' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $ledger->update([
            'student_id' => $request->student_id,
            'session_id' => $request->session_id,
            'fee_type' => $request->fee_type,
            'amount' => $request->amount,
            'paid_amount' => $request->paid_amount,
            'due_amount' => $request->due_amount,
            'payment_date' => $request->payment_date,
            'payment_method' => $request->payment_method,
            'transaction_id' => $request->transaction_id,
            'remarks' => $request->remarks,
        ]);

        return redirect()->route('ledgers.index')
            ->with('success', 'Ledger entry updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Ledger $ledger)
    {
        $ledger->delete();

        return redirect()->route('ledgers.index')
            ->with('success', 'Ledger entry deleted successfully.');
    }

    /**
     * Display student fee summary.
     */
    public function studentSummary($studentId)
    {
        $student = Student::findOrFail($studentId);
        $ledgers = Ledger::where('student_id', $studentId)
            ->orderBy('payment_date', 'desc')
            ->get();
        
        $totalAmount = $ledgers->sum('amount');
        $totalPaid = $ledgers->sum('paid_amount');
        $totalDue = $ledgers->sum('due_amount');
        
        return view('ledgers.student-summary', compact('student', 'ledgers', 'totalAmount', 'totalPaid', 'totalDue'));
    }
} 