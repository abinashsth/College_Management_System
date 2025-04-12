<?php

namespace App\Notifications;

use App\Models\ClassroomAllocation;
use App\Models\Section;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ClassroomAllocated extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * The classroom allocation instance.
     *
     * @var \App\Models\ClassroomAllocation
     */
    protected $allocation;

    /**
     * The section instance.
     *
     * @var \App\Models\Section
     */
    protected $section;

    /**
     * Create a new notification instance.
     *
     * @param  \App\Models\ClassroomAllocation  $allocation
     * @param  \App\Models\Section  $section
     * @return void
     */
    public function __construct(ClassroomAllocation $allocation, Section $section)
    {
        $this->allocation = $allocation;
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
        $building = $this->allocation->building ?: 'Main Building';
        $roomInfo = "Room {$this->allocation->room_number}, Floor {$this->allocation->floor}, {$building}";
        
        $startTime = $this->allocation->start_time ? $this->allocation->start_time->format('h:i A') : 'N/A';
        $endTime = $this->allocation->end_time ? $this->allocation->end_time->format('h:i A') : 'N/A';
        $day = ucfirst($this->allocation->day ?: 'N/A');
        
        return (new MailMessage)
                    ->subject("Classroom Allocated: {$this->section->section_name}")
                    ->greeting("Hello {$notifiable->name},")
                    ->line("A classroom has been allocated for {$this->section->section_name} of {$className}.")
                    ->line("Room Details: {$roomInfo}")
                    ->line("Day: {$day}")
                    ->line("Time: {$startTime} - {$endTime}")
                    ->line("Room Type: " . ucfirst($this->allocation->type ?: 'Regular'))
                    ->line("Capacity: {$this->allocation->capacity}")
                    ->action('View Schedule', route('classroom-allocations.show', $this->allocation))
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
            'allocation_id' => $this->allocation->id,
            'room_number' => $this->allocation->room_number,
            'floor' => $this->allocation->floor,
            'building' => $this->allocation->building,
            'section_id' => $this->section->id,
            'section_name' => $this->section->section_name,
            'class_id' => $this->section->class_id,
            'class_name' => $this->section->class ? $this->section->class->class_name : null,
            'day' => $this->allocation->day,
            'start_time' => $this->allocation->start_time ? $this->allocation->start_time->format('H:i') : null,
            'end_time' => $this->allocation->end_time ? $this->allocation->end_time->format('H:i') : null,
            'academic_session_id' => $this->allocation->academic_session_id,
            'type' => $this->allocation->type,
            'capacity' => $this->allocation->capacity,
            'status' => $this->allocation->status,
            'allocated_at' => now()->toDateTimeString(),
        ];
    }
} 