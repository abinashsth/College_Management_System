<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Student Result</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            font-size: 12px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .school-name {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .report-title {
            font-size: 16px;
            font-weight: bold;
            margin: 10px 0;
        }
        .info-section {
            width: 100%;
            margin-bottom: 20px;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
        }
        .info-table td, .info-table th {
            padding: 5px;
            border: 1px solid #ddd;
        }
        .info-table th {
            width: 30%;
            background-color: #f5f5f5;
            text-align: left;
        }
        .marks-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .marks-table th, .marks-table td {
            border: 1px solid #ddd;
            padding: 5px;
            text-align: center;
        }
        .marks-table th {
            background-color: #f5f5f5;
        }
        .marks-table tfoot {
            font-weight: bold;
            background-color: #f5f5f5;
        }
        .result-section {
            margin-top: 20px;
            padding: 10px;
            border: 1px solid #ddd;
            background-color: #f9f9f9;
        }
        .result-label {
            font-weight: bold;
            margin-bottom: 5px;
        }
        .pass {
            color: green;
            font-weight: bold;
        }
        .fail {
            color: red;
            font-weight: bold;
        }
        .remarks {
            margin-top: 20px;
            padding: 10px;
            border: 1px solid #ddd;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            padding-top: 10px;
            border-top: 1px solid #ddd;
        }
        .signature {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
        }
        .signature-line {
            width: 30%;
            border-top: 1px solid #000;
            padding-top: 5px;
            text-align: center;
            display: inline-block;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="school-name">{{ config('app.name', 'College Management System') }}</div>
        <div>Academic Year: {{ date('Y') }}</div>
        <div class="report-title">STUDENT RESULT REPORT</div>
    </div>

    <div class="info-section">
        <table class="info-table">
            <tr>
                <th>Student Name</th>
                <td>{{ $student->name }}</td>
                <th>Roll Number</th>
                <td>{{ $student->roll_number }}</td>
            </tr>
            <tr>
                <th>Admission Number</th>
                <td>{{ $student->admission_number }}</td>
                <th>Class & Section</th>
                <td>
                    @if($student->enrollments->first())
                        {{ $student->enrollments->first()->schoolClass->name ?? 'N/A' }} - 
                        {{ $student->enrollments->first()->section->name ?? 'N/A' }}
                    @else
                        N/A
                    @endif
                </td>
            </tr>
            <tr>
                <th>Exam Name</th>
                <td>{{ $exam->name }}</td>
                <th>Term</th>
                <td>{{ $exam->term ?? 'N/A' }}</td>
            </tr>
        </table>
    </div>

    <div class="report-title">SUBJECT WISE PERFORMANCE</div>
    <table class="marks-table">
        <thead>
            <tr>
                <th>Subject</th>
                <th>Marks Obtained</th>
                <th>Total Marks</th>
                <th>Percentage</th>
                <th>Grade</th>
                <th>GPA</th>
                <th>Result</th>
            </tr>
        </thead>
        <tbody>
            @foreach($results as $result)
                <tr>
                    <td>{{ $result['subject']->name }}</td>
                    <td>
                        @if($result['is_absent'])
                            Absent
                        @else
                            {{ $result['marks_obtained'] }}
                        @endif
                    </td>
                    <td>{{ $result['total_marks'] }}</td>
                    <td>{{ number_format($result['percentage'], 2) }}%</td>
                    <td>{{ $result['grade'] }}</td>
                    <td>{{ number_format($result['gpa'], 2) }}</td>
                    <td class="{{ $result['passed'] ? 'pass' : 'fail' }}">
                        {{ $result['passed'] ? 'Pass' : 'Fail' }}
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td>Total</td>
                <td>{{ $totalObtained }}</td>
                <td>{{ $totalPossible }}</td>
                <td>{{ number_format($totalPercentage, 2) }}%</td>
                <td colspan="2">GPA: {{ number_format($gpaResult['gpa'], 2) }}</td>
                <td class="{{ $gpaResult['passed'] ? 'pass' : 'fail' }}">
                    {{ $gpaResult['passed'] ? 'PASS' : 'FAIL' }}
                </td>
            </tr>
        </tfoot>
    </table>

    <div class="result-section">
        <div class="result-label">Overall Performance:</div>
        <table class="info-table">
            <tr>
                <th>Total Marks</th>
                <td>{{ $totalObtained }} / {{ $totalPossible }}</td>
                <th>Percentage</th>
                <td>{{ number_format($totalPercentage, 2) }}%</td>
            </tr>
            <tr>
                <th>GPA</th>
                <td>{{ number_format($gpaResult['gpa'], 2) }}</td>
                <th>Result</th>
                <td class="{{ $gpaResult['passed'] ? 'pass' : 'fail' }}">
                    {{ $gpaResult['passed'] ? 'PASSED' : 'FAILED' }}
                </td>
            </tr>
            <tr>
                <th>Total Credit Hours</th>
                <td>{{ $gpaResult['total_credits'] }}</td>
                <th>Total Grade Points</th>
                <td>{{ number_format($gpaResult['grade_points'], 2) }}</td>
            </tr>
        </table>
    </div>

    <div class="remarks">
        <div class="result-label">Remarks:</div>
        @php
            $performanceMessage = "";
            if ($totalPercentage >= 90) {
                $performanceMessage = "Outstanding performance. Keep up the excellent work!";
            } elseif ($totalPercentage >= 80) {
                $performanceMessage = "Excellent performance. Well done!";
            } elseif ($totalPercentage >= 70) {
                $performanceMessage = "Very good performance. Keep it up!";
            } elseif ($totalPercentage >= 60) {
                $performanceMessage = "Good performance. Continue working hard!";
            } elseif ($totalPercentage >= 50) {
                $performanceMessage = "Satisfactory performance. There's room for improvement.";
            } elseif ($totalPercentage >= 40) {
                $performanceMessage = "Average performance. Need to work harder.";
            } else {
                $performanceMessage = "Below average performance. Significant improvement needed.";
            }
        @endphp
        <p>{{ $performanceMessage }}</p>
        
        @if(!$gpaResult['passed'])
            <div class="result-label">Improvement Needed in:</div>
            <ul>
                @foreach($results as $result)
                    @if(!$result['passed'])
                        <li>{{ $result['subject']->name }} ({{ number_format($result['percentage'], 2) }}%)</li>
                    @endif
                @endforeach
            </ul>
        @endif
    </div>

    <div class="signature">
        <div class="signature-line">Class Teacher</div>
        <div class="signature-line">Principal</div>
        <div class="signature-line">Parent/Guardian</div>
    </div>

    <div class="footer">
        <p>This is a computer-generated report and does not require a signature.</p>
        <p>Generated on: {{ date('d-m-Y') }}</p>
    </div>
</body>
</html> 