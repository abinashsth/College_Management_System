@extends('layouts.app')

@section('content')
<div class="container mx-auto px-6 py-8">
    <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
        <h2 class="text-2xl font-bold mb-6">Create Employee Salary</h2>

        <form action="{{ route('account.salary_management.employee_salary.store') }}" method="POST" id="salaryForm">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Employee Selection -->
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="employee">
                        Select Employee
                    </label>
                    <select name="employee_id" id="employee" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline focus:ring-2 focus:ring-blue-500" required>
                        <option value="">Select Employee</option>
                        @foreach($employees as $employee)
                            <option value="{{ $employee->id }}" 
                                    data-basic-salary="{{ $employee->basic_salary }}"
                                    data-department="{{ $employee->department_id }}">
                                {{ $employee->name }} - {{ $employee->department }} ({{ $employee->employee_id }})
                            </option>
                        @endforeach
                    </select>
                    @error('employee_id')
                        <p class="text-red-500 text-xs italic">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Salary Month    Selection -->
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="salary_month">
                        Salary Month
                    </label>
                    <input type="month" name="salary_month" id="salary_month" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500" required>      
                    @error('salary_month')
                        <p class="text-red-500 text-xs italic">{{ $message }}</p>
                    @enderror   
                </div>

               
            </div>

            <!-- Salary Calculation Section -->
            <div class="bg-gray-50 p-6 rounded-lg shadow-md mb-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Salary Calculation</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Basic Salary -->
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="basic_salary">
                            Basic Salary
                        </label>
                        <div class="relative">
                          
                            <input type="number" 
                                   name="basic_salary" 
                                   id="basic_salary" 
                                   step="0.01" 
                                   min="0"
                                   required
                                   placeholder="0.00"
                                   oninput="calculateSalary()"
                                   class="pl-8 shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        @error('basic_salary')
                            <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Total Allowances -->
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="allowances">
                            Allowances
                        </label>
                        <div class="relative">
                           
                            <input type="number" 
                                   name="allowances" 
                                   id="allowances" 
                                   step="0.01" 
                                   min="0"
                                   required
                                   placeholder="0.00"
                                   oninput="calculateSalary()"
                                   class="pl-8 shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        @error('allowances')
                            <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Total Deductions -->
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="deductions">
                            Deductions
                        </label>
                        <div class="relative">
                           
                            <input type="number" 
                                   name="deductions" 
                                   id="deductions" 
                                   step="0.01" 
                                   min="0"
                                   required
                                   placeholder="0.00"
                                   oninput="calculateSalary()"
                                   class="pl-8 shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        </div>
                        @error('deductions')
                            <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Net Salary -->
                <div class="mt-8">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="net_salary">
                        Net Salary
                    </label>
                    <div class="relative">
                       
                        <input type="text" 
                               name="net_salary" 
                               id="net_salary" 
                               readonly
                               class="pl-10 shadow-lg border-2 border-blue-500 rounded-lg w-full py-3 px-4 text-blue-700 bg-blue-50 leading-tight text-xl font-bold"
                               placeholder="0.00">
                    </div>
                    @error('net_salary')
                        <p class="text-red-500 text-xs italic mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

          

            <!-- Payment Details -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="payment_date">
                        Payment Date
                    </label>
                    <input type="date" 
                           name="payment_date" 
                           id="payment_date" 
                           class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500" 
                           required>
                    @error('payment_date')
                        <p class="text-red-500 text-xs italic">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-1" for="status">
                        Status
                    </label>
                    <select name="status" 
                            id="status" 
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500" 
                            required>
                        <option value="">Select Status</option>
                       
                        <option value="approved">Approved</option>
                        <option value="rejected">Rejected</option>
                     
                    </select>
                    @error('status')
                        <p class="text-red-500 text-xs italic">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-1" for="payment_method">
                        Payment Method
                    </label>
                    <select name="payment_method" 
                            id="payment_method" 
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500" 
                            required>
                        <option value="">Select Payment Method</option>
                        <option value="bank_transfer">Bank Transfer</option>
                        <option value="cash">Cash</option>
                        <option value="check">Check</option>
                    </select>
                    @error('payment_method')
                        <p class="text-red-500 text-xs italic">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-1" for="remarks">
                    Remarks
                </label>
                <textarea name="remarks" 
                          id="remarks" 
                          rows="3" 
                          class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500"
                          placeholder="Enter any additional notes or remarks..."></textarea>
            </div>

            <div class="flex items-center justify-between">
                <button type="submit" 
                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                    Create Salary Record
                </button>
                <a href="{{ route('account.salary_management.employee_salary.index') }}" 
                   class="text-blue-500 hover:text-blue-700 font-medium">
                    Back to List
                </a>
            </div>
        </form>
    </div>

    @if($errors->any())
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
            <p class="font-bold">Please correct the following errors:</p>
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
</div>


