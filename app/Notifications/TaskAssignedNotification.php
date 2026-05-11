<?php

namespace App\Notifications;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TaskAssignedNotification extends Notification
{
    use Queueable;

    public Task $task;

    public function __construct(Task $task)
    {
        $this->task = $task;
    }

    public function via($notifiable)
    {
        return ['database']; // نخزنها في DB
    }

     public function toArray(object $notifiable): array
    {
        return [
            'title' => 'تم تعيين مهمة جديدة',
            'message' => 'تم تعيين المهمة "' . $this->task->name . '" لك.',
            'task_id' => $this->task->id,
        ];
    }
}
