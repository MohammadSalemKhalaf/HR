<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Support\Arr;

class ActivityLogService
{
    /**
     * Log an activity.
     *
     * @param string|null $companyId
     * @param string|null $actorUserId
     * @param string $action
     * @param string|null $description
     * @param mixed $subject
     * @param array $metadata
     * @return ActivityLog
     */
    public function log(?string $companyId, ?string $actorUserId, string $action, ?string $description = null, $subject = null, array $metadata = []): ActivityLog
    {
        $subjectType = null;
        $subjectId = null;

        if ($subject) {
            if (is_object($subject) && method_exists($subject, 'getKey')) {
                $subjectType = get_class($subject);
                $subjectId = (string) $subject->getKey();
            } elseif (is_array($subject)) {
                $subjectType = Arr::get($subject, '_type');
                $subjectId = Arr::get($subject, 'id');
            }
        }

        $log = ActivityLog::create([
            'company_id' => $companyId,
            'actor_user_id' => $actorUserId,
            'subject_type' => $subjectType,
            'subject_id' => $subjectId,
            'action' => $action,
            'description' => $description,
            'metadata' => $metadata,
        ]);

        return $log;
    }
}
