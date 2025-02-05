{{-- 
@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Employee Salary Details</h2>
    <p><strong>Employee:</strong> {{ $employeeSalary->employee->name }}</p>
    <p><strong>Basic Salary:</strong> {{ $employeeSalary->basic_salary }}</p>
    <p><strong>Allowances:</strong> {{ $employeeSalary->allowances }}</p>
    <p><strong>Deductions:</strong> {{ $employeeSalary->deductions }}</p>
    <p><strong>Total Salary:</strong> {{ $employeeSalary->basic_salary + $employeeSalary->allowances - $employeeSalary->deductions }}</p>
    <p><strong>Payment Date:</strong> {{ $employeeSalary->payment_date }}</p>
    <p><strong>Payment Method:</strong> {{ ucfirst($employeeSalary->payment_method) }}</p>
    <p><strong>Status:</strong> {{ ucfirst($employeeSalary->status) }}</p>
    <p><strong>Remarks:</strong> {{ $employeeSalary->remarks }}</p>
    <a href="{{ route('account.salary_management.employee_salary.index') }}" class="btn btn-primary">Back</a>
</div>
@endsection --}}
