<?php

namespace App\Notifications;

use App\Models\Mark;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Exam;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class GradeAssigned extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * The mark instance.
     *
     * @var \App\Models\Mark
     */
    protected $mark;

    /**
     * The student instance.
     *
     * @var \App\Models\Student
     */
    protected $student;

    /**
     * The exam instance.
     *
     * @var \App\Models\Exam
     */
    protected $exam;

    /**
     * The subject instance.
     *
     * @var \App\Models\Subject
     */
    protected $subject;

    /**
     * Create a new notification instance.
     *
     * @param  \App\Models\Mark  $mark
     * @param  \App\Models\Student  $student
     * @param  \App\Models\Exam  $exam
     * @param  \App\Models\Subject  $subject
     * @return void
     */
    public function __construct(Mark $mark, Student $student, Exam $exam, Subject $subject)
    {
        $this->mark = $mark;
        $this->student = $student;
        $this->exam = $exam;
        $this->subject = $subject;
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
        return (new MailMessage)
                    ->subject("Grade Assigned: {$this->subject->name} - {$this->exam->name}")
                    ->greeting("Hello {$notifiable->name},")
                    ->line("A grade has been assigned for {$this->student->name} in {$this->subject->name} for {$this->exam->name}.")
                    ->line("Marks Obtained: {$this->mark->marks_obtained} out of {$this->exam->total_marks}")
                    ->line("Grade: {$this->mark->grade}")
                    ->line("Status: " . ucfirst($this->mark->status))
                    ->action('View Grade Details', route('marks.show', $this->mark))
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
            'mark_id' => $this->mark->id,
            'student_id' => $this->student->id,
            'student_name' => $this->student->name,
            'exam_id' => $this->exam->id,
            'exam_name' => $this->exam->name,
            'subject_id' => $this->subject->id,
            'subject_name' => $this->subject->name,
            'marks_obtained' => $this->mark->marks_obtained,
            'total_marks' => $this->exam->total_marks,
            'grade' => $this->mark->grade,
            'status' => $this->mark->status,
            'assigned_by' => $this->mark->updatedBy->name ?? 'System',
        ];
    }
} 