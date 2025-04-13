<?php

namespace App\Notifications;

use App\Models\Attendance;
use App\Models\Student;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AttendanceUpdated extends Notification implements ShouldQueue
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
     * The old status value.
     *
     * @var string
     */
    protected $oldStatus;

    /**
     * Create a new notification instance.
     *
     * @param  \App\Models\Attendance  $attendance
     * @param  \App\Models\Student  $student
     * @param  string|null  $oldStatus
     * @return void
     */
    public function __construct(Attendance $attendance, Student $student, ?string $oldStatus = null)
    {
        $this->attendance = $attendance;
        $this->student = $student;
        $this->oldStatus = $oldStatus;
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
        $status = ucfirst($this->attendance->status);
        $class = $this->attendance->class ? $this->attendance->class->name : 'Unknown';
        $subject = $this->attendance->subject ? $this->attendance->subject->name : 'Unknown';
        $oldStatus = $this->oldStatus ? ucfirst($this->oldStatus) : 'Unknown';
        
        $mailMessage = (new MailMessage)
            ->subject("Attendance Updated: {$status} - {$this->student->name}")
            ->greeting("Hello {$notifiable->name},")
            ->line("Attendance has been updated for {$this->student->name}.")
            ->line("Date: " . $this->attendance->attendance_date->format('d M, Y'))
            ->line("Status: {$oldStatus} → {$status}")
            ->line("Class: {$class}")
            ->line("Subject: {$subject}");

        if ($this->attendance->remarks) {
            $mailMessage->line("Remarks: {$this->attendance->remarks}");
        }

        return $mailMessage
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
            'date' => $this->attendance->attendance_date->toDateString(),
            'old_status' => $this->oldStatus,
            'new_status' => $this->attendance->status,
            'class_id' => $this->attendance->section ? $this->attendance->section->class_id : null,
            'class_name' => $this->attendance->section && $this->attendance->section->class ? $this->attendance->section->class->name : null,
            'section_id' => $this->attendance->section_id,
            'section_name' => $this->attendance->section ? $this->attendance->section->section_name : null,
            'subject_id' => $this->attendance->subject_id,
            'subject_name' => $this->attendance->subject ? $this->attendance->subject->name : null,
            'updated_by' => $this->attendance->taken_by ?? 'System',
        ];
    }
} 