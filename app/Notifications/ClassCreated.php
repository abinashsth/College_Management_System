<?php

namespace App\Notifications;

use App\Models\Classes;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ClassCreated extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * The class instance.
     *
     * @var \App\Models\Classes
     */
    protected $class;

    /**
     * Create a new notification instance.
     *
     * @param  \App\Models\Classes  $class
     * @return void
     */
    public function __construct(Classes $class)
    {
        $this->class = $class;
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
        $programName = $this->class->program ? $this->class->program->name : 'N/A';
        $departmentName = $this->class->department ? $this->class->department->name : 'N/A';
        
        return (new MailMessage)
                    ->subject("New Class Created: {$this->class->class_name}")
                    ->greeting("Hello {$notifiable->name},")
                    ->line("A new class has been created in the system.")
                    ->line("Class Name: {$this->class->class_name}")
                    ->line("Program: {$programName}")
                    ->line("Department: {$departmentName}")
                    ->line("Capacity: {$this->class->capacity}")
                    ->line("Status: " . ucfirst($this->class->status))
                    ->action('View Class Details', route('classes.show', $this->class))
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
            'class_id' => $this->class->id,
            'class_name' => $this->class->class_name,
            'program_id' => $this->class->program_id,
            'program_name' => $this->class->program ? $this->class->program->name : null,
            'department_id' => $this->class->department_id,
            'department_name' => $this->class->department ? $this->class->department->name : null,
            'academic_year_id' => $this->class->academic_year_id,
            'academic_year' => $this->class->academicYear ? $this->class->academicYear->name : null,
            'capacity' => $this->class->capacity,
            'status' => $this->class->status,
            'description' => $this->class->description,
            'created_at' => now()->toDateTimeString(),
        ];
    }
} 