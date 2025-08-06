<?php

namespace App\Mail;

use App\Models\Project;
use App\Models\Setting;
use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TaskNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $task;
    public $notificationType;
    /**
     * Create a new message instance.
     */
    public function __construct(Task $task, $notificationType)
    {
        $this->task = $task;
        $this->notificationType = $notificationType;
    }
    public function build()
    {
        $project = Project::find($this->task->project_id); // تأكد من أن الحقل هو project_id وليس project
        $setting = Setting::first();
        // $companyName = config('app.name');
        $companyName = $setting->site_name;

        $subject = $this->notificationType === 'created'
            ? 'تم إنشاء مهمة جديدة: ' . $this->task->title
            : 'تم إكمال المهمة: ' . $this->task->title;

        $statusColor = $this->notificationType === 'created' ? '#2196F3' : '#4CAF50';
        $statusText = $this->notificationType === 'created' ? 'قيد التنفيذ' : 'مكتمل';

        // حل مشكلة التواريخ - التحقق من وجودها أولاً
        $startTime = $this->task->start_time ? $this->task->start_time : 'غير محدد';
        $endTime = $this->task->end_time ? $this->task->end_time : 'غير محدد';

        $content = '
    <!DOCTYPE html>
    <html dir="rtl">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>' . $subject . '</title>
    </head>
    <body style="font-family: Arial, sans-serif; margin: 0; padding: 0; background-color: #f5f5f5;">
        <div style="max-width: 600px; margin: 20px auto; background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
            <!-- Header -->
            <div style="background: ' . $statusColor . '; padding: 20px; text-align: center; color: white;">
                <h1 style="margin: 10px 0 0; font-size: 24px;">' . $companyName . '</h1>
            </div>
            
            <!-- Content -->
            <div style="padding: 25px;">
                <h2 style="color: #333; text-align: center; margin-top: 0;">' .
            ($this->notificationType === 'created' ? 'تم إنشاء مهمة جديدة' : 'تم إكمال المهمة') .
            '</h2>
                
                <div style="background: #f9f9f9; padding: 15px; border-radius: 5px; margin-bottom: 20px; border-left: 4px solid ' . $statusColor . ';">
                    <h3 style="margin-top: 0; color: #2c3e50;">' . $this->task->title . '</h3>
                    
                    <table style="width: 100%; border-collapse: collapse;">
                        <tr>
                            <td style="padding: 5px 0; width: 120px;"><strong>الحالة:</strong></td>
                            <td style="padding: 5px 0;">
                                <span style="background: ' . $statusColor . '; color: white; padding: 3px 10px; border-radius: 4px; font-size: 12px;">' . $statusText . '</span>
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 5px 0;"><strong>المشروع:</strong></td>
                            <td style="padding: 5px 0;">' . ($project ? $project->name : 'غير معروف') . '</td>
                        </tr>
                        <tr>
                            <td style="padding: 5px 0;"><strong>تاريخ الإنشاء:</strong></td>
                            <td style="padding: 5px 0;">' . $this->task->created_at->format('Y-m-d H:i') . '</td>
                        </tr>
                        <tr>
                            <td style="padding: 5px 0;"><strong>تاريخ البدء:</strong></td>
                            <td style="padding: 5px 0;">' . $startTime . '</td>
                        </tr>
                        <tr>
                            <td style="padding: 5px 0;"><strong>تاريخ الانتهاء:</strong></td>
                            <td style="padding: 5px 0;">' . $endTime . '</td>
                        </tr>
                    </table>
                </div>
                
                <div style="margin-bottom: 25px;">
                    <h3 style="color: #2c3e50; border-bottom: 1px solid #eee; padding-bottom: 5px; margin-bottom: 10px;">وصف المهمة</h3>
                    <div style="line-height: 1.6;">' . nl2br(e($this->task->description)) . '</div>
                </div>
                
                <div style="text-align: center; margin-top: 30px;">
                    <a href="' . route('admin.projects.tasks.index', $this->task->project) . '" style="background: ' . $statusColor . '; color: white; padding: 12px 25px; text-decoration: none; border-radius: 4px; display: inline-block; font-weight: bold;">
                        عرض المهام في المشروع
                    </a>
                </div>
            </div>
            
            <!-- Footer -->
            <div style="background: #f5f5f5; padding: 15px; text-align: center; font-size: 12px; color: #777; border-top: 1px solid #e0e0e0;">
                <p style="margin: 0;">© ' . date('Y') . ' ' . $companyName . '. جميع الحقوق محفوظة.</p>
                <p style="margin: 5px 0 0; font-size: 11px;">
                    <a href="' . route('main') . '" style="color: #777; text-decoration: none;">الرئيسية</a> | 
                    <a href="' . route('main') . '" style="color: #777; text-decoration: none;">اتصل بنا</a>
                </p>
            </div>
        </div>
    </body>
    </html>';

        return $this->subject($subject)
            ->html($content);
    }
    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Task Notification',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'view.name',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
