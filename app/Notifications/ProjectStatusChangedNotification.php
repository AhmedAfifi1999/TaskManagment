<?php

namespace App\Notifications;

use App\Models\Project;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ProjectStatusChangedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Project $project,
        public User $changedBy,
        public string $status
    ) {
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
    $statuses = [
        'not_started' => 'لم يبدأ',
        'in_progress' => 'قيد التنفيذ',
        'completed' => 'مكتمل',
    ];

    $statusKey = strtolower($this->status);

    $status = $statuses[$statusKey] ?? $this->status;

    return [
        'title' => 'تم تحديث حالة المشروع',
        'message' => "{$this->changedBy->name} قام بتغيير حالة المشروع \"{$this->project->name}\" إلى \"{$status}\".",
        'project_id' => $this->project->id,
        'status' => $status,
        'changed_by' => $this->changedBy->id,
        'changed_by_name' => $this->changedBy->name,
    ];
}
}
