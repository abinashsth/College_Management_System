<?php

namespace App\Notifications;

use App\Models\Exam;
use App\Models\Student;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ExamResultPublished extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * The exam instance.
     *
     * @var \App\Models\Exam
     */
    protected $exam;

    /**
     * The student instance.
     *
     * @var \App\Models\Student|null
     */
    protected $student;

    /**
     * Create a new notification instance.
     *
     * @param  \App\Models\Exam  $exam
     * @param  \App\Models\Student|null  $student
     * @return void
     */
    public function __construct(Exam $exam, Student $student = null)
    {
        $this->exam = $exam;
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
        $mailMessage = (new MailMessage)
            ->subject("Exam Results Published: {$this->exam->name}")
            ->greeting("Hello {$notifiable->name},")
            ->line("The results for {$this->exam->name} have been published.");

        if ($this->student) {
            $mailMessage->line("Student: {$this->student->name}");
            $mailMessage->action('View Results', route('exams.student-results', [
                'exam' => $this->exam->id,
                'student' => $this->student->id
            ]));
        } else {
            $mailMessage->action('View Results', route('exams.results', $this->exam));
        }

        return $mailMessage->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        $data = [
            'exam_id' => $this->exam->id,
            'exam_name' => $this->exam->name,
            'published_at' => now()->toDateTimeString(),
            'published_by' => $this->exam->updatedBy->name ?? 'System',
        ];

        if ($this->student) {
            $data['student_id'] = $this->student->id;
            $data['student_name'] = $this->student->name;
        }

        return $data;
    }
} 