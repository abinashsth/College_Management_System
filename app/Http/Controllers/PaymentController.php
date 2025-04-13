<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Invoice;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PaymentController extends Controller
{
    /**
     * Display a listing of the payments.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Payment::with(['student', 'invoice']);
        
        // Apply filters
        if ($request->filled('student_id')) {
            $query->where('student_id', $request->student_id);
        }
        
        if ($request->filled('invoice_id')) {
            $query->where('invoice_id', $request->invoice_id);
        }
        
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }
        
        if ($request->filled('date_from')) {
            $query->whereDate('payment_date', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('payment_date', '<=', $request->date_to);
        }
        
        $payments = $query->orderBy('payment_date', 'desc')->paginate(15);
        
        // Get filter options
        $students = Student::orderBy('name')->get();
        $invoices = Invoice::orderBy('invoice_number')->get();
        $paymentMethods = [
            'cash' => 'Cash',
            'check' => 'Check',
            'bank_transfer' => 'Bank Transfer',
            'credit_card' => 'Credit Card',
            'online_payment' => 'Online Payment',
            'other' => 'Other'
        ];
        
        return view('finance.payments.index', compact('payments', 'students', 'invoices', 'paymentMethods'));
    }

    /**
     * Show the form for creating a new payment.
     *
     * @param  \App\Models\Invoice|null  $invoice
     * @return \Illuminate\Http\Response
     */
    public function create(Invoice $invoice = null)
    {
        if ($invoice) {
            $invoice->load('student');
            $student = $invoice->student;
            $invoices = [$invoice];
        } else {
            $students = Student::orderBy('name')->get();
            $invoices = Invoice::whereIn('status', ['pending', 'partial', 'overdue'])
                              ->orderBy('invoice_number')
                              ->get();
            $student = null;
        }
        
        $paymentMethods = [
            'cash' => 'Cash',
            'check' => 'Check',
            'bank_transfer' => 'Bank Transfer',
            'credit_card' => 'Credit Card',
            'online_payment' => 'Online Payment',
            'other' => 'Other'
        ];
        
        return view('finance.payments.create', compact('invoice', 'student', 'students', 'invoices', 'paymentMethods'));
    }

    /**
     * Store a newly created payment in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'invoice_id' => 'required|exists:invoices,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:cash,check,bank_transfer,credit_card,online_payment,other',
            'payment_date' => 'required|date',
            'reference_number' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
        ]);
        
        // Get the invoice
        $invoice = Invoice::findOrFail($validated['invoice_id']);
        
        // Validate that payment amount doesn't exceed the balance
        if ($validated['amount'] > $invoice->balance_amount) {
            return redirect()->back()
                ->with('error', 'Payment amount cannot exceed the invoice balance.')
                ->withInput();
        }
        
        try {
            DB::beginTransaction();
            
            // Create the payment
            $payment = Payment::create([
                'invoice_id' => $validated['invoice_id'],
                'student_id' => $invoice->student_id,
                'amount' => $validated['amount'],
                'payment_method' => $validated['payment_method'],
                'payment_date' => $validated['payment_date'],
                'receipt_number' => $this->generateReceiptNumber(),
                'reference_number' => $validated['reference_number'],
                'notes' => $validated['notes'],
                'created_by' => Auth::id(),
            ]);
            
            // Update invoice
            $newPaidAmount = $invoice->paid_amount + $validated['amount'];
            $newBalanceAmount = $invoice->total_amount - $newPaidAmount;
            
            // Determine new status
            $newStatus = 'pending';
            if ($newBalanceAmount <= 0) {
                $newStatus = 'paid';
            } elseif ($newPaidAmount > 0) {
                $newStatus = 'partial';
            }
            
            // Check if overdue
            if ($newStatus !== 'paid' && $invoice->due_date < now()->toDateString()) {
                $newStatus = 'overdue';
            }
            
            $invoice->update([
                'paid_amount' => $newPaidAmount,
                'balance_amount' => $newBalanceAmount,
                'status' => $newStatus,
                'updated_by' => Auth::id(),
            ]);
            
            DB::commit();
            
            return redirect()->route('payments.show', $payment)
                ->with('success', 'Payment recorded successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to record payment: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified payment.
     *
     * @param  \App\Models\Payment  $payment
     * @return \Illuminate\Http\Response
     */
    public function show(Payment $payment)
    {
        $payment->load(['student', 'invoice', 'invoice.invoiceItems.feeType']);
        return view('finance.payments.show', compact('payment'));
    }

    /**
     * Show the form for editing the specified payment.
     *
     * @param  \App\Models\Payment  $payment
     * @return \Illuminate\Http\Response
     */
    public function edit(Payment $payment)
    {
        // Don't allow editing payments that would disrupt the system
        return redirect()->route('payments.show', $payment)
            ->with('error', 'Payments cannot be edited to maintain financial integrity. Please void this payment and create a new one if needed.');
    }

    /**
     * Remove the specified payment from storage (soft delete/void).
     *
     * @param  \App\Models\Payment  $payment
     * @return \Illuminate\Http\Response
     */
    public function destroy(Payment $payment)
    {
        try {
            DB::beginTransaction();
            
            // Get the invoice
            $invoice = $payment->invoice;
            
            // Update invoice
            $newPaidAmount = $invoice->paid_amount - $payment->amount;
            $newBalanceAmount = $invoice->total_amount - $newPaidAmount;
            
            // Determine new status
            $newStatus = 'pending';
            if ($newPaidAmount <= 0) {
                $newStatus = 'pending';
            } elseif ($newPaidAmount > 0 && $newPaidAmount < $invoice->total_amount) {
                $newStatus = 'partial';
            } elseif ($newPaidAmount >= $invoice->total_amount) {
                $newStatus = 'paid';
            }
            
            // Check if overdue
            if ($newStatus !== 'paid' && $invoice->due_date < now()->toDateString()) {
                $newStatus = 'overdue';
            }
            
            $invoice->update([
                'paid_amount' => $newPaidAmount,
                'balance_amount' => $newBalanceAmount,
                'status' => $newStatus,
                'updated_by' => Auth::id(),
            ]);
            
            // Soft delete/void the payment
            $payment->update([
                'is_voided' => true,
                'voided_by' => Auth::id(),
                'voided_at' => now(),
                'void_reason' => request('void_reason', 'Payment voided by administrator'),
            ]);
            
            DB::commit();
            
            return redirect()->route('payments.index')
                ->with('success', 'Payment voided successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to void payment: ' . $e->getMessage());
        }
    }
    
    /**
     * Print a receipt for the specified payment.
     *
     * @param  \App\Models\Payment  $payment
     * @return \Illuminate\Http\Response
     */
    public function printReceipt(Payment $payment)
    {
        $payment->load(['student', 'invoice', 'invoice.invoiceItems.feeType']);
        return view('finance.payments.receipt', compact('payment'));
    }
    
    /**
     * Generate a new receipt number.
     *
     * @return string
     */
    private function generateReceiptNumber()
    {
        $prefix = 'RCPT-' . date('Y');
        $lastPayment = Payment::where('receipt_number', 'like', $prefix . '%')
                            ->orderBy('id', 'desc')
                            ->first();
        
        if ($lastPayment) {
            $lastNumber = (int) substr($lastPayment->receipt_number, strlen($prefix) + 1);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        
        return $prefix . '-' . str_pad($newNumber, 5, '0', STR_PAD_LEFT);
    }
} 