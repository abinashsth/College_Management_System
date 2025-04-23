<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Registration Confirmation - {{ config('app.name') }}</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
        }
        .header {
            background-color: #3490dc;
            color: white;
            padding: 20px;
            text-align: center;
        }
        .content {
            padding: 20px;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 10px 20px;
            text-align: center;
            font-size: 0.8em;
            color: #6c757d;
        }
        h1 {
            color: #2779bd;
        }
        .info {
            margin: 20px 0;
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Student Registration Confirmation</h1>
    </div>
    
    <div class="content">
        <p>Dear Parent/Guardian,</p>
        
        <p>We are pleased to inform you that <strong>{{ $student->first_name }} {{ $student->last_name }}</strong> has been successfully registered at {{ config('app.name') }}.</p>
        
        <div class="info">
            <h3>Student Details:</h3>
            <p><strong>Student ID:</strong> {{ $student->registration_number }}</p>
            <p><strong>Department:</strong> {{ $student->department->name ?? 'Not assigned' }}</p>
            <p><strong>Program:</strong> {{ $student->program->name ?? 'Not assigned' }}</p>
            <p><strong>Class:</strong> {{ $student->class->name ?? 'Not assigned' }}</p>
            <p><strong>Academic Session:</strong> {{ $student->academicSession->name ?? 'Not assigned' }}</p>
        </div>
        
        <p>We encourage you to stay involved in your child's academic journey. You will receive regular updates regarding their progress, attendance, and important events.</p>
        
        <p>If you have any questions or need to update your contact information, please contact our administrative office.</p>
        
        <p>Thank you for entrusting us with your child's education. We are committed to providing a supportive and enriching learning environment.</p>
        
        <p>Sincerely,<br>
        The {{ config('app.name') }} Team</p>
    </div>
    
    <div class="footer">
        <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
    </div>
</body>
</html> 