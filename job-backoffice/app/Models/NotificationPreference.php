<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationPreference extends Model
{
    protected $fillable = [
        'user_id',
        'email_task_assigned',
        'email_task_completed',
        'email_leave_requested',
        'email_leave_approval',
        'email_weekly_report',
        'email_activity_digest',
        'in_app_notifications',
    ];

    protected $casts = [
        'email_task_assigned' => 'boolean',
        'email_task_completed' => 'boolean',
        'email_leave_requested' => 'boolean',
        'email_leave_approval' => 'boolean',
        'email_weekly_report' => 'boolean',
        'email_activity_digest' => 'boolean',
        'in_app_notifications' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get or create preferences for user with defaults
     */
    public static function forUser(User $user): self
    {
        return self::firstOrCreate(
            ['user_id' => $user->id],
            [
                'email_task_assigned' => true,
                'email_task_completed' => true,
                'email_leave_requested' => true,
                'email_leave_approval' => true,
                'email_weekly_report' => true,
                'email_activity_digest' => false,
                'in_app_notifications' => true,
            ]
        );
    }

    /**
     * Check if user wants email for specific notification type
     */
    public function wantsEmail(string $type): bool
    {
        $key = "email_$type";
        return $this->$key ?? false;
    }
}
