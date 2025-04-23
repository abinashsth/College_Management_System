<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to {{ config('app.name') }}</title>
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
        <h1>Welcome to {{ config('app.name') }}</h1>
    </div>
    
    <div class="content">
        <p>Dear {{ $student->first_name }} {{ $student->last_name }},</p>
        
        <p>Congratulations on your successful registration at {{ config('app.name') }}!</p>
        
        <p>We are excited to have you join our academic community. Your journey with us begins now, and we're here to support you every step of the way.</p>
        
        <div class="info">
            <h3>Your Registration Details:</h3>
            <p><strong>Student ID:</strong> {{ $student->registration_number }}</p>
            <p><strong>Department:</strong> {{ $student->department->name ?? 'Not assigned' }}</p>
            <p><strong>Program:</strong> {{ $student->program->name ?? 'Not assigned' }}</p>
            <p><strong>Class:</strong> {{ $student->class->name ?? 'Not assigned' }}</p>
            <p><strong>Academic Session:</strong> {{ $student->academicSession->name ?? 'Not assigned' }}</p>
        </div>
        
        <p>Please log in to your student portal to complete your profile, check your class schedule, and access important resources. If you have any questions or need assistance, please don't hesitate to contact our support team.</p>
        
        <p>Best wishes for a successful academic journey!</p>
        
        <p>Warm regards,<br>
        The {{ config('app.name') }} Team</p>
    </div>
    
    <div class="footer">
        <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        <p>This email was sent to {{ $student->email }}</p>
    </div>
</body>
</html> 