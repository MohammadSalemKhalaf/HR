<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends BaseApiController
{
    /**
     * Get notifications for the current user
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $notifications = $user->notifications()
                ->orderBy('created_at', 'desc')
                ->paginate(20);

            return $this->success('Notifications retrieved successfully.', $notifications);
        } catch (\Throwable $e) {
            report($e);
            return $this->error('Failed to retrieve notifications.', [], 500);
        }
    }

    /**
     * Mark a notification as read
     */
    public function markAsRead(Request $request, string $id): JsonResponse
    {
        try {
            $user = $request->user();
            $notification = $user->notifications()->find($id);

            if (!$notification) {
                return $this->error('Notification not found.', [], 404);
            }

            $notification->markAsRead();

            return $this->success('Notification marked as read.');
        } catch (\Throwable $e) {
            report($e);
            return $this->error('Failed to mark notification as read.', [], 500);
        }
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $user->unreadNotifications->markAsRead();

            return $this->success('All notifications marked as read.');
        } catch (\Throwable $e) {
            report($e);
            return $this->error('Failed to mark notifications as read.', [], 500);
        }
    }
}
