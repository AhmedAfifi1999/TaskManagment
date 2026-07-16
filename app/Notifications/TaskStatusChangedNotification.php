<?php

namespace App\Notifications;

use App\Models\Task;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TaskStatusChangedNotification extends Notification
{
    use Queueable;

    public Task $task;
    public User $changedBy;

    /**
     * Create a new notification instance.
     */
    public function __construct(Task $task, User $changedBy)
    {
        $this->task = $task;
        $this->changedBy = $changedBy;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'تم تحديث حالة المهمة',
            'message' => $this->changedBy->name .
                ' قام بتغيير حالة المهمة "' .
                $this->task->name .
                '" إلى "' .
                $this->task->status .
                '".',
            'task_id' => $this->task->id,
            'status' => $this->task->status,
            'changed_by' => $this->changedBy->id,
        ];
    }
}