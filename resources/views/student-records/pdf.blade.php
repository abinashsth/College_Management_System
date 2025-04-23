<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Student Records - {{ $student->first_name }} {{ $student->last_name }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            font-size: 12px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .subtitle {
            font-size: 14px;
            margin-bottom: 5px;
        }
        .student-info {
            width: 100%;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            padding: 10px;
            background-color: #f9f9f9;
        }
        .student-info table {
            width: 100%;
        }
        .student-info th {
            text-align: left;
            width: 150px;
            padding: 5px;
        }
        .student-info td {
            padding: 5px;
        }
        .record {
            margin-bottom: 20px;
            border: 1px solid #ddd;
            padding: 10px;
        }
        .record-header {
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
            margin-bottom: 10px;
            display: flex;
            justify-content: space-between;
        }
        .record-title {
            font-weight: bold;
            font-size: 14px;
        }
        .record-type {
            background-color: #eee;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 10px;
        }
        .record-content {
            margin-bottom: 10px;
        }
        .record-meta {
            font-size: 10px;
            color: #666;
            text-align: right;
        }
        .page-number {
            text-align: center;
            font-size: 10px;
            margin-top: 20px;
        }
        .record-type-academic { background-color: #d4edda; color: #155724; }
        .record-type-disciplinary { background-color: #f8d7da; color: #721c24; }
        .record-type-achievement { background-color: #fff3cd; color: #856404; }
        .record-type-personal { background-color: #cce5ff; color: #004085; }
        .record-type-enrollment { background-color: #d1ecf1; color: #0c5460; }
        .record-type-medical { background-color: #e2e3e5; color: #383d41; }
        .record-type-notes { background-color: #f5f5f5; color: #333; }
        .no-records {
            text-align: center;
            padding: 20px;
            color: #666;
            font-style: italic;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table.records-table th, table.records-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        table.records-table th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">Student Records</div>
        <div class="subtitle">{{ config('app.name', 'Laravel') }}</div>
        <div class="subtitle">Generated on {{ now()->format('F d, Y') }}</div>
    </div>
    
    <div class="student-info">
        <table>
            <tr>
                <th>Student Name:</th>
                <td>{{ $student->first_name }} {{ $student->last_name }}</td>
                <th>Student ID:</th>
                <td>{{ $student->student_id ?? $student->admission_number }}</td>
            </tr>
            <tr>
                <th>Email:</th>
                <td>{{ $student->email ?? 'N/A' }}</td>
                <th>Phone:</th>
                <td>{{ $student->phone ?? 'N/A' }}</td>
            </tr>
            @if($student->program)
            <tr>
                <th>Program:</th>
                <td>{{ $student->program->name }}</td>
                <th>Batch Year:</th>
                <td>{{ $student->batch_year ?? 'N/A' }}</td>
            </tr>
            @endif
        </table>
    </div>
    
    @if($records->count() > 0)
        <table class="records-table">
            <thead>
                <tr>
                    <th width="20%">Date</th>
                    <th width="15%">Type</th>
                    <th width="30%">Title</th>
                    <th width="35%">Description</th>
                </tr>
            </thead>
            <tbody>
                @foreach($records as $record)
                <tr>
                    <td>{{ $record->created_at->format('M d, Y') }}</td>
                    <td>
                        <span class="record-type record-type-{{ $record->record_type }}">
                            {{ ucfirst($record->record_type) }}
                        </span>
                    </td>
                    <td>{{ $record->title }}</td>
                    <td>{{ Str::limit($record->description, 100) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        
        @foreach($records as $record)
        <div class="record">
            <div class="record-header">
                <div class="record-title">{{ $record->title }}</div>
                <div>
                    <span class="record-type record-type-{{ $record->record_type }}">
                        {{ ucfirst($record->record_type) }}
                    </span>
                </div>
            </div>
            <div class="record-content">
                {{ $record->description }}
            </div>
            @if($record->record_data && count($record->record_data) > 0)
            <div class="record-details">
                <table>
                    @foreach($record->record_data as $key => $value)
                    <tr>
                        <th width="30%">{{ ucwords(str_replace('_', ' ', $key)) }}:</th>
                        <td>{{ is_array($value) ? implode(', ', $value) : $value }}</td>
                    </tr>
                    @endforeach
                </table>
            </div>
            @endif
            <div class="record-meta">
                Created: {{ $record->created_at->format('M d, Y') }} by {{ $record->createdBy->name }}
                @if($record->created_at->ne($record->updated_at))
                | Updated: {{ $record->updated_at->format('M d, Y') }} by {{ $record->updatedBy->name }}
                @endif
            </div>
        </div>
        @endforeach
    @else
        <div class="no-records">
            No records found for this student.
        </div>
    @endif
    
    <div class="page-number">
        Page <span class="pagenum"></span>
    </div>
</body>
</html> 