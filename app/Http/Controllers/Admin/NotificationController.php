<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;

class NotificationController extends Controller
{
    //
    public function index(Request $request)
    {
        $notifications = auth()
            ->user()
            ->notifications()
            ->latest()
            ->paginate(15);

        return view('admin.notifications.index', compact('notifications'));
    }

    /**
     * تحديد إشعار كمقروء ثم الانتقال للرابط إن وجد.
     */
    public function show(DatabaseNotification $notification)
    {
        abort_if($notification->notifiable_id != auth()->id(), 403);

        if (is_null($notification->read_at)) {
            $notification->markAsRead();
        }

        $data = $notification->data;

        return redirect($data['url'] ?? route('admin.notifications.index'));
    }

    /**
     * تحديد جميع الإشعارات كمقروءة.
     */
    public function markAllAsRead()
    {
        auth()->user()
            ->unreadNotifications
            ->markAsRead();

        return back()->with('success', 'تم تحديد جميع الإشعارات كمقروءة.');
    }

    /**
     * حذف إشعار.
     */
    public function destroy(DatabaseNotification $notification)
    {
        abort_if($notification->notifiable_id != auth()->id(), 403);

        $notification->delete();

        return back()->with('success', 'تم حذف الإشعار.');
    }

    public function markAsRead(DatabaseNotification $notification)
    {
        abort_if($notification->notifiable_id != auth()->id(), 403);

        if (is_null($notification->read_at)) {
            $notification->markAsRead();
        }

        return back()->with('success', 'تم تحديد الإشعار كمقروء.');
    }
}
