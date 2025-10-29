<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Admin\ClassModel;

class ClassUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $class;
    public $updateType;
    public $updatedBy;

    /**
     * Create a new event instance.
     */
    public function __construct(ClassModel $class, $updateType = 'update', $updatedBy = null)
    {
        $this->class = $class;
        $this->updateType = $updateType;
        $this->updatedBy = $updatedBy;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('classes'), // Public channel for all class updates
            new PrivateChannel('admin.classes'), // Private channel for admin-only updates
        ];
    }

    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith(): array
    {
        return [
            'class' => [
                'id' => $this->class->id,
                'student_name' => $this->class->student_name,
                'teacher_id' => $this->class->teacher_id,
                'teacher_name' => $this->class->teacher ? $this->class->teacher->name : null,
                'class_type' => $this->class->class_type,
                'schedule' => $this->class->schedule,
                'time' => $this->class->time,
                'status' => $this->class->status,
                'notes' => $this->class->notes,
                'created_at' => $this->class->created_at,
                'updated_at' => $this->class->updated_at
            ],
            'update_type' => $this->updateType,
            'updated_by' => $this->updatedBy ? [
                'id' => $this->updatedBy->id,
                'name' => $this->updatedBy->name,
                'role' => $this->updatedBy->role instanceof \App\Models\UserRole ? $this->updatedBy->role->value : $this->updatedBy->role
            ] : null,
            'timestamp' => now()->toISOString()
        ];
    }

    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastAs(): string
    {
        return 'class.updated';
    }
}