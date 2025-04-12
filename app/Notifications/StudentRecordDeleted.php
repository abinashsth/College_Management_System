<?php

namespace App\Notifications;

use App\Models\Student;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class StudentRecordDeleted extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * The deleted record data.
     *
     * @var array
     */
    protected $recordData;

    /**
     * The student instance.
     *
     * @var \App\Models\Student
     */
    protected $student;

    /**
     * Create a new notification instance.
     *
     * @param  array  $recordData
     * @param  \App\Models\Student  $student
     * @return void
     */
    public function __construct(array $recordData, Student $student)
    {
        $this->recordData = $recordData;
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
        $recordTypeName = $this->recordData['record_type_name'] ?? 'Student';
        
        return (new MailMessage)
                    ->subject("{$recordTypeName} Record Deleted for {$this->student->name}")
                    ->greeting("Hello {$notifiable->name},")
                    ->line("A {$recordTypeName} record has been deleted for {$this->student->name}.")
                    ->line("Title: {$this->recordData['title']}")
                    ->line('This record is no longer available in the system.')
                    ->action('View Student Profile', route('students.show', $this->student))
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
            'student_id' => $this->student->id,
            'student_name' => $this->student->name,
            'record_type' => $this->recordData['record_type'] ?? null,
            'record_type_name' => $this->recordData['record_type_name'] ?? 'Student',
            'title' => $this->recordData['title'] ?? null,
            'deleted_by' => $this->recordData['deleted_by'] ?? 'System',
            'deleted_at' => now()->toDateTimeString(),
        ];
    }
} 