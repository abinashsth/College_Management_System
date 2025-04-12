<?php

namespace App\Notifications;

use App\Models\Student;
use App\Models\StudentRecord;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class StudentRecordCreated extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * The student record instance.
     *
     * @var \App\Models\StudentRecord
     */
    protected $record;

    /**
     * The student instance.
     *
     * @var \App\Models\Student
     */
    protected $student;

    /**
     * Create a new notification instance.
     *
     * @param  \App\Models\StudentRecord  $record
     * @param  \App\Models\Student  $student
     * @return void
     */
    public function __construct(StudentRecord $record, Student $student)
    {
        $this->record = $record;
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
        $recordTypeName = $this->record->getRecordTypeName();
        
        return (new MailMessage)
                    ->subject("New {$recordTypeName} Record Created for {$this->student->name}")
                    ->greeting("Hello {$notifiable->name},")
                    ->line("A new {$recordTypeName} record has been created for {$this->student->name}.")
                    ->line("Title: {$this->record->title}")
                    ->line("Description: " . \Illuminate\Support\Str::limit($this->record->description, 100))
                    ->action('View Record', route('student-records.show', $this->record))
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
            'record_id' => $this->record->id,
            'student_id' => $this->student->id,
            'student_name' => $this->student->name,
            'record_type' => $this->record->record_type,
            'record_type_name' => $this->record->getRecordTypeName(),
            'title' => $this->record->title,
            'created_by' => $this->record->createdBy->name ?? 'System',
        ];
    }
} 