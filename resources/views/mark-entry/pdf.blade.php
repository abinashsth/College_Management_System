<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Mark Sheet</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 30px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header h1 {
            margin: 0;
            color: #333;
        }
        .info {
            margin-bottom: 20px;
        }
        .info p {
            margin: 5px 0;
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
        .signature {
            margin-top: 100px;
            display: flex;
            justify-content: space-between;
        }
        .signature div {
            text-align: center;
            width: 200px;
        }
        .signature-line {
            border-top: 1px solid #000;
            margin-top: 50px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ config('app.name') }}</h1>
        <h2>Mark Sheet</h2>
    </div>

    <div class="info">
        <p><strong>Subject:</strong> {{ $marks->first()->subject->name }}</p>
        <p><strong>Exam:</strong> {{ $marks->first()->exam->name }}</p>
        <p><strong>Maximum Marks:</strong> {{ $maxMarks }}</p>
        <p><strong>Date:</strong> {{ now()->format('d/m/Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Roll No.</th>
                <th>Student Name</th>
                <th>Marks</th>
                <th>Grade</th>
                <th>Grade Point</th>
                <th>Remarks</th>
            </tr>
        </thead>
        <tbody>
            @foreach($marks as $mark)
                <tr>
                    <td>{{ $mark->student->roll_number }}</td>
                    <td>{{ $mark->student->name }}</td>
                    <td>{{ $mark->marks }}</td>
                    <td>{{ $mark->grade }}</td>
                    <td>{{ $mark->grade_point }}</td>
                    <td>{{ $mark->remarks }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>This is a computer-generated document. No signature is required.</p>
    </div>

    <div class="signature">
        <div>
            <div class="signature-line"></div>
            <p>Class Teacher</p>
        </div>
        <div>
            <div class="signature-line"></div>
            <p>Subject Teacher</p>
        </div>
        <div>
            <div class="signature-line"></div>
            <p>Principal</p>
        </div>
    </div>
</body>
</html> 