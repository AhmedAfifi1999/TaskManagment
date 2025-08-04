<?php

namespace App\Observers;

use App\Mail\TaskNotification;
use App\Models\Task;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class TaskObserver
{
    /**
     * Handle the Task "created" event.
     */
    public function created(Task $task): void
    {
        try {
            if (
                isset($task->user->email) &&
                filter_var($task->user->email, FILTER_VALIDATE_EMAIL)
            ) {
                // dd($task);

                Mail::to($task->user->email)
                    ->send(new TaskNotification($task, 'created'));
            } else {
                Log::warning('User or email missing for task: ' . $task->id);
            }
        } catch (\Exception $e) {
            Log::error('Failed to send task creation notification: ' . $e->getMessage());
        }
    }

    public function updated(Task $task): void
    {
        try {
            // فقط إذا تغيرت الحالة إلى مكتملة
            if ($task->isDirty('status') && $task->status === 'completed') {

                $emails = [];

                // التحقق من وجود علاقة projectTask وبريد المدير
                if (
                    isset($task->projectTask->email) &&
                    filter_var($task->projectTask->email, FILTER_VALIDATE_EMAIL)
                ) {
                    $emails['to'] = $task->projectTask->email;
                }

                // التحقق من وجود علاقة user وبريد المستخدم
                if (
                    isset($task->user->email) &&
                    filter_var($task->user->email, FILTER_VALIDATE_EMAIL)
                ) {
                    $emails['cc'] = $task->user->email;
                }

                // فقط إذا كان هناك بريد واحد على الأقل
                if (!empty($emails)) {
                    $mail = Mail::to($emails['to'] ?? null);

                    if (isset($emails['cc'])) {
                        $mail->cc($emails['cc']);
                    }

                    $mail->send(new TaskNotification($task, 'completed'));
                }
            }
        } catch (\Exception $e) {
            Log::error('Failed to send task completion notification: ' . $e->getMessage());
        }
    }
    /**
     * Handle the Task "deleted" event.
     */
    public function deleted(Task $task): void
    {
        //
    }

    /**
     * Handle the Task "restored" event.
     */
    public function restored(Task $task): void
    {
        //
    }

    /**
     * Handle the Task "force deleted" event.
     */
    public function forceDeleted(Task $task): void
    {
        //
    }
}
