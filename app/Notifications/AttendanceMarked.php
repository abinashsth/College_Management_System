<?php

namespace App\Notifications;

use App\Models\Attendance;
use App\Models\Student;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AttendanceMarked extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * The attendance instance.
     *
     * @var \App\Models\Attendance
     */
    protected $attendance;

    /**
     * The student instance.
     *
     * @var \App\Models\Student
     */
    protected $student;

    /**
     * Create a new notification instance.
     *
     * @param  \App\Models\Attendance  $attendance
     * @param  \App\Models\Student  $student
     * @return void
     */
    public function __construct(Attendance $attendance, Student $student)
    {
        $this->attendance = $attendance;
        $this->student = $student;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $status = $this->attendance->is_present ? 'Present' : 'Absent';
        $class = $this->attendance->class ? $this->attendance->class->name : 'Unknown';
        $subject = $this->attendance->subject ? $this->attendance->subject->name : 'Unknown';
        
        return (new MailMessage)
                    ->subject("Attendance Marked: {$status} - {$this->student->name}")
                    ->greeting("Hello {$notifiable->name},")
                    ->line("Attendance has been marked for {$this->student->name}.")
                    ->line("Date: " . $this->attendance->date->format('d M, Y'))
                    ->line("Status: {$status}")
                    ->line("Class: {$class}")
                    ->line("Subject: {$subject}")
                    ->action('View Attendance Record', route('attendances.show', $this->attendance))
                    ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'attendance_id' => $this->attendance->id,
            'student_id' => $this->student->id,
            'student_name' => $this->student->name,
            'date' => $this->attendance->date->toDateString(),
            'is_present' => $this->attendance->is_present,
            'status' => $this->attendance->is_present ? 'Present' : 'Absent',
            'class_id' => $this->attendance->class_id,
            'class_name' => $this->attendance->class ? $this->attendance->class->name : null,
            'subject_id' => $this->attendance->subject_id,
            'subject_name' => $this->attendance->subject ? $this->attendance->subject->name : null,
            'marked_by' => $this->attendance->created_by ?? 'System',
        ];
    }
} 