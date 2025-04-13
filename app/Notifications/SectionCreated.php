<?php

namespace App\Notifications;

use App\Models\Section;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SectionCreated extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * The section instance.
     *
     * @var \App\Models\Section
     */
    protected $section;

    /**
     * Create a new notification instance.
     *
     * @param  \App\Models\Section  $section
     * @return void
     */
    public function __construct(Section $section)
    {
        $this->section = $section;
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
        $className = $this->section->class ? $this->section->class->class_name : 'N/A';
        $teacherName = $this->section->teacher ? $this->section->teacher->name : 'Not Assigned';
        
        $startTime = $this->section->start_time ? $this->section->start_time->format('h:i A') : 'N/A';
        $endTime = $this->section->end_time ? $this->section->end_time->format('h:i A') : 'N/A';
        
        return (new MailMessage)
                    ->subject("New Section Created: {$this->section->section_name}")
                    ->greeting("Hello {$notifiable->name},")
                    ->line("A new section has been created in the system.")
                    ->line("Section Name: {$this->section->section_name}")
                    ->line("Class: {$className}")
                    ->line("Teacher: {$teacherName}")
                    ->line("Capacity: {$this->section->capacity}")
                    ->line("Schedule: {$startTime} - {$endTime}")
                    ->line("Status: " . ucfirst($this->section->status))
                    ->action('View Section Details', route('sections.show', $this->section))
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
            'section_id' => $this->section->id,
            'section_name' => $this->section->section_name,
            'class_id' => $this->section->class_id,
            'class_name' => $this->section->class ? $this->section->class->class_name : null,
            'teacher_id' => $this->section->teacher_id,
            'teacher_name' => $this->section->teacher ? $this->section->teacher->name : null,
            'capacity' => $this->section->capacity,
            'start_time' => $this->section->start_time ? $this->section->start_time->format('H:i') : null,
            'end_time' => $this->section->end_time ? $this->section->end_time->format('H:i') : null,
            'status' => $this->section->status,
            'description' => $this->section->description,
            'created_at' => now()->toDateTimeString(),
        ];
    }
} 