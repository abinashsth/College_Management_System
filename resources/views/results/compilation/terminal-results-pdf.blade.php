<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Terminal Results</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .school-name {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .result-title {
            font-size: 20px;
            margin-bottom: 20px;
        }
        .details {
            margin-bottom: 20px;
        }
        .details-row {
            margin-bottom: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f5f5f5;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
        }
        .signature-line {
            width: 200px;
            border-top: 1px solid #000;
            margin: 50px auto 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="school-name">{{ config('app.name') }}</div>
        <div class="result-title">Terminal Examination Results</div>
    </div>

    <div class="details">
        <div class="details-row">
            <strong>Class:</strong> {{ $class->class_name }}
        </div>
        <div class="details-row">
            <strong>Exam Type:</strong> {{ $examType->name }}
        </div>
        <div class="details-row">
            <strong>Academic Session:</strong> {{ $academicSession->name }}
        </div>
        <div class="details-row">
            <strong>Date:</strong> {{ now()->format('F d, Y') }}
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Rank</th>
                <th>Student Name</th>
                <th>Roll Number</th>
                <th>Total Marks</th>
                <th>Percentage</th>
                <th>Grade</th>
                <th>Remarks</th>
            </tr>
        </thead>
        <tbody>
            @foreach($results as $result)
                <tr>
                    <td>{{ $result->rank }}</td>
                    <td>{{ $result->student->name }}</td>
                    <td>{{ $result->student->roll_number }}</td>
                    <td>{{ number_format($result->total_marks, 2) }}</td>
                    <td>{{ number_format($result->percentage, 2) }}%</td>
                    <td>{{ $result->grade }}</td>
                    <td>{{ $result->remarks }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <div class="signature-line"></div>
        <div>Principal's Signature</div>
    </div>
</body>
</html> 