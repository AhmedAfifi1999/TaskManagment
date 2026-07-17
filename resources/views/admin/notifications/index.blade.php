@extends('admin.layouts.app')

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">

        <div class="card shadow-sm">

            <!-- Header -->
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <h4 class="mb-1">🔔 الإشعارات</h4>
                    <small class="text-muted">
                        إجمالي الإشعارات: {{ $notifications->total() }}
                    </small>
                </div>

                <div>
                    <form action="{{ route('admin.notifications.markAllAsRead') }}" method="POST" class="d-inline">
                        @csrf

                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="ti ti-checks me-1"></i>
                            تحديد الكل كمقروء
                        </button>
                    </form>
                </div>
            </div>

            <!-- Notifications -->
            <div class="card-body notifications-body">

                @forelse($notifications as $notification)
                    @php
                        $data = $notification->data;
                        $isUnread = is_null($notification->read_at);
                    @endphp

                    <div class="notification-item {{ $isUnread ? 'unread' : '' }}">

                        <div class="notification-icon">
                            <i class="{{ $data['icon'] ?? 'ti ti-bell' }}"></i>
                        </div>

                        <div class="notification-content">

                            <div class="d-flex justify-content-between flex-wrap">

                                <div class="flex-grow-1">

                                    <h6 class="mb-1 fw-bold">
                                        {{ $data['title'] ?? 'إشعار جديد' }}
                                    </h6>

                                    <p class="mb-1 text-muted">
                                        {{ $data['message'] ?? '' }}
                                    </p>

                                    <small class="text-muted">
                                        <i class="ti ti-clock me-1"></i>
                                        {{ $notification->created_at->diffForHumans() }}
                                    </small>

                                </div>

                                <div class="notification-actions">

                                    @if (!empty($data['url']))
                                        <a href="{{ $data['url'] }}" class="btn btn-outline-primary btn-sm" title="فتح">
                                            <i class="ti ti-eye"></i>
                                        </a>
                                    @endif

                                    @if ($isUnread)
                                        <form action="{{ route('admin.notifications.read', $notification->id) }}"
                                            method="POST" class="d-inline">
                                            @csrf

                                            <button class="btn btn-outline-success btn-sm" title="تحديد كمقروء">
                                                <i class="ti ti-check"></i>
                                            </button>

                                        </form>
                                    @endif

                                    <form action="{{ route('admin.notifications.destroy', $notification->id) }}"
                                        method="POST" class="d-inline">

                                        @csrf
                                        @method('DELETE')

                                        <button class="btn btn-outline-danger btn-sm" title="حذف">
                                            <i class="ti ti-trash"></i>
                                        </button>

                                    </form>

                                </div>

                            </div>

                        </div>

                    </div>

                    @if (!$loop->last)
                        <hr class="notification-divider">
                    @endif

                @empty

                    <div class="text-center py-5">

                        <i class="ti ti-bell-off text-secondary" style="font-size:70px"></i>

                        <h5 class="mt-3">
                            لا توجد إشعارات
                        </h5>

                        <p class="text-muted mb-0">
                            ستظهر جميع الإشعارات هنا عند توفرها.
                        </p>

                    </div>
                @endforelse

            </div>
            @if ($notifications->hasPages())
                <div class="card-footer">
                    {{ $notifications->links() }}
                </div>
            @endif

        </div>

    </div>
@endsection


@push('styles')
    <style>
        .notifications-body {
            padding: 35px !important;
        }

        /* Card */
        .notification-item {
            display: flex;
            align-items: flex-start;
            gap: 18px;

            padding: 22px;

            margin-bottom: 18px;

            background: #fff;

            border: 1px solid #dee2e6;
            border-radius: 14px;

            transition: .25s;
        }

        /* Hover */
        .notification-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 18px rgba(0, 0, 0, .08);
        }

        /* Read */
        .notification-item:not(.unread):hover {
            background: #f8f9fa;
        }

        /* Unread */
        .notification-item.unread {
            background: #e9ecef;
            border-right: 5px solid #6c757d;
            border-color: #ced4da;
        }

        .notification-item.unread:hover {
            background: #dee2e6;
        }

        /* Icon */
        .notification-icon {
            width: 54px;
            height: 54px;

            border-radius: 50%;

            display: flex;
            align-items: center;
            justify-content: center;

            background: #eef2ff;
            color: #696cff;

            font-size: 22px;

            flex-shrink: 0;
        }

        /* Content */
        .notification-content {
            flex: 1;
        }

        /* Title */
        .notification-content h6 {
            color: #212529;
            margin-bottom: 6px;
        }

        /* Message */
        .notification-content p {
            color: #6c757d;
            margin-bottom: 8px;
        }

        /* Time */
        .notification-content small {
            color: #868e96;
        }

        /* Actions */
        .notification-actions {
            display: flex;
            gap: 8px;
            align-items: center;
        }

        .notification-actions .btn {
            width: 38px;
            height: 38px;

            padding: 0;

            display: flex;
            align-items: center;
            justify-content: center;

            border-radius: 10px;
        }

        /* Divider */
        .notification-divider {
            margin: 15px 0;
            opacity: .15;
        }

        .notifications-body>.notification-item.unread {
            background: #f1f1f1 !important;
        }
    </style>
@endpush