<script>
    // Auto-populate basic salary when employee is selected
    document.getElementById('employee').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const basicSalary = selectedOption.getAttribute('data-basic-salary');
        const departmentId = selectedOption.getAttribute('data-department');
        
        document.getElementById('basic_salary').value = basicSalary || '';
        document.getElementById('department').value = departmentId || '';
        
        calculateSalary();
    });

    // Calculate net salary
    function calculateSalary() {
        const basicSalary = parseFloat(document.getElementById('basic_salary').value) || 0;
        const allowances = parseFloat(document.getElementById('allowances').value) || 0;
        const deductions = parseFloat(document.getElementById('deductions').value) || 0;
        
        const netSalary = basicSalary + allowances - deductions;
        
        // Format net salary with 2 decimal places and thousands separator
        const formattedNetSalary = new Intl.NumberFormat('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }).format(netSalary);
        
        document.getElementById('net_salary').value = formattedNetSalary;
    }

    // Initialize calculation
    calculateSalary();
</script>


<script>
class SalaryCalculator {
    constructor() {
        this.form = document.getElementById('salaryForm');
        this.elements = {
            employee: document.getElementById('employee'),
            basicSalary: document.getElementById('basic_salary'),
            totalAllowances: document.getElementById('total_allowances'),
            totalDeductions: document.getElementById('total_deductions'),
            netSalary: document.getElementById('net_salary'),
            paymentDate: document.getElementById('payment_date')
        };
        this.init();
    }

    init() {
        this.setDefaultDate();
        this.attachEventListeners();
    }

    setDefaultDate() {
        this.elements.paymentDate.valueAsDate = new Date();
    }

    attachEventListeners() {
        this.elements.employee.addEventListener('change', () => this.handleEmployeeChange());
        this.elements.basicSalary.addEventListener('input', () => this.calculateSalary());
        document.querySelectorAll('input[type="checkbox"][data-amount]').forEach(checkbox => {
            checkbox.addEventListener('change', () => this.calculateSalary());
        });
    }

    handleEmployeeChange() {
        const selectedOption = this.elements.employee.options[this.elements.employee.selectedIndex];
        
        if (selectedOption.value) {
            this.elements.basicSalary.value = selectedOption.dataset.basicSalary || '0';
            this.elements.department.value = selectedOption.dataset.department;
            this.calculateSalary();
        }
    }

    calculateSalary() {
        const basicSalary = parseFloat(this.elements.basicSalary.value) || 0;
        const { allowances, deductions } = this.calculateComponents();
        const netSalary = basicSalary + allowances - deductions;

        this.updateDisplayValues(allowances, deductions, netSalary);
    }

    calculateComponents() {
        return Array.from(document.querySelectorAll('input[type="checkbox"][data-amount]:checked'))
            .reduce((totals, checkbox) => {
                const amount = parseFloat(checkbox.dataset.amount) || 0;
                const type = checkbox.dataset.type;
                
                if (type === 'allowance') totals.allowances += amount;
                if (type === 'deduction') totals.deductions += amount;
                
                return totals;
            }, { allowances: 0, deductions: 0 });
    }

    updateDisplayValues(allowances, deductions, netSalary) {
        this.elements.totalAllowances.value = allowances.toFixed(2);
        this.elements.totalDeductions.value = deductions.toFixed(2);
        this.elements.netSalary.value = netSalary.toFixed(2);
    }
}

document.addEventListener('DOMContentLoaded', () => new SalaryCalculator());
</script>
@endsection